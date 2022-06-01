<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProperosCreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname',100)->nullable();
            $table->string('lastname',100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->index();
            $table->string('company', 100)->nullable();
            $table->string('password');
            $table->string('status')->nullable()->default('active');
            $table->unsignedInteger('affiliate_id')->nullable()->default(0);
            $table->decimal('percent',10,2)->nullable()->default(0);
            $table->decimal('pending_balance')->nullable()->default(0);
            $table->decimal('available_balance')->nullable()->default(0);
            $table->string('avatar')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->SoftDeletes();
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
