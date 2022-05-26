<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStyleGuidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('style_guides', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->string('name')->nullable()->default(null);
            $table->string('remove_background')->nullable()->default(null);
            $table->string('size_with')->nullable()->default(null);
            $table->string('size_height')->nullable()->default(null);
            $table->string('unit_measurement')->nullable()->default(null);
            $table->string('file_format')->nullable()->default(null);
            $table->string('color_profile')->nullable()->default(null);
            $table->string('resolution')->nullable()->default(null);
            $table->string('resolution_units')->nullable()->default(null);
            $table->string('other')->nullable()->default(null);
            $table->string('uploaded_logo')->nullable()->default(null);
            $table->string('upload_watermark')->nullable()->default(null);
            $table->string('video_instructions')->nullable()->default(null);
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
        Schema::dropIfExists('style_guides');
    }
}
