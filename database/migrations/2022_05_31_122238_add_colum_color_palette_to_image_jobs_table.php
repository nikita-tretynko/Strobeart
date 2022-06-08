<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumColorPaletteToImageJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('image_jobs', function (Blueprint $table) {
            $table->bigInteger('color_palette')->nullable()->default(null);
            $table->bigInteger('typography')->nullable()->default(null);
            $table->bigInteger('file_id_video_instruction')->nullable()->default(null);
            $table->bigInteger('add_logo')->change();
            $table->bigInteger('add_watermark')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('image_jobs', function (Blueprint $table) {
            $table->dropColumn('color_palette');
            $table->dropColumn('typography');
            $table->dropColumn('file_id_video_instruction');
            $table->tinyInteger('add_logo')->change();
            $table->tinyInteger('add_watermark')->change();
        });
    }
}
