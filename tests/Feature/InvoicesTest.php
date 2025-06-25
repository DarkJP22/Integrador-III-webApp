<?php

namespace Tests\Feature;

use App\Role;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvoicesTest extends TestCase
{
    use RefreshDatabase;
   /** @test */
    public function una_factura_requiere_codigo_de_actividad_hacienda()
    {
        //$this->withExceptionHandling();

    
        $user =  User::factory()->create();
     

        $role = Role::factory()->create(['name' => 'clinica']);
      

        $user->assignRole($role);

        $this->actingAs($user);

        $data = [
            'CodigoActividad' => ''
        ];

        $response = $this->post('/invoices', $data)
                    ->assertSessionHasErrors(['CodigoActividad']);

    }
}
 