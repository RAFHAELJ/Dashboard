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
        Schema::create('campanhas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->date('comeco');
            $table->date('fim');
            $table->longText('radios');
            $table->string('publico');
            $table->string('idade');
            $table->string('tipo');
            $table->string('video')->nullable();
            $table->string('capa')->nullable();
            $table->string('imagem')->nullable();
            $table->string('regiao')->nullable();
            $table->integer('tempo');
            $table->string('url');
            $table->integer('duracao');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campanhas');
    }
};
