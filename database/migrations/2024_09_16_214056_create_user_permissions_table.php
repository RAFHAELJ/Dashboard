<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id(); // ID da associação
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relacionamento com a tabela de users
            $table->foreignId('permission_id')->constrained()->onDelete('cascade'); // Relacionamento com a tabela de permissions
            $table->foreignId('page_id')->constrained()->onDelete('cascade'); // Relacionamento com a tabela de pages
            $table->timestamps(); // Campos created_at e updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_permissions');
    }
}
