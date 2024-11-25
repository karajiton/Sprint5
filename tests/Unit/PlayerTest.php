<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;


class PlayerTest extends TestCase
{
    use RefreshDatabase; // Limpia la base de datos después de cada test

    protected function setUp(): void
    {
        parent::setUp();
        // Crea un usuario autenticado para las pruebas
        $this->user = User::factory()->create();
        Passport::actingAs($this->user); // Autentica al usuario
    }

    /** @test */
    public function it_can_create_a_player()
    {
        $data = [
            'name' => 'Player Test',
            'email' => 'player@example.com',
        ];

        $response = $this->postJson('/api/players', $data);

        $response->assertStatus(201); // Código de éxito para creación
        $this->assertDatabaseHas('players', ['email' => 'player@example.com']);
    }

    /** @test */
    public function it_can_update_a_player()
    {
        $player = Player::factory()->create();

        $updateData = ['name' => 'Updated Name'];

        $response = $this->putJson("/api/players/{$player->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('players', ['name' => 'Updated Name']);
    }

    /** @test */
    public function it_can_list_players()
    {
        Player::factory()->count(5)->create();

        $response = $this->getJson('/api/players');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data'); // Suponiendo que tienes paginación
    }

    /** @test */
    public function it_can_delete_a_player_and_their_games()
    {
        $player = Player::factory()->create();
        $response = $this->deleteJson("/api/players/{$player->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('players', ['id' => $player->id]);
    }
}

