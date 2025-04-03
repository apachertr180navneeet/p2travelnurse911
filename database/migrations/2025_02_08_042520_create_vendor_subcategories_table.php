<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorSubCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_subcategories', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->string("slug");
            $table->foreignId("vendor_category_id");
            $table->longText("description")->nullable()->default(null);
            $table->string("image")->nullable()->default(null);
            $table->boolean("status")->default(true);
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
        Schema::dropIfExists('vendor_subcategories');
    }
}
