<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstagramConnectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instagram_connects', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->default(null)->nullable();
            $table->text('token')->default(null)->nullable();
            $table->string('page_id')->default(null)->nullable();
            $table->string('instagram_id')->default(null)->nullable();
            $table->string('token_expires')->default(null)->nullable();
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
        Schema::dropIfExists('instagram_connects');
    }
}
