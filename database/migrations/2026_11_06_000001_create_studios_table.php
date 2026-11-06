<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('studios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('photographer_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('studio_name');
            $table->text('description')->nullable();
            $table->string('location');
            $table->string('contact_email');
            $table->string('contact_phone', 20)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studios');
    }
};