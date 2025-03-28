<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToPaymentLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_links', function (Blueprint $table) {
            $table->string('trailer_id')->nullable()->after('certificate');
            $table->string('vehicle_data')->nullable()->after('trailer_id');
            $table->string('vehicle_insured')->nullable()->after('vehicle_data');
            $table->string('vehicle_owner')->nullable()->after('vehicle_insured');
            $table->string('name')->nullable()->after('vehicle_owner');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_links', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('certificate');
            $table->dropColumn('trailer_id');
            $table->dropColumn('vehicle_data');
            $table->dropColumn('vehicle_insured');
            $table->dropColumn('vehicle_owner');
            $table->dropColumn('name');
        });
    }
}
