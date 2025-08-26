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
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->string('organization')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();
            $table->date('deadline')->nullable();
            $table->text('scope')->nullable();
            $table->json('documents')->nullable();
            $table->string('status', 24)->default('nouveau'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenders');
    }
};
