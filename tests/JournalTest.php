<?php

// ensure we load our base file (PHPStorm Bug when using remote interpreter )
require_once ('BaseTest.php');

use Money\Money;
use Scottlaurent\Accounting\Models\Journal;

use Models\User;
use Models\Account;
use Models\Product;


/**
 * Class JournalTest
 */
class JournalTest extends BaseTest
{

    public function testX() {

        $j = new Journal();
        $j->setCurrency('USD');
        $z = $j->getBalanceDollars();
        $this->assertEquals(0, $z);

        $q = Journal::class;
        $tt = new $q();
        $mm = 5;
    }
	/**
	 *
	 */
	public function testJournals()
	{
		
		// create some sample model types that will have journals
		$user = $this->createFakeUser();

		// initialize journals for these models
		$user->initJournal();
		
        // we have created journals
        $this->assertInstanceOf(Journal::class, User::find($user->id)->journal);
        
        // we get money balances for our journals
        $this->assertInstanceOf(Money::class, User::find($user->id)->journal->balance);
        
        // our journals have a zero balance
        $this->assertEquals(0,User::find($user->id)->journal->balance->getAmount());
		
        $user_journal = User::find($user->id)->journal;
		
        // we can credit a journal and get back dollar balances and standard Money balances
        $user_journal->creditDollars(100);
		
        $this->assertEquals(100,$user_journal->getCurrentBalanceDollars());
		$this->assertEquals(100,$user_journal->getDollarsCreditedToday());
		$this->assertEquals(0,$user_journal->getDebitedDollarsToday());
        $this->assertEquals(10000,$user_journal->getCurrentBalance()->getAmount());
		
        // we can debit a journal
        $user_journal = User::find($user->id)->journal;
        $user_journal->debitDollars(100.99);
        
        $this->assertEquals(100.99,$user_journal->getDebitedDollarsToday());
		$this->assertEquals(-0.99,$user_journal->getCurrentBalanceDollars());
		$this->assertEquals(-99,$user_journal->getCurrentBalance()->getAmount());
		
	}
	
	/**
	 *
	 */
	public function testJournalObjectReferences()
	{
		
		/*
		|--------------------------------------------------------------------------
		| setup
		|--------------------------------------------------------------------------
		*/
		
		$user = $this->createFakeUser();
		$user->initJournal();
		$user_journal = $user->fresh()->journal;
		
		$account = $this->createFakeAccount();
		$account->initJournal();
		$account_journal = Account::find(1)->journal;
		
		$product = Product::create(['name'=>'Product 1','price'=> mt_rand(1,99999)]);
		$qty_products = mt_rand(25000,100000);
		
		// credit the account journal for some products that have been purchased.
        $a_transaction = $account_journal->creditDollars($product->price * $qty_products);
        
        // reference the product inside this transaction
        $a_transaction->referencesObject($product);
        
		// debit the user journal for some products that have been purchased.
        $u_transaction = $user_journal->debitDollars($product->price * $qty_products);
        
        // reference the product inside this transaction
        $u_transaction->referencesObject($product);
        
		/*
		|--------------------------------------------------------------------------
		| assertions
		|--------------------------------------------------------------------------
		*/
		
        // make sure that the amount credited is correct...
		$this->assertEquals($product->price * $qty_products,$account_journal->getCurrentBalanceDollars(),"Product Purchase Income");
		
		// and also that the referenced product can be retrieved from the transaction
		$this->assertInstanceOf($a_transaction->ref_class,$a_transaction->getReferencedObject());
		$this->assertEquals($a_transaction->getReferencedObject()->fresh(),$product->fresh());
		
		// make sure that the amount debited is correct...
		$this->assertEquals(-1 * $product->price * $qty_products,$user_journal->getCurrentBalanceDollars(),"Products Purchased");
		
		// and also that the referenced product can be retrieved from the transaction
		$this->assertInstanceOf($u_transaction->ref_class,$u_transaction->getReferencedObject());
		
		$this->assertEquals($u_transaction->getReferencedObject()->fresh(),$product->fresh());
	}

	public function testJournalTransactionSoftDeletes() {

        $user = $this->createFakeUser();
        $user->initJournal();
        $user_journal = $user->fresh()->journal;

        $account = $this->createFakeAccount();
        $account->initJournal();
        $account_journal = Account::find(1)->journal;

        $a_transaction = $account_journal->creditDollars(12.50);

        $this->assertEquals(12.50, $account_journal->getCurrentBalanceDollars());

        $a_transaction->delete();
        $this->assertEquals(0, $account_journal->getCurrentBalanceDollars());

        $this->assertTrue($a_transaction->trashed());
        $this->assertEquals(0, \Scottlaurent\Accounting\Models\JournalTransaction::get()->count() );
        $this->assertEquals(1, \Scottlaurent\Accounting\Models\JournalTransaction::withTrashed()->get()->count() );

    }

    public function testSetCurrency()
    {

        $user = $this->createFakeUser();
        $user->initJournal();
        $user_journal = $user->fresh()->journal;
        $user_journal->setCurrency('CAD');
        $user_journal->save();

        $fresh_journal = $user->fresh()->journal;
        $this->assertEquals('CAD', $fresh_journal->currency);

    }


    }