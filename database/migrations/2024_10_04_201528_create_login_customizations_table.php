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
        Schema::create('login_customizations', function (Blueprint $table) {
            $table->id(); // Chave primária
            $table->string('layout_name'); // Nome do layout
            $table->string('top_type'); // Tipo do topo (Cor ou Imagem)
            $table->string('top_value')->nullable(); // Valor da cor ou caminho da imagem do topo
            $table->string('background_type'); // Tipo de fundo (Cor ou Imagem)
            $table->string('background_value')->nullable(); // Cor ou caminho da imagem de fundo
            $table->string('login_button_text'); // Texto do botão de login
            $table->string('login_button_color'); // Cor do botão de login
            $table->json('login_method')->nullable(); // Métodos de login (e.g., Email, Nome, etc.)
            $table->json('login_password_method')->nullable(); // Métodos de senha (e.g., CPF, Telefone, etc.)            
            $table->string('input_color'); // Cor dos campos de input
            $table->string('background_image')->nullable(); // Caminho da imagem de fundo
            $table->json('elements'); // Dados dos elementos da página (JSON)
            $table->string('region'); // Região associada ao layout
            $table->string('username')->nullable(); // Nome do usuário associado
            $table->timestamps(); // Criação e atualização
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_customizations');
    }
};
