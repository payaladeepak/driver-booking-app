cat > /home/u608956572/domains/wow.dukandar.online/public_html/wow/database/migrations/2025_09_09_200833_create_payments_table.php <<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->nullable()->index();
            $table->unsignedBigInteger('trip_id')->nullable()->index();
            $table->decimal('amount', 10, 2);
            $table->enum('method', ['razorpay','cod','wallet'])->default('razorpay');
            $table->string('gateway_payment_id')->nullable();
            $table->enum('status', ['pending','success','failed','refunded'])->default('pending')->index();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('bookings')->nullOnDelete();
            $table->foreign('trip_id')->references('id')->on('trips')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}

