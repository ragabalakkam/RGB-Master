<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table)
        {
            $table->id();
            $table->text('name');
            $table->text('slogan')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            # VAT info
            $table->string('tax_number')->nullable();
            $table->string('commercial_reg_no')->nullable();

            # address
            $table->text('address')->nullable();
            $table->text('full_address')->nullable();

            # creator
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            # notes
            $table->text('notes')->nullable();
            $table->text('extra')->nullable();

            # flags
            $table->boolean('online')->default(true);

            # timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
