<?php

namespace Tests\Unit;

use App\Models\ProductSet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if a product can be created
     */
    public function test_can_create_product(): void
    {
        // Create a product set for the product
        $productSet = ProductSet::factory()->create();
        // Simulate a POST request to create a product
        $response = $this->postJson('/api/products', [
            'product_set_id' => $productSet->id,
            'sku' => '12345',
            'name' => 'Test Product',
            'type' => 'device',
            'condition' => 'new',
            'description_title' => 'Test Description',
            'description_text' => 'This is a test description for the product.',
            'price' => 99.99,
            'wholesale_price' => 79.99,
            'published' => true,
        ]);

        // Assert the request was successful (HTTP 201)
        $response->assertStatus(201);

        // Assert that the product was successfully created in the database
        $this->assertDatabaseHas('products', [
            'sku' => '12345',
            'name' => 'Test Product',
        ]);
    }
}


