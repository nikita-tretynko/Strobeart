<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->bigInteger('client_id')->nullable()->default(null);
            $table->bigInteger('job_image_id')->nullable()->default(null);
            $table->integer('amount')->nullable()->default(null);
            $table->string('receipt_url')->nullable()->default(null);
            $table->string('payment_intent_id')->nullable()->default(null);
            $table->string('type_payment')->nullable()->default(null);
            $table->string('status')->nullable()->default(null);
            $table->integer('money_back')->nullable()->default(null);
            $table->string('created')->nullable()->default(null);
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
        Schema::dropIfExists('payments');
    }
}
