<?php

use App\Models\Service;
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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string("numero");
            $table->string("service");
            $table->foreignIdFor(Service::class, "service_id");
            $table->string("served")->nullable();
            $table->integer("etat")->default(0)->nullable();
            $table->integer("guichet")->nullable();
            $table->integer("code")->nullable();
            $table->integer("transfert")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
