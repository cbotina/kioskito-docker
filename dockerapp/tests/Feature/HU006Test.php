<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class HU006Test extends TestCase
{

    use RefreshDatabase;

     public function setUp(): void
     {
         parent::setUp();
         Artisan::call('db:seed');
     }
    public function test_mostrar_lista_pedidos_existente(): void {
        $tokenAdmin = $this->post('/api/login', [
            "email" => "admin@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenAdmin];

        $orders = Order::factory()->count(3)->create();

        $response = $this->withHeaders($headers)
            ->get('/api/orders');

        $response->assertStatus(200);
        $response->assertJson($orders->toArray());
    }

    public function test_mostrar_lista_pedidos_vacia(): void {
        $tokenAdmin = $this->post('/api/login', [
            "email" => "admin@gmail.com",
            "password" => "12345678",
        ])->json('token');

        $headers = ['Authorization' => 'Bearer ' . $tokenAdmin];


        $response = $this->withHeaders($headers)
            ->get('/api/orders');

        $response->assertStatus(200);
        $response->assertJson([]);
    }

}
