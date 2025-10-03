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
            $table->string('author')->nullable();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->string('resource_type')->nullable();
            $table->string('campaign')->nullable();
            $table->string('original_url', 2048);
            $table->string('utm_url', 2048);
            $table->text('context_text')->nullable();
            $table->string('created_by_ip',45)->nullable();
            $table->timestamps();

            $table->index('author');
            $table->index('campaign');
            $table->index(['created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('utm_links');
    }
};
