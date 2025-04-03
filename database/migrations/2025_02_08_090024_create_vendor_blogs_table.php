<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_agencies_id')->constrained('vendor_agencies')->onDelete('cascade');
            $table->string('title');
            $table->text('desc')->nullable();
            $table->string('logo')->nullable();
            $table->longText('content')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_blogs');
    }
}
