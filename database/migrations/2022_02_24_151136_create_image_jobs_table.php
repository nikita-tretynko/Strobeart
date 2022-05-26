<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImageJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('image_jobs')) {
            Schema::create('image_jobs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('editing_level');
                $table->string('style_guide');
                $table->string('color_profile');
                $table->string('file_format');
                $table->string('other')->nullable()->default(null);
                $table->boolean('add_logo')->default(false);
                $table->boolean('add_watermark')->default(false);
                $table->boolean('white_background')->default(false);
                $table->boolean('red_eye')->default(false);
                $table->timestamp('due_date');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('image_jobs')) {
            Schema::dropIfExists('image_jobs');
        }
    }
}
