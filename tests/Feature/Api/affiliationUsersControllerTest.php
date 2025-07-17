<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\AffiliationUsers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class AffiliationUserTest extends TestCase
{
    use RefreshDatabase;
  use HasFactory;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

   #[Test]
    public function usuario_autenticado_api_puede_listar_afiliaciones()
    {
        AffiliationUsers::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'api')->getJson('/api/affiliation-users');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);
    }
//Listo
   #[Test]
    public function usuario_autenticado_api_puede_crear_afiliacion()
    {
        $data = [
            'date' => now()->toDateString(),
            'active' => true,
            'type_affiliation' => 1,
            'voucher' => 'voucher_123.pdf',
        ];

        $response = $this->actingAs($this->user, 'api')->postJson('/api/affiliation-users', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('affiliation_users', $data);
    }

    #[Test]
    public function usuario_autenticado_api_puede_actualizar_afiliacion()
    {
        $affiliationUser = AffiliationUsers::factory()->create();

        $updatedData = [
            'user_id' => $this->user->id,
            'date' => now()->toDateString(),
            'active' => false,
            'type_affiliation' => 2,
            'voucher' => 'updated_voucher.pdf',
        ];

        $response = $this->actingAs($this->user, 'api')->putJson("/api/affiliation-users/{$affiliationUser->id}", $updatedData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('affiliation_users', [
            'id' => $affiliationUser->id,
            'voucher' => 'updated_voucher.pdf',
            'type_affiliation' => 2,
        ]);
    }

   #[Test]
    public function usuario_autenticado_api_puede_eliminar_afiliacion()
    {
        $affiliationUser = AffiliationUsers::factory()->create();

        $response = $this->actingAs($this->user, 'api')->deleteJson("/api/affiliation-users/{$affiliationUser->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('affiliation_users', ['id' => $affiliationUser->id]);
    }
}
