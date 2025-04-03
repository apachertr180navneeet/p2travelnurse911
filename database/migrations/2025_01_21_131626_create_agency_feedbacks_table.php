<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgencyFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agency_feedbacks', function (Blueprint $table) {
            $table->id(); // Auto-increment ID
            $table->unsignedBigInteger('client_id'); // Foreign key to clients table
            $table->unsignedBigInteger('user_id');   // Foreign key to users table
            $table->unsignedTinyInteger('rating');   // Rating (1 to 5)
            $table->text('comments')->nullable();    // Optional comments
            $table->boolean('status')->default(0);   // Status (e.g., active/inactive)
            $table->boolean('is_approved')->default(0); // Approval status
            $table->timestamps(); // Created at and updated at timestamps
            
            // Foreign key constraints
            // $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agency_feedbacks');
    }
}
