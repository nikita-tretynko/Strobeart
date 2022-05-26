<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnApprovalInImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('images')) {
            if (!Schema::hasColumn('images', 'approval')) {
                Schema::table('images', function (Blueprint $table) {
                    $table->boolean('approval')->default(false)->after('image_job_id');
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
        if (Schema::hasTable('images')) {
            if (Schema::hasColumn('images', 'approval')) {
                Schema::table('images', function (Blueprint $table) {
                    $table->dropColumn('approval');
                });
            }
        }
    }
}
