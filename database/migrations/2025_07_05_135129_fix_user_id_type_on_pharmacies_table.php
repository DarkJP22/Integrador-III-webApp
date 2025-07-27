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
        Schema::table('pharmacies', function (Blueprint $table) {
            if (!Schema::hasColumn('pharmacies', 'user_id')) {
                $table->unsignedInteger('user_id')->nullable();
            }

            // Agregar la clave foránea (puedes usar try-catch si quieres manejar errores en caso de que ya exista)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pharmacies', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            // Puedes volver a bigint si quieres, pero asegúrate que coincida con users.id
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }
};
