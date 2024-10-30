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
        Schema::create('mac_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('radio_id');
            $table->string('nome');
            $table->string('mac_antigo');
            $table->string('mac_novo');
            $table->timestamps();
        
            $table->foreign('radio_id')->references('id')->on('radios')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mac_history');
    }
};
