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
        Schema::create('ms_users', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 191)->unique();
            $table->string('nama')->nullable();
            $table->string('no_hp')->unique();;
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role', 191);
            $table->string('key')->nullable();
            $table->string('otp')->nullable();
            $table->text('fcm_token')->nullable();
            $table->integer('reset_pswd_count')->length(2)->nullable();
            $table->date('reset_pswd_at')->nullable();
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
        Schema::dropIfExists('ms_users');
    }
}
