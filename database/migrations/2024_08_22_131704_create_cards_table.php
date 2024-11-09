<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Execute the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('page');
            $table->string('url')->nullable();
            $table->enum('type', ['Gráfico', 'Texto']);
            $table->string('content')->nullable();
            $table->text('chart_options')->nullable(); // Para dados específicos de gráficos
            $table->text('annotations')->nullable(); // Para armazenar anotações
            $table->integer('position_x')->default(0); // Posição X na tela
            $table->integer('position_y')->default(0); // Posição Y na tela
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards');
    }
}
