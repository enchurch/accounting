<?php

namespace Scottlaurent\Accounting\ModelTraits;

use Scottlaurent\Accounting\Models\Journal;

/**
 * Class AccountingJournal
 * @package Scottlaurent\Accounting\ModelTraits
 */
trait AccountingJournal {


    /**
     * Override this if subclassing Journal
     * @return string
     */
    protected function getJournalClass(): string
    {
        return config('accounting.models.journal');
    }


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
            $journalClass = $this->getJournalClass();
            $journal = new $journalClass();
	        $journal->currency = $currency_code;
	        $journal->balance = 0;
	        return $this->journal()->save($journal);
	    }
		throw new \Exception('Journal already exists.');
    }
	
}