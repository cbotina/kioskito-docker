<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class HU010Test extends TestCase
{
    use RefreshDatabase;

     public function setUp(): void
     {
         parent::setUp();
         Artisan::call('db:seed');
     }

    public function test_pasar_pedido_a_cocina(): void {
        $tokenJefe = $this->post('/api/login', [
            "email" => "jefe@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenJefe];

        $order = Order::factory()->create();

        $response = $this->withHeaders($headers)
            ->put("/api/orders/{$order->id}/start");

        $response->assertStatus(200);

        $response->assertJsonFragment(['status' => Order::STATUS_STARTED]);
    }
    public function test_finalizar_pedido(): void {
        $tokenKitchenBoss = $this->post('/api/login', [
            "email" => "jefe@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenKitchenBoss];

        $order = Order::factory()->create(['status' => Order::STATUS_STARTED]);

        $response = $this->withHeaders($headers)
            ->put("/api/orders/{$order->id}/finish");

        $response->assertStatus(200);

        $response->assertJsonFragment(['status' => Order::STATUS_FINISHED]);
    }



}
