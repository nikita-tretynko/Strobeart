<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublishingFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publishing_files', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable()->default(null);
            $table->bigInteger('image_jobs_id')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->boolean('type')->nullable()->default(null);
            $table->text('alt_text')->nullable()->default(null);
            $table->text('hashtags')->nullable()->default(null);
            $table->string('link')->nullable()->default(null);
            $table->boolean('auto_post_facebook')->nullable()->default(null);
            $table->text('tag_users')->nullable()->default(null);
            $table->text('tag_products')->nullable()->default(null);
            $table->text('search_tag')->nullable()->default(null);
            $table->text('pin_location')->nullable()->default(null);
            $table->string('date_publication')->nullable()->default(null);
            $table->string('platform')->nullable()->default(null);
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
        Schema::dropIfExists('publishing_files');
    }
}
