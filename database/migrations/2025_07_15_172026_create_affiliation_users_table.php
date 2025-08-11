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
        Schema::create('affiliation_users', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->integer('discount')->default(10);
            $table->enum('active', ['Pending', 'Approved', 'Denied'])->default('Pending');
            $table->float('priceToAffiliation')->default(5);
            $table->enum('type_affiliation', ['Monthly', 'Semi-Annually', 'Annually'])->default('Monthly');
            $table->text('voucher')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliation_users');
    }
};
