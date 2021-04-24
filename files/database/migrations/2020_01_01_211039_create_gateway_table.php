<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGatewayTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
   public function up()
   {
         Schema::create('gateways', function (Blueprint $table) {
             $table->increments('id');
             $table->text('email');
             $table->string('gateway');
             $table->unsignedInteger('enabled')->nullable();
             $table->text('api');
             $table->text('private_key');
             $table->text('name');
             $table->float('min_basket');
             $table->float('max_basket');
             $table->float('percentage');
             $table->float('amount');
             $table->text('service_id');
         });
   }
   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
         Schema::dropIfExists('gateways');
   }
}
