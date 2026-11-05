<?php
namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 1:22 PM
 */

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Transactions\Contracts\BankTransactionInterface;

// Retirada: resta la cantidad si la estrategia de descubierto lo permite
class WithdrawTransaction extends BaseTransaction implements BankTransactionInterface
{
    public function applyTransaction(BankAccountInterface $account): float
    {
        // Calcula el saldo que quedaría
        $newBalance = $account->getBalance() - $this->amount;
        // Pregunta a la estrategia de la cuenta si ese saldo está permitido
        if (!$account->getOverdraft()->isGrantOverdraftFunds($newBalance)) {
            throw new InvalidOverdraftFundsException("Insufficient balance to complete the withdrawal");
        }
        return $newBalance;
    }

    public function getTransactionInfo(): string
    {
        return "WITHDRAW_TRANSACTION";
    }
}
