<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobOpportunitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('job_opportunities', function (Blueprint $table) {
        //     $table->id(); // Primary key
        //     $table->unsignedBigInteger('client_id')->default(0); // Reference to user
        //     $table->string('title', 255)->nullable(); // Job opportunity title
        //     $table->unsignedBigInteger('profession_id')->default(0); // Profession ID
        //     $table->unsignedBigInteger('speciality_id')->default(0); // Specialty ID
        //     $table->date('start_date')->nullable(); // Start date
        //     $table->unsignedBigInteger('shift_id')->default(0); // Shift ID
        //     $table->unsignedBigInteger('state_id')->default(0); // State ID
        //     $table->unsignedBigInteger('city_id')->default(0); // City ID
        //     $table->unsignedBigInteger('employment_type_id')->default(0); // Employment type ID
        //     $table->double('pay_rate')->nullable();
        //     $table->string('pay_type')->nullable();
        //     $table->integer('status')->default(1);
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
        // Schema::dropIfExists('job_opportunities');
    }
}
