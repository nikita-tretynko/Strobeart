<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoneyTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('money_transfers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->default(null)->nullable();
            $table->bigInteger('payment_id')->nullable()->default(null);
            $table->bigInteger('image_job_id')->nullable()->default(null);
            $table->integer('amount')->default(null)->nullable();
            $table->integer('net')->default(null)->nullable();
            $table->integer('fee')->default(null)->nullable();
            $table->text('description')->default(null)->nullable();
            $table->string('status')->default(null)->nullable();
            $table->string('type_balance')->nullable()->default(null);
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
        Schema::dropIfExists('money_transfers');
    }
}
