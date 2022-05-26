<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnMessageInImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('images')) {
            if (!Schema::hasColumn('images', 'message')) {
                Schema::table('images', function (Blueprint $table) {
                    $table->text('message')->nullable()->default(null)->after('decline');
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
            if (Schema::hasColumn('images', 'message')) {
                Schema::table('images', function (Blueprint $table) {
                    $table->dropColumn('message');
                });
            }
        }
    }
}
