<?php
namespace ComBank\Bank;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:25 PM
 */

use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\Support\Traits\AmountValidationTrait;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class BankAccount implements BankAccountInterface
{
    use AmountValidationTrait;

    private float $balance;
    private bool $status;
    private OverdraftInterface $overdraft;

    public function __construct(float $balance)
    {
        $this->balance = $balance;
        $this->status = true;
        $this->overdraft = new NoOverdraft();
    }

    public function transaction(BankTransactionInterface $transaction)
    {
        if (!$this->status) {
            throw new BankAccountException("Cannot perform transactions on a closed account");
        }
        $this->validateAmount($transaction->getAmount());

        try {
            $newBalance = $transaction->applyTransaction($this);
        } catch (InvalidOverdraftFundsException $e) {
            throw new FailedTransactionException("Insufficient balance to complete the transaction", 0, $e);
        }

        $this->setBalance($newBalance);
    }

    public function isOpen(): bool
    {
        return $this->status;
    }

    public function reopenAccount()
    {
        if ($this->status) {
            throw new BankAccountException("The account is already open");
        }
        $this->setStatus(true);
    }

    public function closeAccount()
    {
        if (!$this->status) {
            throw new BankAccountException("The account is already closed");
        }
        $this->setStatus(false);
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance)
    {
        $this->balance = $balance;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status)
    {
        $this->status = $status;
    }

    public function getOverdraft(): OverdraftInterface
    {
        return $this->overdraft;
    }

    public function setOverdraft(OverdraftInterface $overdraft)
    {
        $this->overdraft = $overdraft;
    }

    public function applyOverdraft(OverdraftInterface $overdraft)
    {
        $this->setOverdraft($overdraft);
    }
}
