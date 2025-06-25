<?php

namespace Tests\Feature;

use App\Plan;
use App\Role;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MedicAssistantsTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    /** @test */
    public function un_usuario_diferente_a_rol_medico_no_puede_ver_asistentes()
    {
        $this->withExceptionHandling();

    
        $user =  User::factory()->create();
     

        $role = Role::factory()->create(['name' => 'clinica']);
      

        $user->assignRole($role);
      

        $this->actingAs($user);


        $response = $this->get('/medic/assistants')
                    ->assertRedirect('/');

        
    }
    /** @test */
    public function solo_un_medico_con_perfil_secretaria_facturacion_puede_crear_asistentes()
    {
        $this->withExceptionHandling();

    
        $user =  User::factory()->create();
     

        $role = Role::factory()->create(['name' => 'medico']);
      
        
        $user->assignRole($role);
      

        $this->actingAs($user);

     
        $planFreeMedico = Plan::factory()->create([
            'include_assistant' => 0
        ]);

        $user->subscription()->create([
            'plan_id' => $planFreeMedico->id,
            'cost' => $planFreeMedico->cost,
            'quantity' => $planFreeMedico->quantity,
            'ends_at' => Carbon::now()->addMonths($planFreeMedico->quantity),
            'purchase_operation_number' => $planFreeMedico->cost > 0 ? '---' : 'free'
        ]);

    

        //when
        $response = $this->get('/medic/assistants/create')
                    ->assertRedirect('/medic/changeaccounttype');

        
    }
}
