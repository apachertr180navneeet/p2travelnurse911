<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSendJobOpportunitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('send_job_opportunities', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('job_opportunity_id')->default(0);
        //     $table->unsignedBigInteger('job_request_id')->default(0);
        //     $table->unsignedBigInteger('user_id')->default(0);
        //     $table->string('response',50)->default('Active');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('send_job_opportunities');
    }
}
