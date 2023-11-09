<?php

use App\Models\Service;
use App\Models\Ticket;
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
        Schema::create('ticket_times', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Service::class, "service_id");
            $table->foreignIdFor(Ticket::class, "ticket_id")->nullable();
            $table->dateTime('passage')->nullable();
            $table->integer('user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_times');
    }
};
