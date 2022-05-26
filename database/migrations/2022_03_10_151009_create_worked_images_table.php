<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkedImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('worked_images', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->bigInteger('user_work_id')->nullable()->default(null);
            $table->bigInteger('image_jobs_id')->nullable()->default(null);
            $table->bigInteger('image_id')->nullable()->default(null);
            $table->text('image_url')->nullable()->default(null);
            $table->string('status')->nullable()->default(null);
            $table->string('timer')->default(null)->nullable();
            $table->integer('add_time')->default(null)->nullable();
            $table->integer('number_file')->default(null)->nullable();
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
        Schema::dropIfExists('worked_images');
    }
}
