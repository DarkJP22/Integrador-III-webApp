<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevalorizacionToDiseaseNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disease_notes', function (Blueprint $table) {
            $table->text('revalorizacion')->after('phisical_review')->nullable();  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disease_notes', function (Blueprint $table) {
            $table->dropColumn('revalorizacion');
        });
    }
}
