<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class HU004Test extends TestCase
{
    use RefreshDatabase;

     public function setUp(): void
     {
         parent::setUp();
         Artisan::call('db:seed');
     }
    public function test_eliminar_producto_existente(): void
    {
        $tokenAdmin = $this->post('/api/login', [
            "email" => "admin@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenAdmin];

        $product = Product::create([
            "name" => "Test Product",
            "description" => "Test product description",
            "available" => true,
            "image_path" => "test_image.png",
            "price" => 1000
        ]);

        $response = $this->withHeaders($headers)
            ->json('DELETE', "/api/products/{$product->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_mostrar_mensaje_error_producto_no_existe(): void
    {
        $tokenAdmin = $this->post('/api/login', [
            "email" => "admin@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenAdmin];

        $response = $this->withHeaders($headers)
            ->json('DELETE', "/api/products/9999");

        $response->assertStatus(204);
    }

    public function test_mostrar_mensaje_error_no_autorizado_eliminar(): void
    {
        $tokenClient = $this->post('/api/login', [
            "email" => "cliente@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenClient];

        $product = Product::create([
            "name" => "Test Product",
            "description" => "Test product description",
            "available" => true,
            "image_path" => "test_image.png",
            "price" => 1000
        ]);

        $response = $this->withHeaders($headers)
            ->json('DELETE', "/api/products/{$product->id}");

        $response->assertStatus(403);
    }
}
