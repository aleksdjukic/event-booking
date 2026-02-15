<?php

use App\Domain\Booking\Enums\BookingStatus;
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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->string('status')->default(BookingStatus::PENDING->value);
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['ticket_id', 'status']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
