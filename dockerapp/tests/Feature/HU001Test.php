<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class HU001Test extends TestCase
{
    use RefreshDatabase;
    public function setUp(): void
    {
        parent::setUp();

        // Run the UserSeeder before each test
        Artisan::call('db:seed');
    }
    public function test_inicio_sesion_exitoso(): void
    {

        $response = $this->post('/api/login', [
            "email"=>"admin@gmail.com",
            "password"=>"12345678",
        ]);

        $response->assertStatus(200);

    }
    public function test_inicio_sesion_no_autorizado(): void
    {

        $token = $this->post('/api/login', [
            "email"=>"cliente@gmail.com",
            "password"=>"12345678",
        ])->json('token');

        $this->withHeader('Authorization', 'Bearer ' . $token)
        ->get('/api/orders/')
        ->assertStatus(403);

    }
    public function test_inicio_sesion_credenciales_invalidas(): void
    {

        $response = $this->post('/api/login', [
            "email"=>"admin@gmail.com",
            "password"=>"abcd",
        ]);

        $response->assertStatus(400);

    }

    public function test_inicio_sesion_campos_vacios(): void
    {

        $response = $this->post('/api/login', [
            "email"=>"",
            "password"=>"",
        ]);

        $response->assertStatus(400);

    }

}
