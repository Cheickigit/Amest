<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();

            // infos demandeur
            $table->string('name', 180);
            $table->string('company', 180)->nullable();
            $table->string('email', 180)->nullable();
            $table->string('phone', 60)->nullable();
            $table->string('city', 120)->nullable();

            // détails projet
            $table->string('project_type', 120)->nullable();
            $table->string('budget', 120)->nullable();
            $table->string('desired_date', 120)->nullable();

            $table->text('message')->nullable();

            // pièces jointes (liste d’objets: name/path/url/size/mime)
            $table->json('files')->nullable();

            // suivi
            $table->string('status', 20)->default('new'); // new|read|archived
            $table->timestamp('read_at')->nullable();

            // trace
            $table->string('source_ip', 64)->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
