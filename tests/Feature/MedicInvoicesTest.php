<?php

namespace Tests\Feature;

use App\Plan;
use App\Repositories\UserRepository;
use App\Role;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class MedicInvoicesTest extends TestCase
{

    use RefreshDatabase, WithFaker;
   
    /** @test */
    public function un_usuario_diferente_a_rol_medico_no_puede_ver_facturacion()
    {
        $this->withExceptionHandling();

    
        $user =  User::factory()->create();
     

        $role = Role::factory()->create(['name' => 'clinica']);
      

        $user->assignRole($role);
      

        $this->actingAs($user);


        $response = $this->get('/medic/invoices')
                    ->assertRedirect('/');

        
    }

    /** @test */
    public function solo_un_medico_con_perfil_facturacion_puede_facturar()
    {
        $this->withExceptionHandling();

    
        $user =  User::factory()->create();
     

        $role = Role::factory()->create(['name' => 'medico']);
      
        
        $user->assignRole($role);
      

        $this->actingAs($user);

     
        $planFreeMedico = Plan::factory()->create([
            'include_fe' => 0
        ]);

        $user->subscription()->create([
            'plan_id' => $planFreeMedico->id,
            'cost' => $planFreeMedico->cost,
            'quantity' => $planFreeMedico->quantity,
            'ends_at' => Carbon::now()->addMonths($planFreeMedico->quantity),
            'purchase_operation_number' => $planFreeMedico->cost > 0 ? '---' : 'free'
        ]);

    

        //when
        $response = $this->get('/invoices/create')
                    ->assertRedirect('/');

        
    }

    /** @test */
    public function un_medico_con_perfil_gratis_no_puede_ver_facturacion()
    {
        $this->withExceptionHandling();

        //given teniendo un medico
    
        $user =  User::factory()->create();
     

        $role = Role::factory()->create(['name' => 'medico']);
      

        $user->assignRole($role);
        $user->update(['current_role_id' => $role->id]);

        $this->actingAs($user);

       
        $planFreeMedico = Plan::factory()->create([
            'include_fe' => 0
        ]);

        $user->subscription()->create([
            'plan_id' => $planFreeMedico->id,
            'cost' => $planFreeMedico->cost,
            'quantity' => $planFreeMedico->quantity,
            'ends_at' => Carbon::now()->addMonths($planFreeMedico->quantity),
            'purchase_operation_number' => $planFreeMedico->cost > 0 ? '---' : 'free'
        ]);

        

        //when
        $response = $this->get('/medic/invoices')
                    ->assertRedirect('/medic/changeaccounttype');

        
    }

    
}
