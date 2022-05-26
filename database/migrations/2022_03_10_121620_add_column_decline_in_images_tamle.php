<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDeclineInImagesTamle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('images')) {
            if (!Schema::hasColumn('images', 'decline')) {
                Schema::table('images', function (Blueprint $table) {
                    $table->boolean('decline')->default(false)->after('approval');
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
            if (Schema::hasColumn('images', 'decline')) {
                Schema::table('images', function (Blueprint $table) {
                    $table->dropColumn('decline');
                });
            }
        }
    }
}
