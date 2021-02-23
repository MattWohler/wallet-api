<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('api_tokens')) {
            return;
        }

        Schema::create('api_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('target');
            $table->string('token', 300)->unique();
            $table->json('scopes');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['name', 'target']);
            $table->index(['token', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_token');
    }
}
