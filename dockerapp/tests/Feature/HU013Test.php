<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class HU013Test extends TestCase
{
    use RefreshDatabase;

     public function setUp(): void
     {
         parent::setUp();
         Artisan::call('db:seed');
     }
    public function test_agregar_producto_al_pedido(): void {
        $client = User::factory()->create();

        $tokenClient = $this->post('/api/login', [
            "email" => $client->email,
            "password" => "password",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenClient];

        $order = Order::factory()->create();

        $product = Product::factory()->create();

        $productData = [
            'product_id' => $product->id,
            'quantity' => 2,
        ];

        $response = $this->withHeaders($headers)
            ->post("/api/orders/{$order->id}/products", $productData);

        $response->assertStatus(200);

    }

    public function test_mostrar_mensaje_alerta_campos_vacios_al_agregar_producto_a_pedido(): void {
        $client = User::factory()->create();

        $tokenClient = $this->post('/api/login', [
            "email" => $client->email,
            "password" => "password",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenClient];

        $order = Order::factory()->create();

        $productData = [];

        $response = $this->withHeaders($headers)
            ->post("/api/orders/{$order->id}/products", $productData);

        $response->assertStatus(400);

        $response->assertJsonValidationErrors(['product_id', 'quantity']);
    }

}
