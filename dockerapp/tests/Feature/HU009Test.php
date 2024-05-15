<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class HU009Test extends TestCase
{
    use RefreshDatabase;

     public function setUp(): void
     {
         parent::setUp();
         Artisan::call('db:seed');
     }
    public function test_mostrar_lista_pedidos_aprobados_ascendente(): void {
        $tokenAdmin = $this->post('/api/login', [
            "email" => "admin@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenAdmin];

        $approvedOrder1 = Order::factory()->create(['status' => Order::STATUS_APPROVED]);
        $approvedOrder2 = Order::factory()->create(['status' => Order::STATUS_APPROVED]);
        $pendingOrder = Order::factory()->create(['status' => Order::STATUS_PENDING]);
        $rejectedOrder = Order::factory()->create(['status' => Order::STATUS_REJECTED]);

        $response = $this->withHeaders($headers)
            ->get('/api/orders/approved');

        $response->assertStatus(200);

        $orders = $response->json();
        $this->assertCount(2, $orders);
        $this->assertEquals($approvedOrder1->id, $orders[0]['id']);
        $this->assertEquals($approvedOrder2->id, $orders[1]['id']);
    }

    public function test_mostrar_lista_pedidos_aprobados_vacia(): void {
        $tokenAdmin = $this->post('/api/login', [
            "email" => "admin@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenAdmin];

        $pendingOrder = Order::factory()->create(['status' => Order::STATUS_PENDING]);
        $rejectedOrder = Order::factory()->create(['status' => Order::STATUS_REJECTED]);

        $response = $this->withHeaders($headers)
            ->get('/api/orders/approved');

        $response->assertStatus(200);

        $response->assertJson([]);
    }


}
