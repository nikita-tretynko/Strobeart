<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->bigInteger('to_user_id')->nullable()->default(null);
            $table->text('message')->nullable()->default(null);
            $table->integer('rating')->nullable()->default(null);
            $table->string('type')->nullable()->default(null);
            $table->bigInteger('job_image_id')->nullable()->default(null);
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
        Schema::dropIfExists('reviews');
    }
}
