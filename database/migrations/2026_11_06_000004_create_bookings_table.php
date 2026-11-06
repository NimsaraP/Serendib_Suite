<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('photographer_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('studio_id')
                ->nullable()
                ->constrained('studios')
                ->restrictOnDelete();

            $table->foreignId('package_id')
                ->constrained('packages')
                ->restrictOnDelete();

            $table->decimal('package_price', 10, 2)->unsigned();
            $table->date('event_date');
            $table->string('event_location');
            $table->text('special_requirements')->nullable();
            $table->date('booking_date');
            $table->string('booking_status', 30)->default('pending');
            $table->string('event_status', 30)->default('upcoming');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

