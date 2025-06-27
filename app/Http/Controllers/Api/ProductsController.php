<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Api\Store\StoreProductRequest;
use App\Http\Requests\Api\Update\UpdateProductRequest;
use App\Http\Resources\Api\LowStockProductResource;
use App\Http\Resources\Api\ProductsResource;

use App\Http\Resources\Api\ProductStockResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockReservation;
use App\Models\Warehouse;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    use HttpResponses;

    public function index()
    {
        $products = Product::with('category.parent')->paginate(10);
        return ProductsResource::collection($products);
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        try {
            $category_id = Category::where('name', $validated['category'])->first()?->id ?? null;

            if ($validated['category'] && !isset($category_id)) {
                return $this->error(null, 'Category not found', 422);
            }

            Product::create([
                'name' => $validated['name'],
                'sku' => $validated['sku'],
                'category_id' => $category_id,
                'description' => $validated['description'],
                'price' => $validated['price'],
                'tax_rate' => $validated['tax_rate'],
                'packaging' => $validated['packaging'],
                'unit' => $validated['unit'],
                'min_order_quantity' => $validated['min_order_quantity'],
                'reorder_level' => $validated['reorder_level'],
            ]);

            return $this->success(null, 'Product created successfully');
        } catch (\Throwable $th) {
            return $this->error(null, 'Failed to create product', 500);
        }
    }

    public function show(Product $product)
    {
        $product->load('category.parent');
        return new ProductsResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        try {
            if (isset($validated['category'])) {
                $category_id = Category::where('name', $validated['category'])->first()?->id ?? null;
                if ($validated['category'] && !isset($category_id)) {
                    return $this->error(null, 'Category not found', 422);
                }

                $validated['category_id'] = $category_id;
            }

            $product->update($validated);

            return $this->success(null, 'Product updated successfully');

        } catch (\Throwable $th) {
            return $this->error(null, 'Failed to update product', 500);
        }
    }

    public function destroy(Product $product)
    {
        $count = $product->delete();

        if ($count == 0) {
            return $this->error(null, 'Something went wrong. Please try again later.');
        }

        return $this->success(null, 'Product deleted successfully!');
    }

    public function stock(Product $product)
    {
        $product->load([
            'warehouses.stock' => function ($query) use ($product) {
                $query->where('product_id', $product->id);
            }
        ]);

        return new ProductStockResource($product);
    }

    public function reserve(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        try {
            $stock = $product->stock()->where('warehouse_id', $validated['warehouse_id'])->first();

            if (!$stock) {
                return $this->error(null, 'No stock record found for this warehouse', 404);
            }

            // Calculate reserved quantity
            $reservedQty = $product->reservations()
                ->where('stocks.warehouse_id', $validated['warehouse_id']) // ✅ scoped to warehouse
                ->where('stock_reservations.status', 'reserved')
                ->where('stock_reservations.expires_at', '>', now())
                ->sum('stock_reservations.quantity');

            $availableQuantity = $stock->quantity - $reservedQty;

            if ($availableQuantity < $validated['quantity']) {
                return $this->error(null, "Only {$availableQuantity} units are available for reservation in this warehouse", 422);
            }

            $stock = $product->stock()->where('warehouse_id', $validated['warehouse_id'])->first();

            // reserve the stock
            $stock->reservations()->create([
                "reserved_by" => 1,
                'quantity' => $validated['quantity'],
                'expires_at' => now()->addMinutes(30),
                'status' => 'reserved'
            ]);

            return $this->success(null, 'Stock reserved successfully', 201);
        } catch (\Throwable $th) {
            return $this->error(null, $th->getMessage(), 500);
        }
    }

    public function release(Request $request, Product $product)
    {

        $validated = $request->validate([
            'reservation_id' => 'required|integer|exists:stock_reservations,id',
        ]);

        try {

            $reservation = StockReservation::where('id', $validated['reservation_id'])
                ->where('product_id', $product->id)
                ->where('status', 'reserved')
                ->first();

            if (!isset($reservation)) {
                return $this->error(null, 'Active reservation not found for this product.', 404);
            }
            $reservation->update(['status' => 'released']);

            return $this->success(null, 'Reservation released.', 200);

        } catch (\Throwable $th) {
            return $this->error(null, $th->getMessage(), 500);
        }
    }

    public function lowStock()
    {
        $products = Product::with([
            'stock',
            'reservations' => function ($q) {
                $q->where('status', 'reserved')->where('expires_at', '>', now());
            }
        ])->get();


        $lowStock = $products->filter(fn($p) => $p->available_quantity < $p->reorder_level)->values();

        if ($lowStock->isEmpty()) {
            return $this->success(null, 'No products available with low quantity', 201);
        }

        return LowStockProductResource::collection($lowStock);
    }
}

