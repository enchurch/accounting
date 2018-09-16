<?php
/**
 * Copyright (c) 2018.
 *
 * This file is part of Enchurch
 *
 * Enchurch is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * Enchurch is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 * PURPOSE.  See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public
 * License along with Enchurch.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Enchurch is based on Jethro PMM, originally written by Tom Barrett.
 * Enchurch is written by Mark Sersen
 *
 */

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
        Schema::create('accounting_journals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ledger_id')->unsigned()->nullable();
            $table->bigInteger('balance');
            $table->string('currency',5);
            $table->string('morphed_type',32);
            $table->integer('morphed_id');
            $table->timestamps();
        });

        Schema::create('accounting_journal_transactions', function (Blueprint $table) {
            $table->char('id',36)->unique();
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

            $table->foreign('journal_id', 'journaltrans_journal')->references('id')->on('accounting_journals');
        });


    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounting_journal_transactions');
        Schema::dropIfExists('accounting_journals');
    }
}
