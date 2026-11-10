<?php
namespace ComBank\Transactions;

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class TransferTransaction extends BaseTransaction implements BankTransactionInterface
{
    private $destinationAccount;

    public function __construct(float $amount, BankAccountInterface $destinationAccount)
    {
        parent::__construct($amount);
        $this->destinationAccount = $destinationAccount;
    }

    public function applyTransaction(BankAccountInterface $account): float
    {   
        $withdraw = new WithdrawTransaction($this->amount);
        $newBalance = $withdraw->applyTransaction($account);
        $this->destinationAccount->transaction(new DepositTransaction($this->amount));
        return $newBalance;
    }

    public function getTransactionInfo(): string
    {
        return "TRANSFER_TRANSACTION";
    }

    public function getDestinationAccount(): BankAccountInterface
    {
        return $this->destinationAccount;
    }

    public function setDestinationAccount(BankAccountInterface $account) 
    {
        $this->destinationAccount = $account;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}