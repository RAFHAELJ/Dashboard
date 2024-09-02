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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->integer('note_id')->default(0); // ID da nota
            $table->text('content')->nullable(); // Conteúdo da nota
            $table->string('color')->default('#f5f5f5'); // Cor da nota
            $table->integer('position_x')->default(0); // Posição X na tela
            $table->integer('position_y')->default(0); // Posição Y na tela
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
