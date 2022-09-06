<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->enum('role', ['employee', 'customer'])->default('customer');

            # email
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();

            # phone
            $table->string('phone')->nullable();
            $table->timestamp('phone_verified_at')->nullable();

            # address
            $table->text('address')->nullable();
            $table->text('full_address')->nullable();
            $table->text('location')->nullable();

            # locale (preferences)
            $table->unsignedBigInteger('locale_id')->nullable()->default(1);
            $table->foreign('locale_id')->references('id')->on('locales')->onUpdate('cascade')->onDelete('set null');

            # notes
            $table->text('notes')->nullable();
            $table->text('extra')->nullable();

            # timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
