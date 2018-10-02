<?php

namespace Scottlaurent\Accounting\Models;

use Illuminate\Database\Eloquent\Model;
use Money\Money;
use Money\Currency;
use Carbon\Carbon;

/**
 * Class Ledger
 * @package Scottlaurent\Accounting
 * @property    Money                  $balance
 * @property    string                 $currency
 * @property    Carbon                 $updated_at
 * @property    Carbon                 $post_date
 * @property    Carbon                 $created_at
 */
class Ledger extends Model
{
	
	public $currency = 'USD';
	
	/**
	 *
	 */
	public function journals()
	{
		return $this->hasMany(config('accounting.models.journal'));
	}
	
	/**
     * Get all of the posts for the country.
     */
    public function journal_transctions()
    {
        return $this->hasManyThrough(config('accounting.models.journal_transaction'), config('accounting.models.journal'));
    }
	
	/**
	 *
	 */
	public function getCurrentBalance()
	{
		if ($this->type == 'asset' || $this->type == 'expense') {
			$balance = $this->journal_transctions->sum('debit') - $this->journal_transctions->sum('credit');
		} else {
			$balance = $this->journal_transctions->sum('credit') - $this->journal_transctions->sum('debit');
		}
		
		return new Money($balance, new Currency($this->currency));
	}
	
		/**
	 *
	 */
	public function getCurrentBalanceInDollars()
	{
		return $this->getCurrentBalance()->getAmount() / 100;
	}
	
	
}