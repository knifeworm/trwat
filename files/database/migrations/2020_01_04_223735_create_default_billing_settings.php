<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultBillingSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::table('billing')->insert(
          array(
              'currency' => 'euro',
              'use_categories' => '0',
              'tos' => 'This is the example TOS, please change this.',
          )
      );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      DB::table('billing')->where('id', '=', '1')->delete();
    }
}
