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
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Customer info
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 30)->nullable();

            // Addresses
            $table->text('pickup_address')->nullable();
            $table->text('drop_address')->nullable();

            // Core booking info
            $table->dateTime('booking_date')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->string('status', 50)->default('pending');
            $table->string('booking_code', 50)->unique();

            // Extended fields
            $table->string('service_type')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('pickup')->nullable();
            $table->string('dropoff')->nullable();
            $table->decimal('pickup_lat', 10, 7)->nullable();
            $table->decimal('pickup_lng', 10, 7)->nullable();
            $table->decimal('drop_lat', 10, 7)->nullable();
            $table->decimal('drop_lng', 10, 7)->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('requested_at')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Timestamps + soft deletes
            $table->timestamps();
            $table->softDeletes();

            // Indexes & foreign keys
            $table->index('customer_id');
            $table->index('driver_id');
            $table->index('booking_date');
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
