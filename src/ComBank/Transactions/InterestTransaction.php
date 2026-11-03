<?php
namespace ComBank\Transactions;

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class InterestTransaction extends BaseTransaction implements BankTransactionInterface
{
    public function __construct(BankAccountInterface $account)
    {
        $this->amount = $this->calculateInterest($account);
    }

    public function applyTransaction(BankAccountInterface $account): float
    {
        $this->amount = $this->calculateInterest($account);
        return $account->getBalance() + $this->amount;
    }

    public function getTransactionInfo(): string
    {
        return "INTEREST_TRANSACTION";
    }

    private function calculateInterest(BankAccountInterface $account): float
    {
        $interest = $account->getBalance() * $account->getOverdraft()->getInterestRate() / 100;
        if ($interest <= 0.0) {
            throw new ZeroAmountException("Interest can only be applied to a positive balance");
        }
        return $interest;
    }
}
