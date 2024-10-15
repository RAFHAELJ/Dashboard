<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id(); // ID da página
            $table->string('name'); // Nome da página ou recurso (ex: dashboard, relatorios)
            $table->string('slug');// Slug da página
            $table->string('description')->nullable();
            $table->timestamps(); // Campos created_at e updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
