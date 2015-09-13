<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleUserTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('role_user', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('role_id')->unsigned();
      $table->integer('user_id')->unsigned();

      $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });

    DB::table('role_user')->insert([
      'role_id' => 1,
      'user_id' => 1
    ]);
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('role_user');
  }
}
