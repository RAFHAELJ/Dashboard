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
        Schema::create('hotspot', function (Blueprint $table) {
            $table->id();
            $table->string('bg');
            $table->string('topo');
            $table->string('css');
            $table->string('tipobg');
            $table->string('bgbtn');
            $table->string('login');
            $table->string('senha');
            $table->string('redesocial');
            $table->string('facebook');
            $table->string('twitter');
            $table->string('instagram');
            $table->json('caditens');
            $table->json('faq');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotspot');
    }
};
