<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMigrationCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('migration_customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('username')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('user_type');
            $table->string('mobile')->nullable()->unique();
            $table->string('msisdn', 15)->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->integer('status');
            $table->integer('password_grant')->default(0)->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('profile_image')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->string('alternate_phone')->nullable();
            $table->text('address')->nullable();
            $table->longText('profile_image_base64')->nullable();
            $table->boolean('is_password_set')->default(false);
            $table->rememberToken();
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
        Schema::dropIfExists('migration_customers');
    }
}
