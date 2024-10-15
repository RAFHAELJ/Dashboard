<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('faq', function (Blueprint $table) {
            $table->string('nome')->after('id')->nullable(); // Adiciona a coluna nome após o id, com a opção de ser nula
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('faq', function (Blueprint $table) {
            $table->dropColumn('nome'); // Remove a coluna nome
        });
    }
};
