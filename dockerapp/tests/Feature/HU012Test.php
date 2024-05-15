<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class HU012Test extends TestCase
{
    use RefreshDatabase;

     public function setUp(): void
     {
         parent::setUp();
         Artisan::call('db:seed');
     }
    public function test_crear_pedido_en_base_de_datos(): void {
        $tokenClient = $this->post('/api/login', [
            "email" => "cliente@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenClient];

        $orderData = [
            'name' => 'Order Name',
            'payment_path' => 'payment.png',
        ];

        $response = $this->withHeaders($headers)
            ->post('/api/orders', $orderData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('orders', ['name' => 'Order Name']);
    }

    public function test_mostrar_mensaje_alerta_sin_productos_seleccionados(): void {
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

    public function test_mostrar_mensaje_alerta_sin_adjuntar_comprobante_pago(): void {
        $client = User::factory()->create();

        $tokenClient = $this->post('/api/login', [
            "email" => $client->email,
            "password" => "password",
        ])->json('token');


        $headers = ['Authorization' => 'Bearer ' . $tokenClient];


        $orderData = [
            'name' => 'Order Name',
        ];

        $response = $this->withHeaders($headers)
            ->post('/api/orders', $orderData);

        $response->assertStatus(400);

        $response->assertJsonFragment(['payment_path' => ['The payment path field is required.']]);
    }

    public function test_mostrar_mensaje_alerta_nombre_vacio(): void {
        $client = User::factory()->create();

        $tokenClient = $this->post('/api/login', [
            "email" => $client->email,
            "password" => "password",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenClient];

        $orderData = [
            'name' => '',
            'payment_path' => 'payment.png',
        ];

        $response = $this->withHeaders($headers)
            ->post('/api/orders', $orderData);

        $response->assertStatus(400);

        $response->assertJsonFragment(['name' => ['The name field is required.']]);
    }

}
