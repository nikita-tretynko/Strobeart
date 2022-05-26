<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_jobs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('job_image_id')->nullable()->default(null);
            $table->string('channels_sid')->nullable()->default(null);
            $table->string('channels_name')->nullable()->default(null);
            $table->bigInteger('user_editor_id')->nullable()->default(null);
            $table->bigInteger('user_business_id')->nullable()->default(null);
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
        Schema::dropIfExists('chat_jobs');
    }
}
