<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class HU005Test extends TestCase
{
    use RefreshDatabase;

     public function setUp(): void
     {
         parent::setUp();
         Artisan::call('db:seed');
     }
    public function test_listar_productos_existentes(): void
    {
        $tokenAdmin = $this->post('/api/login', [
            "email" => "admin@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenAdmin];

        $products = Product::factory()->count(3)->create();

        $response = $this->withHeaders($headers)
            ->get('/api/products');

        $response->assertStatus(200);
        $response->assertJson($products->toArray());
    }

    public function test_listar_productos_vacios(): void
    {
        $tokenAdmin = $this->post('/api/login', [
            "email" => "admin@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenAdmin];

        $response = $this->withHeaders($headers)
            ->get('/api/products');

        $response->assertStatus(200);
        $response->assertJson([]);
    }
}
