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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    use HttpResponses;

    public function index(Request $request)
    {
        // filters parameters from the request parameters
        $filters = $request->only(['search', 'category', 'min_price', 'in_stock', 'warehouse', 'sort_by']);
        $query = Product::with('category.parent');

        $query = $this->advancedFiltering($query, $filters);
        $products = $query->paginate($request->get('per_page', 10));

        $products = Product::with('category.parent')->paginate($request->get('per_page', 10));
        return ProductsResource::collection($products);
    }

    protected function advancedFiltering($query, $filters)
    {
        // fill text search
        $query->when(isset($filters['search']), function (Builder $query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        })
            // category filter
            ->when($filters['category'] ?? null, fn($q, $categoryId) =>
                $q->where('category_id', $categoryId))

            // Min price
            ->when($filters['min_price'] ?? null, fn($q, $min) =>
                $q->where('price', '>=', $min))

            // Max price
            ->when($filters['max_price'] ?? null, fn($q, $max) =>
                $q->where('price', '<=', $max))

            // In-stock products only
            ->when(isset($filters['in_stock']) && filter_var($filters['in_stock'], FILTER_VALIDATE_BOOLEAN), function ($q) {
                $q->whereHas('stock', fn($q) => $q->where('quantity', '>', 0));
            })

            ->when($filters['warehouse'] ?? null, function ($q, $warehouseId) {
                $q->whereHas('stock', fn($q) =>
                    $q->where('warehouse_id', $warehouseId));
            });

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $allowedSorts = ['name', 'price', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query;
    }

    public function store(StoreProductRequest $request)
    {
        // validate the request
        $validated = $request->validated();

        try {
            // Check if the category exists and get its ID
            $category_id = Category::where('name', $validated['category'])->first()?->id ?? null;

            // If category is required and not found, return an error
            if ($validated['category'] && !isset($category_id)) {
                return $this->error(null, 'Category not found', 422);
            }

            // Create the product
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
            // Log the error for debugging
            return $this->error(null, 'Failed to create product', 500);
        }
    }

    public function show(Product $product)
    {
        // Load the product with its category and parent category
        $product->load('category.parent');
        return new ProductsResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        // validate the request
        $validated = $request->validated();

        try {
            // Check if the category exists and get its ID
            if (isset($validated['category'])) {
                $category_id = Category::where('name', $validated['category'])->first()?->id ?? null;
                if ($validated['category'] && !isset($category_id)) {
                    return $this->error(null, 'Category not found', 422);
                }

                $validated['category_id'] = $category_id;
            }

            // Update the product
            $product->update($validated);

            return $this->success(null, 'Product updated successfully');

        } catch (\Throwable $th) {
            return $this->error(null, 'Failed to update product', 500);
        }
    }

    public function destroy(Product $product)
    {
        // delete the product
        $count = $product->delete();

        if ($count == 0) {
            return $this->error(null, 'Something went wrong. Please try again later.');
        }

        return $this->success(null, 'Product deleted successfully!');
    }

    public function stock(Product $product)
    {
        // Load the product with its stock and warehouses
        $product->load([
            'warehouses.stock' => function ($query) use ($product) {
                $query->where('product_id', $product->id);
            }
        ]);

        return new ProductStockResource($product);
    }

    public function reserve(Request $request, Product $product)
    {
        // Validate the request
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        try {
            // Check if the product has stock in the specified warehouse
            $stock = $product->stock()->where('warehouse_id', $validated['warehouse_id'])->first();

            // If no stock found, return an error
            if (!$stock) {
                return $this->error(null, 'No stock record found for this warehouse', 404);
            }

            // Calculate reserved quantity
            $reservedQty = $product->reservations()
                ->where('stocks.warehouse_id', $validated['warehouse_id']) // ✅ scoped to warehouse
                ->where('stock_reservations.status', 'reserved')
                ->where('stock_reservations.expires_at', '>', now())
                ->sum('stock_reservations.quantity');

            // Calculate available quantity
            $availableQuantity = $stock->quantity - $reservedQty;

            // Check if the requested quantity exceeds available stock
            if ($availableQuantity < $validated['quantity']) {
                return $this->error(null, "Only {$availableQuantity} units are available for reservation in this warehouse", 422);
            }

            // Check if the product has stock in the specified warehouse
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
        // Validate the request
        $validated = $request->validate([
            'reservation_id' => 'required|integer|exists:stock_reservations,id',
        ]);

        try {
            // Find the reservation for the product
            $reservation = StockReservation::where('id', $validated['reservation_id'])
                ->whereHas('stock', function ($query) use ($product) {
                    $query->where('product_id', $product->id);
                })
                ->where('status', 'reserved')
                ->first();

            // If no reservation found, return an error
            if (!isset($reservation)) {
                return $this->error(null, 'Active reservation not found for this product.', 404);
            }

            // Check if the reservation has already expired
            $reservation->update(['status' => 'released']);

            return $this->success(null, 'Reservation released.', 200);

        } catch (\Throwable $th) {
            return $this->error(null, $th->getMessage(), 500);
        }
    }

    public function lowStock()
    {
        // Fetch all products with their stock and reservations
        $products = Product::with([
            'stock',
            'reservations' => function ($q) {
                $q->where('status', 'reserved')->where('expires_at', '>', now());
            }
        ])->get();

        // Filter products with low stock
        $lowStock = $products->filter(fn($p) => $p->available_quantity < $p->reorder_level)->values();

        if ($lowStock->isEmpty()) {
            return $this->success(null, 'No products available with low quantity', 201);
        }

        return LowStockProductResource::collection($lowStock);
    }
}

