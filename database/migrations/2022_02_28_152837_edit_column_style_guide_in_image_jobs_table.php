<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditColumnStyleGuideInImageJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('image_jobs')) {
            if (Schema::hasColumn('image_jobs', 'style_guide')) {
                Schema::table('image_jobs', function (Blueprint $table) {
                    $table->string('style_guide')->nullable()->default(null)->change();
                });
            }
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
            if (Schema::hasColumn('image_jobs', 'style_guide')) {
                Schema::table('image_jobs', function (Blueprint $table) {
                    $table->string('style_guide')->change();
                });
            }
        }
    }
}
