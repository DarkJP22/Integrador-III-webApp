<?php

use App\Enums\OfficeType;
use App\Office;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->json('settings')->nullable();
        });

        $offices = Office::where('type', OfficeType::LABORATORY)->get();

        foreach ($offices as $office) {

            if ($office->settings === null) {
                $office->settings = [];
                $office->settings['lab_whatsapp_message'] = 'Hola deseo ser contactado para asesoría de examenes de laboratorio';
                $office->settings['lab_whatsapp_exam_message'] = 'Hola deseo ser contactado para asesoría de examenes de laboratorio';
                $office->settings['lab_whatsapp_package_exam_message'] = 'Hola deseo solicitar una cita para paquete';
                $office->settings['lab_exam_cash_discount'] = 0;
                $office->save();
            }


        }

    }

    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn('settings');
        });
    }
};
