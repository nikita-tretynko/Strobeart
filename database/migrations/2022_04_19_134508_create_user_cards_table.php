<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_cards', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->default(null)->nullable();
            $table->string('customer')->default(null)->nullable();
            $table->string('card')->default(null)->nullable();
            $table->string('last4')->default(null)->nullable();
            $table->integer('exp_year')->default(null)->nullable();
            $table->integer('exp_month')->default(null)->nullable();
            $table->string('brand')->default(null)->nullable();
            $table->string('country')->default(null)->nullable();
            $table->string('address_zip')->default(null)->nullable();
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
        Schema::dropIfExists('user_cards');
    }
}
