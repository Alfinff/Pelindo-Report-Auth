<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_profile', function (Blueprint $table) {
            $table->id();
            $table->string('foto')->nullable();
            $table->date('tgllahir')->nullable();
            $table->string('jenis_kelamin')->default('LAKI-LAKI')->nullable();
            $table->string('alamat')->nullable();
            $table->string('user_id', 191);
            $table->string('uuid', 191)->unique();
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
        Schema::dropIfExists('ms_profile');
    }
}
