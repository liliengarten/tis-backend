<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_via_api()
    {
        $payload = [
            'fio' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'secret123',
        ];

        $response = $this->postJson('/api/signup', $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'user_token'
            ]
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
            'fio' => 'Test User'
        ]);
    }

    public function test_register_with_taken_email()
    {
        $payload = [
            'fio' => 'admin',
            'email' => 'admin@admin.com',
            'password' => 'adminadmin',
        ];

//        $this->assertDatabaseHas('users', [
//            'fio' => 'admin',
//            'email' => 'admin@admin.com'
//        ]);

        $response = $this->postJson('/api/signup', $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'code' => '422',
            'message' => 'Validation Error',
            'errors' => [[
                'email' => ['The email had already been taken.']
            ]]
        ]);
    }

}
