<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_images', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('image_job_id')->nullable()->default(null);
            $table->bigInteger('image_id')->nullable()->default(null);
            $table->integer('amount')->nullable()->default(null);
            $table->integer('commission')->nullable()->default(null);
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
        Schema::dropIfExists('payment_images');
    }
}
