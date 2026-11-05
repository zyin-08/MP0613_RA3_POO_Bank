<?php
namespace ComBank\Transactions;

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\Transactions\Contracts\BankTransactionInterface;

// Interés: la cantidad no se indica, se calcula a partir de la cuenta
class InterestTransaction extends BaseTransaction implements BankTransactionInterface
{
    // Sobrescribe el constructor del padre: recibe la cuenta en lugar de una cantidad
    public function __construct(BankAccountInterface $account)
    {
        $this->amount = $this->calculateInterest($account);
    }

    public function applyTransaction(BankAccountInterface $account): float
    {
        // Recalcula por si el saldo ha cambiado desde que se creó la transacción
        $this->amount = $this->calculateInterest($account);
        return $account->getBalance() + $this->amount;
    }

    public function getTransactionInfo(): string
    {
        return "INTEREST_TRANSACTION";
    }

    // Interés = saldo * tasa / 100. La tasa la da la estrategia de descubierto
    private function calculateInterest(BankAccountInterface $account): float
    {
        $interest = $account->getBalance() * $account->getOverdraft()->getInterestRate() / 100;
        // Con saldo 0 o negativo no hay interés: se lanza una excepción.
        if ($interest <= 0.0) {
            throw new ZeroAmountException("Interest can only be applied to a positive balance");
        }
        return $interest;
    }
}
