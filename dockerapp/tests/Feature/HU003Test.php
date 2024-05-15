<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class HU003Test extends TestCase
{
    use RefreshDatabase;

     public function setUp(): void
     {
         parent::setUp();
         Artisan::call('db:seed');
     }

    public function test_editar_producto_exitosamente(): void
    {
        $tokenAdmin = $this->post('/api/login', [
            "email" => "admin@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenAdmin];

        $product = Product::create([
            "name" => "Original Product",
            "description" => "Original product description",
            "available" => true,
            "image_path" => "original_image.png",
            "price" => 1000
        ]);

        $newProductData = [
            "name" => "Edited Product",
            "description" => "Edited product description",
            "available" => false,
            "image_path" => "edited_image.png",
            "price" => 1500
        ];

        $response = $this->withHeaders($headers)
            ->json('PUT', "/api/products/{$product->id}", $newProductData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', $newProductData);
    }

    public function test_mostrar_mensaje_alerta_campos_vacios_al_editar(): void
    {
        $tokenAdmin = $this->post('/api/login', [
            "email" => "admin@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenAdmin];

        $productData = [
            "name" => "",
            "description" => "",
            "available" => true,
            "image_path" => "",
            "price" => null
        ];

        $response = $this->withHeaders($headers)
            ->json('POST', '/api/products', $productData);

        $response->assertStatus(400);
        $response->assertJsonValidationErrors(['name', 'description', 'image_path', 'price']);
    }

    public function test_mostrar_mensaje_alerta_exceso_caracteres(): void
    {
        $tokenAdmin = $this->post('/api/login', [
            "email" => "admin@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenAdmin];

        $productData = [
            "name" => "Product with a very long name that exceeds the character limit",
            "description" => "Product description within the character limit",
            "available" => true,
            "image_path" => "product_image.png",
            "price" => 1000
        ];

        $response = $this->withHeaders($headers)
            ->json('POST', '/api/products', $productData);

        $response->assertStatus(400);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_mostrar_mensaje_error_no_autorizado(): void
    {
        $tokenClient = $this->post('/api/login', [
            "email" => "cliente@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenClient];

        $productData = [
            "name" => "New Product",
            "description" => "Product description",
            "available" => true,
            "image_path" => "product_image.png",
            "price" => 1000
        ];

        $response = $this->withHeaders($headers)
            ->json('POST', '/api/products', $productData);

        $response->assertStatus(403);
    }
}
