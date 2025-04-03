<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSocialMediaFieldsToVendorAgenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_agencies', function (Blueprint $table) {
            $table->string("facebook")->nullable()->default(null)->after('desc');
            $table->string("twitter")->nullable()->default(null)->after('desc');
            $table->string("instagram")->nullable()->default(null)->after('desc');
            $table->string("linkedin")->nullable()->default(null)->after('desc');
            $table->string("youtube")->nullable()->default(null)->after('desc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_agencies', function (Blueprint $table) {
            //
        });
    }
}
