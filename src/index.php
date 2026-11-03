<?php

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:24 PM
 */

use ComBank\Bank\BankAccount;
use ComBank\OverdraftStrategy\SilverOverdraft;
use ComBank\OverdraftStrategy\GoldOverdraft;
use ComBank\Transactions\DepositTransaction;
use ComBank\Transactions\InterestTransaction;
use ComBank\Transactions\WithdrawTransaction;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\Exceptions\InvalidArgsException;

require_once 'bootstrap.php';


//---[Bank account 1]---/
// create a new account1 with balance 400
$bankAccount1 = new BankAccount(400);
pl('--------- [Start testing bank account #1, No overdraft] --------');
try {
    // show balance account
    pl("My balance: " . $bankAccount1->getBalance());
    // close account
    $bankAccount1->closeAccount();
    pl("My account is now closed");
    // reopen account
    $bankAccount1->reopenAccount();
    pl("My account is now reopened");


    // deposit +150 
    pl('Doing transaction deposit (+150) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new DepositTransaction(150.0));
    pl('My new balance after deposit (+150) : ' . $bankAccount1->getBalance());

    // withdrawal -25
    pl('Doing transaction withdrawal (-25) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(25.0));
    pl('My new balance after withdrawal (-25) : ' . $bankAccount1->getBalance());

    // withdrawal -600
    pl('Doing transaction withdrawal (-600) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(600.0));
} catch (ZeroAmountException $e) {
    pl($e->getMessage());
} catch (BankAccountException $e) {
    pl($e->getMessage());
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My balance after failed last transaction : ' . $bankAccount1->getBalance());
$bankAccount1->closeAccount();
pl("My account is now closed");



//---[Bank account 2]---/
$bankAccount2 = new BankAccount(200.0);
$bankAccount2->applyOverdraft(new SilverOverdraft());
pl('--------- [Start testing bank account #2, Silver overdraft (100.0 funds)] --------');
try {

    // show balance account
    pl("My balance: " . $bankAccount2->getBalance());
    // deposit +100
    pl('Doing transaction deposit (+100) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new DepositTransaction(100.0));
    pl('My new balance after deposit (+100) : ' . $bankAccount2->getBalance());

    // withdrawal -300
    pl('Doing transaction withdrawal (-300) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(300.0));
    pl('My new balance after withdrawal (-300) : ' . $bankAccount2->getBalance());

    // withdrawal -50
    pl('Doing transaction withdrawal (-50) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(50.0));
    pl('My new balance after withdrawal (-50) with funds : ' . $bankAccount2->getBalance());

    // withdrawal -120
    pl('Doing transaction withdrawal (-120) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(120.0));

} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My balance after failed last transaction : ' . $bankAccount2->getBalance());

try {
    pl('Doing transaction withdrawal (-20) with current balance : ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(20.0));
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My new balance after withdrawal (-20) with funds : ' . $bankAccount2->getBalance());

try {
    $bankAccount2->closeAccount();
    pl("My account is now closed");
    $bankAccount2->closeAccount();
} catch (BankAccountException $e) {
    pl($e->getMessage());
}

//---[Bank account 3]---/
pl('--------- [Start testing bank account #3, Email validation] --------');
try {
    $bankAccount3 = new BankAccount(300.0, 'john.smith@example.com');
    pl('Account created successfully.');
    pl('My balance: ' . $bankAccount3->getBalance());
    pl('My email: ' . $bankAccount3->getEmail());
} catch (InvalidArgsException $e) {
    pl('Account creation rejected: ' . $e->getMessage());
}

try {
    $invalidAccount = new BankAccount(300.0, 'johnexample.com');
    pl('Account created successfully.');
} catch (InvalidArgsException $e) {
    pl('Account creation rejected due to an invalid email format: ' . $e->getMessage());
}

pl('--------- [Start testing bank account #4, Interest transaction] --------');
try {
    $bankAccount4 = new BankAccount(1000.0);
    pl('Doing interest transaction (No overdraft, 1%) with current balance ' . $bankAccount4->getBalance());
    $bankAccount4->transaction(new InterestTransaction($bankAccount4));
    pl('My new balance after interest : ' . $bankAccount4->getBalance());

    $bankAccount5 = new BankAccount(1000.0);
    $bankAccount5->applyOverdraft(new SilverOverdraft());
    pl('Doing interest transaction (Silver overdraft, 5%) with current balance ' . $bankAccount5->getBalance());
    $bankAccount5->transaction(new InterestTransaction($bankAccount5));
    pl('My new balance after interest : ' . $bankAccount5->getBalance());

    $bankAccount6 = new BankAccount(1000.0);
    $bankAccount6->applyOverdraft(new GoldOverdraft());
    pl('Doing interest transaction (Gold overdraft, 10%) with current balance ' . $bankAccount6->getBalance());
    $bankAccount6->transaction(new InterestTransaction($bankAccount6));
    pl('My new balance after interest : ' . $bankAccount6->getBalance());
} catch (ZeroAmountException $e) {
    pl($e->getMessage());
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
