<?php

namespace App\Http\Controllers;

use App\Models\ProductSet;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /*
     * Create a new product within a ProductSet
     */
    public function store(Request $request): ProductResource
    {
        $data = $request->validate([
            'product_set_id' => 'required|exists:product_sets,id',
            'sku' => 'required|string|unique:products,sku',
            'name' => 'required|string|max:40',
            'type' => 'required|in:device,service',
            'condition' => 'required|in:new,used,refurbished',
            'description_title' => 'required|string|max:255',
            'description_text' => 'required|string',
            'price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'published' => 'required|boolean',
        ]);

        $productSet = ProductSet::findOrFail($data['product_set_id']);
        $product = $this->productRepository->create(array_merge($data, ['product_set_id' => $productSet->id]));

        return new ProductResource($product);
    }

    /*
     * Update an existing product
     */
    public function update(Request $request, $id): ProductResource
    {
        $product = $this->productRepository->findById($id);
        $data = $request->validate([
            'sku' => 'sometimes|string|unique:products,sku,' . $product->id,
            'name' => 'sometimes|string|max:40',
            'type' => 'sometimes|in:device,service',
            'condition' => 'sometimes|in:new,used,refurbished',
            'description_title' => 'sometimes|string|max:255',
            'description_text' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'published' => 'sometimes|boolean',
        ]);
        $updatedProduct = $this->productRepository->update($product, $data);

        return new ProductResource($updatedProduct);
    }

    /*
     * Delete an existing product
     */
    public function destroy($id): JsonResponse
    {
        $product = $this->productRepository->findById($id);

        $this->productRepository->delete($product);

        return response()->json(null, 204);
    }
}
