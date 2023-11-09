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
        Schema::create('bornes', function (Blueprint $table) {
            $table->id();
            $table->string("description")->nullable();
            $table->string("services")->nullable();
            $table->string("fond")->nullable();
            $table->string("h_ouverture")->nullable();
            $table->string("h_fermeture")->nullable();
            $table->boolean("monday")->default(1)->nullable();
            $table->boolean("tuesday")->default(1)->nullable();
            $table->boolean("wednesday")->default(1)->nullable();
            $table->boolean("thursday")->default(1)->nullable();
            $table->boolean("friday")->default(1)->nullable();
            $table->boolean("saturday")->default(1)->nullable();
            $table->boolean("sunday")->default(0)->nullable();
            $table->boolean("enable")->default(1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bornes');
    }
};
