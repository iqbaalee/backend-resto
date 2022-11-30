<?php

use App\Models\Customer;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 40);
            $table->enum('status', ['initial', 'down_payment', 'paid', 'done', 'cancel']);
            $table->date('order_date');
            $table->decimal('down_payment', 8, 2);;
            $table->foreignIdFor(Customer::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
