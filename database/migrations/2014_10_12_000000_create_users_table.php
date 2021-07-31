<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('user_name',20)->nullable();;
            $table->string('avatar')->nullable();
            $table->string('email')->unique();
            $table->enum('user_role',['admin','user'])->default('user');;
            $table->timestamp('register_at')->nullable();
            $table->string('password')->nullable();
            $table->integer('otp')->nullable();
            $table->boolean('otpverified')->default(0);
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
        Schema::dropIfExists('users');
    }
}
