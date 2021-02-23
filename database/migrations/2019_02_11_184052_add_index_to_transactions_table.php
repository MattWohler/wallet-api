<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['account', 'type']);
            $table->index('created_at');
            $table->index('provider_transaction_id');
            $table->index('round_id');
            $table->index('wallet_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['account', 'type']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['provider_transaction_id']);
            $table->dropIndex(['round_id']);
            $table->dropIndex(['wallet_transaction_id']);
        });
    }
}
