<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class HU002Test extends TestCase
{
    use RefreshDatabase;

     public function setUp(): void
     {
         parent::setUp();
         Artisan::call('db:seed');
     }

    public function test_crear_producto_exitoso(): void
    {



        $tokenAdmin = $this->post('/api/login', [
            "email" => "admin@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenAdmin];

        $productData = [
            "name" => "Desayuno sencillo",
            "description" => "Huevo, papas y cafe",
            "available" => true,
            "image_path" => "breakfast.png",
            "price" => 5700
        ];

        $response = $this->withHeaders($headers)
            ->json('POST', '/api/products', $productData);


        $response->assertStatus(201);


    }
    public function test_crear_producto_campos_vacios(): void
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
    public function test_crear_producto_excediendo_limite_caracteres(): void
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
    public function test_crear_producto_no_autorizado(): void
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
