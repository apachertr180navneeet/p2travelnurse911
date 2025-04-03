<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->string('country_code', 50)->default('971');
            $table->integer('role_id')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->text('address')->nullable();
            $table->string('profile_pic', 255)->nullable();
            $table->tinyInteger('status')->default(0)->comment = '0:Inactive, 1:Active, 2:Deactive';
            $table->text('deactive_reason')->nullable();
            $table->rememberToken();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('users');
    }
}
