<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('country_id')->unsigned();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('no action');

            $table->integer('region_id')->unsigned();
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('no action');

            $table->integer('town_id')->unsigned();
            $table->foreign('town_id')->references('id')->on('towns')->onDelete('no action');

            $table->string('url');

            $table->string('name');
            $table->string('level', 100);
            $table->string('inn', 30);
            $table->string('kpp', 30);
            $table->string('ogrn', 30);
            $table->string('okato', 30);
            $table->string('postal_code');
            $table->string('address');
            $table->string('contact_name');
            $table->string('contact_address');
            $table->string('contact_fax');
            $table->string('contact_phone');
            $table->string('contact_email');
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
        Schema::drop('organizations');
    }
}
