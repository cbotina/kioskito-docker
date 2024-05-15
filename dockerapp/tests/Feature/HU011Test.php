<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class HU011Test extends TestCase
{
    use RefreshDatabase;

     public function setUp(): void
     {
         parent::setUp();
         Artisan::call('db:seed');
     }
    public function test_mostrar_lista_productos_disponibles(): void {
        $tokenClient = $this->post('/api/login', [
            "email" => "cliente@gmail.com",
            "password" => "12345678",
        ])->json('token');

        // Set the JWT token in the request headers
        $headers = ['Authorization' => 'Bearer ' . $tokenClient];

        // Create some available and unavailable products
        $availableProduct1 = Product::factory()->create(['available' => true]);
        $availableProduct2 = Product::factory()->create(['available' => true]);
        $unavailableProduct = Product::factory()->create(['available' => false]);

        // Send a GET request to fetch all available products
        $response = $this->withHeaders($headers)
            ->get('/api/products/available');

        // Assert that the response is successful
        $response->assertStatus(200); // Assuming 200 is the status for successful request

        // Assert that the returned list of products only contains available products
        $response->assertJsonCount(2); // Expecting only available products
        $response->assertJsonFragment(['id' => $availableProduct1->id]);
        $response->assertJsonFragment(['id' => $availableProduct2->id]);
        $response->assertJsonMissing(['id' => $unavailableProduct->id]);
    }

    public function test_mostrar_lista_productos_vacia(): void {
        $tokenClient = $this->post('/api/login', [
            "email" => "cliente@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenClient];

        $response = $this->withHeaders($headers)
            ->get('/api/products/available');

        $response->assertStatus(200);

        $response->assertJson([]);
    }

}
