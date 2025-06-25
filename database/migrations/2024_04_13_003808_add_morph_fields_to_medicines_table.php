<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dateTime('reminder_start')->after('date_purchase')->nullable();
            $table->dateTime('reminder_end')->after('reminder_start')->nullable();
            $table->integer('active_remember_for_days')->after('remember_days')->default(0);
            $table->integer('requested_units')->after('active_remember_for_days')->default(0);
            $table->nullableMorphs('creator');
        });
        \App\Medicine::lazy()->each(function ($medicine) {
            $medicine->creator_id = $medicine->user_id;
            $medicine->creator_type = 'App\User';
            $medicine->save();
        });
        \App\Pmedicine::lazy()->each(function ($pmedicine) {
            \App\Medicine::create([
                'name' => $pmedicine->name,
                'date_purchase' => $pmedicine->date_purchase,
                'remember' => $pmedicine->remember,
                'remember_days' => $pmedicine->remember_days,
                'user_id' => $pmedicine->user_id,
                'patient_id' => $pmedicine->patient_id,
                'receta' => $pmedicine->receta,
                'active_remember_for_days' => 0,
                'creator_id' => $pmedicine->pharmacy_id,
                'creator_type' => 'App\Pharmacy',
                'created_at' => $pmedicine->created_at,
                'updated_at' => $pmedicine->updated_at,
            ]);

            $pmedicine->delete();
        });
    }

    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropMorphs('creator');
            $table->dropColumn('requested_units');
            $table->dropColumn('active_remember_for_days');
            $table->dropColumn('reminder_end');
            $table->dropColumn('reminder_start');
        });
    }
};
