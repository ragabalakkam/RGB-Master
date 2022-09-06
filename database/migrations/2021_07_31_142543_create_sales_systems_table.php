<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesSystemsTable extends Migration
{
    public function up()
    {
        Schema::create('sales_systems', function (Blueprint $table) {
            $table->id();
            $table->text('key');
            $table->text('name');
            $table->text('icon')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('sales_systems');
    }
}
