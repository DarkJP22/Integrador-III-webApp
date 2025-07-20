<?php
namespace Database\Seeders;


use App\Currency;
use App\Role;
use App\Speciality;
use App\User;
use Illuminate\Database\Seeder;
use App\Plan;
use App\Tax;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $specialities = [
        'Acupunturista', 'Adicciones', 'Alergólogo', 'Algólogo', 'Anestesiólogo', 'Angiólogo', 'Audiólogo y Foniatra', 'Bariatra', 'Biotecnología', 'Cardiólogo', 'Cardiólogo Hemodinamista', 'Cardiólogo Pediatra', 'Cirugía Maxilofacial', 'Cirujano Cardiotorácico', 'Cirujano Endocrinólogo', 'Cirujano Gastroenterólogo', 'Cirujano General', 'Cirujano Oncólogo', 'Cirujano Pediatra', 'Cirujano Plástico', 'Cirujano Vascular', 'Coloproctólogo', 'Dentista', 'Dermatólogo', 'Dermatólogo Pediatra', 'Dermocosmiatra', 'Endocrinólogo', 'Endocrinólogo Pediatra', 'Endodoncista', 'Endoperiodontólogo', 'Estomatología', 'Fisioterapeuta', 'Gastroenterólogo', 'Gastroenterólogo Pediatra', 'Genetista', 'Geriatra', 'Ginecología Oncológica', 'Ginecólogo y Obstetra', 'Hematólogo', 'Hematólogo Pediatra', 'Homeópata', 'Infectólogo', 'Infectólogo Pediatra', 'Inmunólogo', 'Internista', 'Maxilofacial', 'Medicina del Trabajo', 'Médico Alternativo', 'Médico de Rehabilitación', 'Médico Deportivo', 'Médico en Biología de la Reproducción', 'Médico Estético', 'Médico Intensivista', 'Nefrólogo', 'Nefrólogo (Hemodiálisis)', 'Nefrólogo Pediatra', 'Neonatólogo', 'Neumólogo', 'Neumólogo Pediatra', 'Neurocirujano', 'Neurólogo', 'Neurólogo Pediatra', 'Neuroradiólogo', 'Nutriólogo', 'Nutriólogo Clínico', 'Odontopediatra', 'Oftalmólogo', 'Oftalmólogo Pediatra', 'Oncólogo', 'Oncólogo Pediatra', 'Optometrista', 'Ortodoncista', 'Ortopedista Pediatra', 'Ortopedista y Traumatólogo', 'Otorrinolaringólogo', 'Otorrinolaringólogo Pediatra', 'Patólogo Bucal', 'Pediatra', 'Periodoncista', 'Podiatra', 'Prostodoncista', 'Psicólogo', 'Psiquiatra', 'Quiropráctico', 'Radiólogo', 'Rehabilitación en Lenguaje y Audición', 'Reumatólogo', 'Urgencias Médico Quirúrgicas', 'Urólogo', 'Urólogo Pediatra', 'Odontología', 'Cirujía', 'Ginecología', 'Médico Familiar'
    ];

    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->cleanDatabase();

        Role::factory()->create();
        Role::factory()->create([
            'name' => 'paciente',
        ]);
        Role::factory()->create([
            'name' => 'administrador',
        ]);
        Role::factory()->create([
            'name' => 'clinica',
        ]);
        Role::factory()->create([
            'name' => 'asistente',
        ]);
        Role::factory()->create([
            'name' => 'farmacia',
        ]);
        Role::factory()->create([
            'name' => 'operador',
        ]);
        Role::factory()->create([
            'name' => 'laboratorio',
        ]);

        foreach ($this->specialities as $s) {
            Speciality::factory()->create([
                'name' => $s,
            ]);
        }

        $admin1 = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('12345678'),
            'api_token' => Str::random(50),
            'remember_token' => Str::random(10),
        ]);
        
        $admin2 = User::factory()->create([
            'name' => 'admin Julio',
            'email' => 'info@cittacr.com',
            'password' => bcrypt('12345678'),
            'api_token' => Str::random(50),
            'remember_token' => Str::random(10),
        ]);
        \DB::table('role_user')->insert(
            ['role_id' => 3, 'user_id' => 1]
        );
        \DB::table('role_user')->insert(
            ['role_id' => 3, 'user_id' => 2]
        );




        $plan2 = Plan::factory()->create([
            'title' => 'Perfil Médico Básico',
            'cost' => 5000,
            'quantity' => 1,
            'for_medic' => 1,
            'include_assistant' => 1,
            'include_fe' => 1,
            'commission_by_appointment' => 0,
        ]);
        $plan3 = Plan::factory()->create([
            'title' => 'Perfil Médico Detectable',
            'cost' => 5000,
            'quantity' => 1,
            'for_medic' => 1,
            'include_assistant' => 1,
            'include_fe' => 1,
            'commission_by_appointment' => 1,
            'general_cost_commission_by_appointment' => 1000,
            'specialist_cost_commission_by_appointment' => 2500
        ]);

        $plan4 = Plan::factory()->create([
            'title' => 'Perfil Laboratorio',
            'cost' => 10000,
            'quantity' => 1,
            'for_lab' => 1,
            'include_assistant' => 1,
            'include_fe' => 1,
            'commission_by_appointment' => 0,
            'general_cost_commission_by_appointment' => 0,
            'specialist_cost_commission_by_appointment' => 0
        ]);


        Currency::factory()->create();
        Currency::factory()->create([
            'code' => 'USD',
            'name' => 'Dolares',
            'symbol' => '$', // secret
            'exchange' => 589, // secret
            'exchange_venta' => 569,
        ]);
        
        Tax::factory()->create();
        Tax::factory()->create([
            'name' => 'IVA Reducido 2%',
            'tarifa' => 2,
            'CodigoTarifa' => '03'
        ]);
        Tax::factory()->create([
            'name' => 'IVA Reducido 4%',
            'tarifa' => 4,
            'CodigoTarifa' => '04'
        ]);
        Tax::factory()->create([
            'name' => 'IVA Tarifa 0% (Exento)',
            'tarifa' => 0,
            'CodigoTarifa' => '01'
        ]);

        $this->call(OrdersTableSeeder::class);
    }

    private function cleanDatabase(): void
    {
        Artisan::call('migrate:fresh --force');
    }
}
