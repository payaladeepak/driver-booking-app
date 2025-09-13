<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add missing columns only if they don't exist
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'driver_id')) {
                $table->unsignedBigInteger('driver_id')->nullable()->after('customer_id');
            }
            if (!Schema::hasColumn('bookings', 'pickup')) {
                $table->string('pickup')->nullable()->after('service_type');
            }
            if (!Schema::hasColumn('bookings', 'dropoff')) {
                $table->string('dropoff')->nullable()->after('pickup');
            }
            if (!Schema::hasColumn('bookings', 'amount')) {
                // Place amount after service_type if possible
                $table->decimal('amount', 10, 2)->nullable()->after('service_type');
            }
            if (!Schema::hasColumn('bookings', 'booking_code')) {
                $table->string('booking_code', 32)->nullable()->unique()->after('id');
            }
        });

        // Ensure foreign key for driver_id (wrap in try/catch to avoid errors if constraint exists)
        try {
            Schema::table('bookings', function (Blueprint $table) {
                if (Schema::hasColumn('bookings', 'driver_id')) {
                    $table->foreign('driver_id')->references('id')->on('users')->nullOnDelete();
                }
            });
        } catch (\Throwable $e) {
            // ignore if FK already exists or DB doesn't support adding it this way
        }

        // Widen/align status enum to include controller values
        try {
            DB::statement("ALTER TABLE bookings MODIFY `status` ENUM('pending','accepted','on_the_way','on_way','searching','quoted','confirmed','arrived','started','completed','cancelled') NOT NULL DEFAULT 'searching'");
        } catch (\Throwable $e) {
            // ignore if the server rejects enum modify (will be handled manually)
        }

        // Fill NULL booking_code rows with UUIDs and then make column NOT NULL
        try {
            DB::statement("UPDATE bookings SET booking_code = UPPER(REPLACE(UUID(),'-','')) WHERE booking_code IS NULL");
            DB::statement("ALTER TABLE bookings MODIFY `booking_code` varchar(32) NOT NULL");
        } catch (\Throwable $e) {
            // ignore errors here — manual fix may be required if DB permissions restrict statements
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert changes if possible
        Schema::table('bookings', function (Blueprint $table) {
            // drop foreign key if exists (best-effort)
            try {
                $table->dropForeign(['driver_id']);
            } catch (\Throwable $e) {
                // ignore
            }

            if (Schema::hasColumn('bookings', 'driver_id')) {
                try { $table->dropColumn('driver_id'); } catch (\Throwable $e) {}
            }
            if (Schema::hasColumn('bookings', 'pickup')) {
                try { $table->dropColumn('pickup'); } catch (\Throwable $e) {}
            }
            if (Schema::hasColumn('bookings', 'dropoff')) {
                try { $table->dropColumn('dropoff'); } catch (\Throwable $e) {}
            }
            if (Schema::hasColumn('bookings', 'amount')) {
                try { $table->dropColumn('amount'); } catch (\Throwable $e) {}
            }
            // Do not drop booking_code in down() to avoid data loss; just make it nullable
            try {
                DB::statement("ALTER TABLE bookings MODIFY `booking_code` varchar(32) DEFAULT NULL");
            } catch (\Throwable $e) {
                // ignore
            }
        });
    }
};
