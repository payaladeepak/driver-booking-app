cat > /home/u608956572/domains/wow.dukandar.online/public_html/wow/database/migrations/2025_09_09_200833_create_trip_status_events_table.php <<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripStatusEventsTable extends Migration
{
    public function up()
    {
        Schema::create('trip_status_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trip_id')->index();
            $table->enum('status', ['confirmed','on_way','arrived','started','completed','cancelled']);
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('note')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('trip_id')->references('id')->on('trips')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trip_status_events');
    }
}

