<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class HU007Test extends TestCase
{
    use RefreshDatabase;

     public function setUp(): void
     {
         parent::setUp();
         Artisan::call('db:seed');
     }
    public function test_cambiar_estado_pedido_aprobado(): void {
        $tokenAdmin = $this->post('/api/login', [
            "email" => "admin@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenAdmin];

        $order = Order::factory()->create();

        $response = $this->withHeaders($headers)
            ->put("/api/orders/{$order->id}/approve");

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => Order::STATUS_APPROVED]);
    }

}
