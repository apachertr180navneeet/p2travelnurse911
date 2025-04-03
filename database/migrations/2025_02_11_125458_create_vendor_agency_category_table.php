<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorAgencyCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_agency_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_agencies_id')->constrained('vendor_agencies')->onDelete('cascade');
            $table->foreignId('vendor_categories_id')->constrained('vendor_categories')->onDelete('cascade');
            $table->json('vendor_subcategories_ids'); // Store multiple subcategory IDs as JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_agency_category');
    }
}
