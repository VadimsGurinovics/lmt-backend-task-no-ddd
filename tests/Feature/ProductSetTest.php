<?php

namespace Tests\Feature;

use App\Models\ProductSet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductSetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a new ProductSet.
     */
    public function test_can_create_product_set(): void
    {
        // Send a POST request to create a product set
        $response = $this->postJson('/api/product-sets', [
            'name' => 'New Product Set',
            'products' => [
                [
                    'name' => 'Product 1',
                    'sku' => 'PROD1',
                    'type' => 'device',
                    'condition' => 'new',
                    'description_title' => 'Product 1 Title',
                    'description_text' => 'Product 1 Description',
                    'price' => 100.50,
                    'wholesale_price' => 70.00,
                    'published' => true,
                ],
            ],
        ]);
        // Assert the response status
        $response->assertStatus(201);
        // Assert that the ProductSet exists in the database
        $this->assertDatabaseHas('product_sets', ['name' => 'New Product Set']);
        // Assert that the products were created
        $this->assertDatabaseHas('products', ['name' => 'Product 1']);
    }

    /**
     * Test validation for creating a ProductSet without any products.
     */
    public function test_product_set_must_have_at_least_one_product(): void
    {
        // Send a POST request to create a product set without any products
        $response = $this->postJson('/api/product-sets', [
            'name' => 'Invalid Product Set',
            'products' => [],
        ]);

        // Assert that the response returns a 422 Unprocessable Entity (validation error)
        $response->assertStatus(422)
            ->assertJson([
                'messages' => [
                    'products' => ['The products field is required.'],
                ],
            ]);
    }

    /**
     * Test that product set creation fails when required fields are missing.
     */
    public function test_create_product_set_fails_with_missing_name(): void
    {
        // Send request with missing 'name' field
        $response = $this->postJson('/api/product-sets', [
            'products' => [
                [
                    'name' => 'Product 1',
                    'sku' => 'PROD1',
                    'type' => 'device',
                    'condition' => 'new',
                    'description_title' => 'Product 1 Title',
                    'description_text' => 'Product 1 Description',
                    'price' => 100.50,
                    'wholesale_price' => 70.00,
                    'published' => true,
                ],
            ],
        ]);

        // Assert validation errors for the 'name' field
        $response->assertStatus(422)
            ->assertJson([
                'messages' => [
                    'name' => ['The name field is required.'],
                ],
            ]);
    }

    /**
     * Test fetching published product sets via public API.
     */
    public function test_can_fetch_published_product_sets(): void
    {
        // Create a product set with one published product
        $productSet = ProductSet::create([
            'name' => 'Published Set',
            'slug' => Str::slug('Published Set'),
            'published' => true,
        ]);

        // Add a published product to the set
        $productSet->products()->create([
            'name' => 'Published Product',
            'sku' => 'PROD3',
            'type' => 'device',
            'condition' => 'new',
            'description_title' => 'Published Product Title',
            'description_text' => 'Published Product Description',
            'price' => 200.00,
            'wholesale_price' => 150.00,
            'published' => true,  // Ensure product is published
        ]);

        // Fetch product sets via the public API
        $response = $this->getJson('/api/product-sets');

        // Assert that the product set is returned in the response within the "data" key
        $response
            ->assertStatus(200)
            ->assertJsonFragment(['name' => 'Published Set'])
            ->assertJsonPath('data.0.name', 'Published Set');  // Ensure the 'name' exists within the 'data' array
    }
}
