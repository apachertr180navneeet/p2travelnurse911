<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorDirectoryCompanyDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_directory_company_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_categories_id')->constrained('vendor_categories')->onDelete('cascade');
            $table->string('company_name');
            $table->string('email');
            $table->string('phone');
            $table->text('address')->nullable();
            $table->text('about')->nullable();
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
        Schema::dropIfExists('vendor_directory_company_details');
    }
}
