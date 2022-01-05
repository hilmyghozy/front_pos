<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThirdpartyToTablePosFrontPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_front_payment', function (Blueprint $table) {
            $table->integer('subthirdparty');
            $table->integer('subpajak');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pos_front_payment', function (Blueprint $table) {
             $table->dropColumn('subthirdparty');
             $table->dropColumn('subpajak');
        });
    }
}
