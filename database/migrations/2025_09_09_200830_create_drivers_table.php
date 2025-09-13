cat > /home/u608956572/domains/wow.dukandar.online/public_html/wow/database/migrations/2025_09_09_200831_create_drivers_table.php <<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriversTable extends Migration
{
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('name')->nullable();
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->enum('vehicle_type', ['bike','erickshaw','car','personal'])->default('bike')->index();
            $table->string('vehicle_number')->nullable();
            $table->string('license_number')->nullable();
            $table->string('profile_photo')->nullable();
            $table->enum('status', ['pending','verified','rejected','suspended'])->default('pending')->index();
            $table->boolean('is_available')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('drivers');
    }
}

