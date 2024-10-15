<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('access_data', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['controller', 'database']);
            $table->string('nome')->nullable();
            $table->string('ip', 255)->nullable();
            $table->integer('porta')->nullable();
            $table->string('senha')->nullable();
            $table->string('db_host')->nullable();
            $table->string('db_name')->nullable();
            $table->string('db_user')->nullable();
            $table->string('db_password')->nullable();
            $table->text('info')->nullable();
            $table->integer('regiao')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('access_data');
    }
};
