<?php

namespace App\Repositories;

use App\Models\ProductSet;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductSetRepository
{
    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function create(array $data): ProductSet
    {
        // Business rule: A product set must have at least one product
        if (empty($data['products']) || count($data['products']) == 0) {
            throw new Exception("ProductSet must contain at least one product.");
        }

        $validator = Validator::make($data, [
            'name' => 'required|string|max:60',
            'products' => 'required|array',
            'products.*.name' => 'required|string|max:40',
            'products.*.sku' => 'required|string',
            'products.*.price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return DB::transaction(function () use ($data) {
            $productSet = new ProductSet();
            $productSet->name = $data['name'];
            $productSet->slug = Str::slug($data['name']);
            $isPublished = $this->hasPublishedProduct($data['products']);
            $productSet->published = $isPublished ?? false;

            $productSet->save();

            foreach ($data['products'] as $productData) {
                $productSet->products()->create($productData);
            }

            return $productSet;
        });
    }

    /**
     * @throws Exception
     */
    public function update(ProductSet $productSet, array $data): ProductSet
    {
        return DB::transaction(function () use ($productSet, $data) {
            $productSet->name = $data['name'] ?? $productSet->name;
            $productSet->slug = Str::slug($data['name'] ?? $productSet->name);
            $isPublished = $this->hasPublishedProduct($data['products'] ?? []);
            $productSet->published = $isPublished ?? $productSet->published;

            $productSet->save();

            if (!empty($data['products'])) {
                $this->updateProducts($productSet, $data['products']);
            }

            return $productSet->load('products');
        });
    }

    public function delete(ProductSet $productSet): bool
    {
        return $productSet->delete();
    }

    public function findById($id): ProductSet
    {
        return ProductSet::with('products')->findOrFail($id);
    }

    /**
     * @throws Exception
     */
    protected function updateProducts(ProductSet $productSet, array $productsData): ProductSet
    {
        $existingProductIds = $productSet->products()->pluck('id')->toArray();

        foreach ($productsData as $productData) {
            try {
                if (isset($productData['id']) && in_array($productData['id'], $existingProductIds)) {
                    $this->updateExistingProduct($productSet, $productData);

                    $existingProductIds = array_diff($existingProductIds, [$productData['id']]);
                } else {
                    $this->createNewProduct($productSet, $productData);
                }
            } catch (Exception $e) {
                Log::error('Failed to update/create product: ' . $e->getMessage());
                throw new Exception('Failed to update/create product: ' . $e->getMessage());
            }
        }

        return $productSet;
    }

    protected function hasPublishedProduct(array $products): bool
    {
        foreach ($products as $productData) {
            if (isset($productData['published']) && $productData['published'] === true) {
                return true;
            }
        }
        return false;
    }

    protected function updateExistingProduct(ProductSet $productSet, array $productData): void
    {
        $product = $productSet->products()->find($productData['id']);

        if ($product) {
            $product->update($productData);
        } else {
            Log::error('Product with ID ' . $productData['id'] . ' not found for updating.');
        }
    }

    protected function createNewProduct(ProductSet $productSet, array $productData): void
    {
        $productSet->products()->create($productData);
    }
}
