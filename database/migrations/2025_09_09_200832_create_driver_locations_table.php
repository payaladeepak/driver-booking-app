cat > /home/u608956572/domains/wow.dukandar.online/public_html/wow/database/migrations/2025_09_09_200832_create_driver_locations_table.php <<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverLocationsTable extends Migration
{
    public function up()
    {
        Schema::create('driver_locations', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_id')->primary();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->integer('heading')->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->boolean('is_online')->default(false)->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_locations');
    }
}

