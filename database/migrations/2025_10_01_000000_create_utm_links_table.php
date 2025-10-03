<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('utm_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('author')->nullable();            // utm_source
            $table->string('title')->nullable();
            $table->string('slug')->nullable();              // utm_content
            $table->string('resource_type')->nullable();     // utm_medium
            $table->string('campaign')->nullable();          // utm_campaign
            $table->string('original_url', 2048);
            $table->string('utm_url', 2048);
            $table->text('context_text')->nullable();        // only if from paragraph
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('utm_links');
    }
};

