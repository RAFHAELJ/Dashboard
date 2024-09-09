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
            $table->id();
            $table->string('top_image')->nullable();  // Upload de imagem ou cor
            $table->string('background_type')->default('color'); // Pode ser 'color' ou 'image'
            $table->string('background_value')->nullable();  // Valor da cor ou URL da imagem
            $table->string('login_button_background')->nullable();
            $table->string('login_button_color')->nullable();
            $table->string('login_button_shape')->nullable(); // Formato do botão
            $table->string('login_button_text')->nullable();  // Texto do botão
            $table->json('social_logins')->nullable();  // Redes sociais
            $table->json('login_method')->nullable();  // Tipos de login (email, telefone)
            $table->json('password_method')->nullable();  // Máscaras de senha (CPF, telefone)
            $table->json('registration_fields')->nullable();  // Campos de cadastro
            $table->boolean('faq')->default(false);  // Checkbox para FAQ
            $table->timestamps();
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
