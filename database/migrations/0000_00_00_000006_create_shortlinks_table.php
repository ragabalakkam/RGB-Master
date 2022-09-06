<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShortlinksTable extends Migration
{
    public function up()
    {
        Schema::create('shortlinks', function (Blueprint $table) {
            $table->string('short')->primary();
            $table->string('link');
            $table->boolean('only_one_visit')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('visited_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('shortlinks');
    }
}
