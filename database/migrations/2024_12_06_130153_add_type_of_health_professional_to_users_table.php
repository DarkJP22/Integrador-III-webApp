<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('type_of_health_professional')->nullable();
        });
        Schema::table('specialities', function (Blueprint $table) {
            $table->string('professional')->nullable();
        });
        \App\Speciality::truncate();
        \Illuminate\Support\Facades\DB::table('speciality_user')->truncate();
        $medicoSpecialities = [
            'Acupuntura',
            'Administración de Servicios de Salud',
            'Alergología Clínica',
            'Anatomía Patológica',
            'Anestesiología y Recuperación',
            'Cardiología',
            'Cirugía Cráneo maxilofacial',
            'Cirugía de Tórax y Cardiovascular',
            'Cirugía General',
            'Cirugía Pediátrica',
            'Cirugía Plástica, Reconstructiva y Estética',
            'Cirugía Torácica General',
            'Dermatología',
            'Endocrinología',
            'Foniatría-Audiología',
            'Gastroenterología',
            'Genética Clínica',
            'Genética Humana',
            'Geriatría y Gerontología',
            'Ginecología y Obstetricia',
            'Hematología',
            'Homeopatía',
            'Infectología',
            'Informática Médica',
            'Inmunología Clínica',
            'Medicina Aeroespacial',
            'Medicina Crítica y Terapia Intensiva',
            'Medicina de Emergencias',
            'Medicina del Deporte',
            'Medicina del Trabajo',
            'Medicina Extracorpórea',
            'Medicina Familiar y Comunitaria',
            'Medicina Física y Rehabilitación',
            'Medicina Hiperbárica',
            'Medicina Interna',
            'Medicina Legal',
            'Medicina Preventiva y Salud Pública',
            'Medicina Tropical',
            'Nefrología',
            'Neumología',
            'Neurocirugía',
            'Neurología',
            'Nutriología Clínica',
            'Oftalmología',
            'Oncología Médica',
            'Oncología Quirúrgica',
            'Ortopedia y Traumatología',
            'Otorrinolaringología y Cirugía de Cabeza y Cuello',
            'Patología Forense',
            'Pediatría',
            'Psiquiatría',
            'Radiología e Imágenes Médicas',
            'Oncología Radioterápica',
            'Reumatología',
            'Urología',
            'Vascular Periférico'
        ];

        foreach ($medicoSpecialities as $speciality) {
            \App\Speciality::create([
                'name' => $speciality,
                'professional' => 'medico'
            ]);
        }

        $odontologoSpecialities = [
            'Odontopediatría',
            'Ortodoncia',
            'Implantología Oral',
            'Patología y Cirugía oral',
            'Cirugía oral y Maxilofacial',
            'Endodoncia',
            'Periodoncia',
            'Prostodoncia',
            'Odontogeriatría',
            'Trastornos temporomandibulares y dolor orofacial'
        ];
        foreach ($odontologoSpecialities as $speciality) {
            \App\Speciality::create([
                'name' => $speciality,
                'professional' => 'odontologo'
            ]);
        }

        $nutricionSpecialities = [
            'Nutrición Clínica de las Enfermedades Crónicas no Transmisibles',
            'Nutrición Pediátrica',
            'Salud Pública',
            'Gestión de Proyectos en Inocuidad de Alimentos',
            'Nutrición Humana',
            'Gestión de Servicios de Salud',
            'Educación para la Salud',
            'Nutrición Clínica Renal',
            'Trastornos de la Conducta Alimentaria',
            'Gestión de Servicios de Alimentos',
            'Especialista en Servicios de Alimentos',
            'Bioquímica',
            'Educación y Docencia',
            'Investigación en Nutrición Pública',
            'Nutrición Pública y Seguridad Alimentaria Nutricional',
            'Nutrición Clínica',
            'Cuidados Paliativos',
            'Nutrición Deportiva',
            'Seguridad Alimentaria y Nutricional'
        ];
        foreach ($nutricionSpecialities as $speciality) {
            \App\Speciality::create([
                'name' => $speciality,
                'professional' => 'nutricionista'
            ]);
        }

    \App\Speciality::whereNull('professional')->update(['professional' => 'medico']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('type_of_health_professional');
        });
        Schema::table('specialities', function (Blueprint $table) {
            $table->dropColumn('professional');
        });
    }
};
