<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegioesTable extends Migration
{
    public function up()
    {
        Schema::create('regioes', function (Blueprint $table) {
            $table->id();
            $table->string('cidade');
            $table->string('bairros'); // Usaremos JSON para armazenar os bairros como array
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('regioes');
    }
}

