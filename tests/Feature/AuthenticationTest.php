<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles necesarios para el sistema
        Role::create(['name' => 'player', 'guard_name' => 'api']);
    }

    /**
     * Test para registrar un nuevo usuario exitosamente.
     */
    public function test_register_new_user_successfully()
    {
        $response = $this->postJson('/register', [
            'name' => 'TestPlayer',
            'email' => 'testplayer@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'TestPlayer',
            'email' => 'testplayer@example.com',
        ]);

        $user = User::where('email', 'testplayer@example.com')->first();
        $this->assertTrue($user->hasRole('player'));
    }

    /**
     * Test para verificar validaciones de entrada faltantes.
     */
    public function test_register_user_validation_error()
    {
        $response = $this->postJson('/register', [
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'password', 'email']);
    }

    /**
     * Test para verificar que no se permite el registro con un nombre duplicado.
     */
    public function test_register_with_duplicate_name()
    {
        User::factory()->create(['name' => 'DuplicateName', 'email' => 'duplicate@example.com']);

        $response = $this->postJson('/register', [
            'name' => 'DuplicateName',
            'email' => 'newuser@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test para verificar que se puede registrar como anónimo.
     */
    public function test_register_as_anonymous_user()
    {
        $response = $this->postJson('/register', [
            'email' => 'anonymous@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('name', 'anonimo');

        $this->assertDatabaseHas('users', [
            'email' => 'anonymous@example.com',
            'name' => 'anonimo',
        ]);
    }
}
