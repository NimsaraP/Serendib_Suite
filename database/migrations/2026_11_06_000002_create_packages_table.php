<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('photographer_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('studio_id')
                ->nullable()
                ->constrained('studios')
                ->restrictOnDelete();

            $table->string('package_name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->unsigned();
            $table->decimal('duration_hours', 4, 1)->unsigned();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};

