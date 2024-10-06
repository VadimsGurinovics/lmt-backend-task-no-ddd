<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product;
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    public function findById($id): Product
    {
        return Product::findOrFail($id);
    }
}
