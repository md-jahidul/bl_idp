<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientScopeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_scope', function (Blueprint $table) {
            $table->unsignedBigInteger('client_user_id');
            $table->unsignedBigInteger('scope_id');
        });

        Schema::table('client_scope', function (Blueprint $table) {
            $table->foreign('client_user_id')
                ->references('id')->on('users');
        });

        Schema::table('client_scope', function (Blueprint $table) {
            $table->foreign('scope_id')
                ->references('id')->on('scopes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_scope');
    }
}
