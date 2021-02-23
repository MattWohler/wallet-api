<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wallet_transaction_id');
            $table->float('old_balance');
            $table->float('new_balance');
            $table->string('account', 10);
            $table->string('provider_transaction_id', 100);
            $table->string('round_id', 100);
            $table->float('amount');
            $table->string('type', 50);
            $table->unsignedInteger('provider_id');
            $table->string('provider_game_id', 100);
            $table->text('payload');
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
        Schema::dropIfExists('transactions');
    }
}
