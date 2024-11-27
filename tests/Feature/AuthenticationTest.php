<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    protected $playerUser;
    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        
         // Run the migrations
         Artisan::call('migrate');
    
         // Seed the database
         Artisan::call('db:seed');
     
         // Create a personal access client for Passport without interaction
         Artisan::call('passport:client', [
             '--name' => 'TestClient',
             '--no-interaction' => true,
             '--personal' => true
         ]);
     
         // Create a user with 'player' role
         $this->playerUser = User::create([
             'name' => 'PlayerUser',
             'email' => 'player@example.com',
             'password' => bcrypt('securePassword'),
         ]);
         $this->playerUser->assignRole('player');
     
         // Create a user with 'admin' role
         $this->adminUser = User::create([
             'name' => 'AdminUser2',
             'email' => 'admin2@example.com',
             'password' => bcrypt('securePassword'),
         ]);
         $this->adminUser->assignRole('admin');
     }
 
    

    /**
     * Test para registrar un nuevo usuario exitosamente.
     */
    public function test_register_new_user_successfully()
    {
        $this->withoutExceptionHandling();
        $response = $this->postJson('/api/register', [
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
        $response = $this->postJson('/api/register', [
            'name'  => 'anonimo',
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password', 'email']);
    }

    /**
     * Test para verificar que no se permite el registro con un nombre duplicado.
     */
    public function test_register_with_duplicate_name()
    {
        User::factory()->create(['name' => 'DuplicateName', 'email' => 'duplicate@example.com']);

        $response = $this->postJson('/api/register', [
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
        $response = $this->postJson('/api/register', [
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
    public function test_login_successful()
{
    // Crear un usuario de prueba
    $user = User::factory()->create([
        'email' => 'testuser@example.com',
        'password' => bcrypt('password123'),
    ]);

    // Enviar solicitud de login
    $response = $this->postJson('/api/login', [
        'email' => 'testuser@example.com',
        'password' => 'password123',
    ]);

    // Verificar que la respuesta tiene un código 200 y contiene el token
    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'token',
            'user' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
        ]);
    }
    public function test_login_validation_error()
    {
    // Enviar solicitud con datos incompletos
    $response = $this->postJson('api/login', [
        'email' => '',
        'password' => '',
    ]);

    // Verificar que la respuesta tiene un código 422 y errores de validación
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
    }
    public function test_login_invalid_credentials()
    {
    // Crear un usuario de prueba
    $user = User::factory()->create([
        'email' => 'testuser@example.com',
        'password' => bcrypt('password123'),
    ]);

    // Enviar solicitud de login con credenciales incorrectas
    $response = $this->postJson('/api/login', [
        'email' => 'testuser@example.com',
        'password' => 'wrongpassword',
    ]);

    // Verificar que la respuesta tiene un código 401 y el mensaje esperado
    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Invalid credentials',
        ]);
    }
    
    
    
    

}
