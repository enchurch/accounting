<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUsersTable
 */
class CreateAccountingJournalTransactionsTable extends Migration
{
    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ledger_id')->unsigned()->nullable();
            $table->bigInteger('balance');
            $table->string('currency',5);
            $table->string('morphed_type',32);
            $table->integer('morphed_id');
            $table->timestamps();
        });

        Schema::create('journal_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->char('trans_id',36)->unique();
            $table->char('transaction_group',36)->nullable();
            $table->integer('journal_id')->unsigned();
            $table->bigInteger('debit')->nullable();
            $table->bigInteger('credit')->nullable();
            $table->char('currency',5);
            $table->text('memo')->nullable();
            $table->text('tags')->nullable();
            $table->char('ref_class',32)->nullable();
            $table->integer('ref_class_id')->unsigned()->nullable();
            $table->timestamps();
            $table->dateTime('post_date');
            $table->softDeletes();

            $table->foreign('journal_id', 'journaltrans_journal')->references('id')->on('journals');
        });


    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_transactions');
        Schema::dropIfExists('journals');
    }
}
