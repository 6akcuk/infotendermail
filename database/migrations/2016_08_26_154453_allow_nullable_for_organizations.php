<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AllowNullableForOrganizations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->integer('country_id')->unsigned()->nullable(true)->change();
            $table->integer('region_id')->unsigned()->nullable(true)->change();
            $table->integer('town_id')->unsigned()->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->integer('country_id')->unsigned()->nullable(false)->change();
            $table->integer('region_id')->unsigned()->nullable(false)->change();
            $table->integer('town_id')->unsigned()->nullable(false)->change();
        });
    }
}
