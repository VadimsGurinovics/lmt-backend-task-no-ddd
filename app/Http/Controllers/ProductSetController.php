<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductSetResource;
use App\Models\ProductSet;
use App\Repositories\ProductSetRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductSetController extends Controller
{
    protected ProductSetRepository $productSetRepository;

    public function __construct(ProductSetRepository $productSetRepository)
    {
        $this->productSetRepository = $productSetRepository;
    }

    /*
     * Return all published product sets with at least one published product
     */
    public function index(): AnonymousResourceCollection
    {
        $productSets = ProductSet::where('published', true)
            ->whereHas('products', function ($query) {
                $query->where('published', true);
            })
            ->with('products')
            ->get();

        return ProductSetResource::collection($productSets);
    }

    /*
     * Create a new product set
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:60',
            'published' => 'boolean',
            'products' => 'required|array',
            'products.*.name' => 'required|string|max:40',
            'products.*.sku' => 'required|string|unique:products,sku',
            'products.*.type' => 'required|in:device,service',
            'products.*.condition' => 'required|in:new,used,refurbished',
            'products.*.description_title' => 'required|string|max:255',
            'products.*.description_text' => 'required|string',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.wholesale_price' => 'nullable|numeric|min:0',
            'products.*.published' => 'required|boolean',
        ]);

        $productSet = $this->productSetRepository->create($data);

        return (new ProductSetResource($productSet))
            ->response()
            ->setStatusCode(201);
    }

    /*
     * Update a product set by ID
     */
    public function update(Request $request, $id): ProductSetResource
    {
        $productSet = $this->productSetRepository->findById($id);
        $data = $request->validate([
            'name' => 'sometimes|string|max:60',
            'published' => 'boolean',
            'products' => 'array',
            'products.*.id' => 'nullable|exists:products,id',
            'products.*.name' => 'required|string|max:40',
            'products.*.sku' => 'required|string',
            'products.*.type' => 'required|in:device,service',
            'products.*.condition' => 'required|in:new,used,refurbished',
            'products.*.description_title' => 'required|string|max:255',
            'products.*.description_text' => 'required|string',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.wholesale_price' => 'nullable|numeric|min:0',
            'products.*.published' => 'required|boolean',
        ]);
        $productSet = $this->productSetRepository->update($productSet, $data);

        return new ProductSetResource($productSet);
    }

    /*
     * Delete a product set by ID
     */
    public function destroy($id): JsonResponse
    {
        $productSet = $this->productSetRepository->findById($id);

        $this->productSetRepository->delete($productSet);

        return response()->json(null, 204);
    }
}
