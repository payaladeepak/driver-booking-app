<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixBookingsIndexesAndColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration is safe/idempotent:
     * - Adds booking_date, driver_id, status, softDeletes if missing.
     * - Adds indexes only if they do not already exist.
     * - Adds foreign key only if drivers table exists and FK not present.
     */
    public function up()
    {
        if (! Schema::hasTable('bookings')) {
            // If bookings table doesn't exist, create minimal table
            Schema::create('bookings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('customer_name', 191)->nullable();
                $table->string('customer_phone', 30)->nullable();
                $table->text('pickup_address')->nullable();
                $table->text('drop_address')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'booking_date')) {
                $table->dateTime('booking_date')->nullable()->after('id');
            }
            if (! Schema::hasColumn('bookings', 'driver_id')) {
                $table->unsignedBigInteger('driver_id')->nullable()->after('booking_date');
            }
            if (! Schema::hasColumn('bookings', 'status')) {
                $table->string('status', 50)->default('pending')->after('driver_id');
            }
            if (! Schema::hasColumn('bookings', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Helper to check index existence in MySQL
        $connection = DB::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($connection === 'mysql' || $connection === 'mysqli') {
            // Check & create driver_id index if missing
            $exists = DB::select("SHOW INDEX FROM `bookings` WHERE Key_name = 'bookings_driver_id_index'");
            if (empty($exists)) {
                try {
                    Schema::table('bookings', function (Blueprint $table) {
                        $table->index('driver_id');
                    });
                } catch (\Throwable $e) {
                    // ignore if index already exists by race condition
                }
            }

            // Check & create booking_date index if missing
            $exists = DB::select("SHOW INDEX FROM `bookings` WHERE Key_name = 'bookings_booking_date_index'");
            if (empty($exists)) {
                try {
                    Schema::table('bookings', function (Blueprint $table) {
                        $table->index('booking_date');
                    });
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            // Add FK if drivers table exists and FK not already present
            if (Schema::hasTable('drivers')) {
                // Try to detect if FK exists by attempting to query information_schema.KEY_COLUMN_USAGE
                $fkExists = DB::select("
                    SELECT CONSTRAINT_NAME
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                      AND TABLE_NAME = 'bookings'
                      AND COLUMN_NAME = 'driver_id'
                      AND REFERENCED_TABLE_NAME = 'drivers'
                ");
                if (empty($fkExists)) {
                    try {
                        DB::statement("ALTER TABLE `bookings` ADD CONSTRAINT `bookings_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE SET NULL ON UPDATE CASCADE");
                    } catch (\Throwable $e) {
                        // ignore if cannot add FK (different engine, privileges, or already exists)
                    }
                }
            }
        } else {
            // Non-MySQL (sqlite etc.): best-effort index creation (wrap in try/catch)
            try {
                Schema::table('bookings', function (Blueprint $table) {
                    $table->index('driver_id');
                    $table->index('booking_date');
                });
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (! Schema::hasTable('bookings')) {
            return;
        }

        // Attempt to drop foreign key if exists
        try {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropForeign(['driver_id']);
            });
        } catch (\Throwable $e) {}

        Schema::table('bookings', function (Blueprint $table) {
            try { $table->dropIndex(['driver_id']); } catch (\Throwable $e) {}
            try { $table->dropIndex(['booking_date']); } catch (\Throwable $e) {}

            if (Schema::hasColumn('bookings', 'booking_date')) {
                $table->dropColumn('booking_date');
            }
            if (Schema::hasColumn('bookings', 'driver_id')) {
                $table->dropColumn('driver_id');
            }
            if (Schema::hasColumn('bookings', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('bookings', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
}
