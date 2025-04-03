<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('job_requests', function (Blueprint $table) {
        //     $table->id();
        //     $table->bigInteger('user_id');
        //     $table->bigInteger('profession_id')->nullable();
        //     $table->bigInteger('speciality_id')->nullable();
        //     $table->tinyInteger('flexible')->default(0);
        //     $table->date('start_date')->nullable();
        //     $table->date('end_date')->nullable();
        //     $table->integer('shift_id')->nullable();
        //     $table->double('pay_rate')->nullable();
        //     $table->string('pay_type')->nullable();
        //     $table->integer('employment_type_id')->nullable();
        //     $table->integer('user_document_id')->nullable();
        //     $table->string('file')->nullable();
        //     $table->tinyInteger('status')->default(1);
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
        // Schema::dropIfExists('job_requests');
    }
}
