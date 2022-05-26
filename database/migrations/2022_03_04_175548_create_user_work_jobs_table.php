<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserWorkJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_work_jobs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('image_jobs_id')->default(null)->nullable();
            $table->bigInteger('user_id')->default(null)->nullable();
            $table->string('status')->default(null)->nullable();
            $table->string('timer')->default(null)->nullable();
            $table->integer('add_time')->default(null)->nullable();
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
        Schema::dropIfExists('user_work_jobs');
    }
}
