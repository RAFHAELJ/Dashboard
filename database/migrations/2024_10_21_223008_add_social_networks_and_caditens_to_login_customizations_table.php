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
        Schema::table('login_customizations', function (Blueprint $table) {
            // Adicionando o campo 'social_networks' do tipo JSON
            $table->json('social_networks')->nullable()->after('elements');
            
            // Adicionando o campo 'caditens' do tipo JSON
            $table->json('caditens')->nullable()->after('social_networks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login_customizations', function (Blueprint $table) {
            // Removendo os campos adicionados
            $table->dropColumn('social_networks');
            $table->dropColumn('caditens');
        });
    }
};
