<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\StoreProductRequest;
use App\Http\Requests\Update\UpdateProductRequest;
use App\Http\Resources\Api\ProductsResource;
use App\Models\Category;
use App\Models\Product;
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
}

