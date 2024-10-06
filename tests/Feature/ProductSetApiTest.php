<?php

namespace Tests\Feature;

use App\Models\ProductSet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSetApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the public route can retrieve product sets.
     */
    public function test_can_fetch_product_sets(): void
    {
        // Seed the database with product sets
        ProductSet::factory()->hasProducts(3)->count(3)->create();

        // Make a GET request to the public API
        $response = $this->getJson('/api/product-sets');

        // Assert that the response is 200 OK and contains product sets
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data'); // Assuming your API returns a 'data' key
    }

    /**
     * Test creating a new product set (protected by API key).
     */
    public function test_can_create_product_set_with_api_key(): void
    {
        // Provide a valid API key
        $headers = ['X-API-KEY' => config('app.api_key')];
        // Data for creating a product set
        $data = [
            'name' => 'New Product Set',
            'products' => [
                [
                    'name' => 'Product 1',
                    'sku' => 'SKU123',
                    'type' => 'device',
                    'condition' => 'new',
                    'description_title' => 'Title',
                    'description_text' => 'Description',
                    'price' => 99.99,
                    'wholesale_price' => 79.99,
                    'published' => true,
                ],
            ],
        ];
        // Make a POST request to create the product set
        $response = $this->postJson('/api/product-sets', $data, $headers);

        // Assert that the product set is created and response is 201 Created
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'products' => [
                        '*' => [
                            'id',
                            'slug',
                            'type',
                            'condition',
                            'description_title',
                            'description_text',
                            'price',
                            'published',
                        ],
                    ],
                ],
            ]);
    }

    /**
     * Test updating a product set (protected by API key).
     */
    public function test_can_update_product_set_with_api_key(): void
    {
        // Provide a valid API key
        $headers = ['X-API-KEY' => config('app.api_key')];
        // Create a product set
        $productSet = ProductSet::factory()->create([
            'name' => 'Old Product Set',
        ]);
        // Data for updating the product set
        $updateData = [
            'name' => 'Updated Product Set',
            'products' => [
                [
                    'name' => 'Updated Product',
                    'sku' => 'SKU124',
                    'type' => 'device',
                    'condition' => 'new',
                    'description_title' => 'New Title',
                    'description_text' => 'New Description',
                    'price' => 109.99,
                    'wholesale_price' => 89.99,
                    'published' => true,
                ],
            ],
        ];
        // Make a PUT request to update the product set
        $response = $this->putJson("/api/product-sets/{$productSet->id}", $updateData, $headers);

        // Assert the product set is updated
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Product Set']); // Ensure the name was updated
    }

    /**
     * Test deleting a product (protected by API key).
     */
    public function test_can_delete_product_with_api_key(): void
    {
        // Provide a valid API key
        $headers = ['X-API-KEY' => config('app.api_key')];
        // Create a product set with products
        $productSet = ProductSet::factory()->hasProducts(1)->create();
        // Grab the first product from the set
        $product = $productSet->products()->first();
        // Make a DELETE request to delete the product
        $response = $this->deleteJson("/api/products/{$product->id}", [], $headers);

        // Assert the product is deleted with a 204 No Content status
        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
