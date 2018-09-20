<?php

namespace Scottlaurent\Accounting\ModelTraits;

use Scottlaurent\Accounting\Models\Journal;

/**
 * Class AccountingJournal
 * @package Scottlaurent\Accounting\ModelTraits
 */
trait AccountingJournal {


    /**
	 * Morph to Journal.
	 *
	 * @return mixed
	 */
	public function journal()
    {
        return $this->morphOne(config('accounting.models.journal'),'morphed');
    }
	
	/**
	 * Initialize a journal for a given model object
	 *
	 * @param string $currency_code
	 * @return Journal
	 * @throws \Exception
	 */
	public function initJournal($currency_code='USD') {
    	if (!$this->journal) {
            $journalClass = config('accounting.models.journal');
            $journal = new $journalClass();
	        $journal->currency = $currency_code;
	        $journal->balance = 0;
	        return $this->journal()->save($journal);
	    }
		throw new \Exception('Journal already exists.');
    }
	
}