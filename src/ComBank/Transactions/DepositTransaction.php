<?php
namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 11:30 AM
 */

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;

// Ingreso: hereda de BaseTransaction (extends) y cumple la interfaz (implements)
class DepositTransaction extends BaseTransaction implements BankTransactionInterface
{
    public function applyTransaction(BankAccountInterface $account): float
    {
        // Nuevo saldo = saldo actual + cantidad
        return $account->getBalance() + $this->amount;
    }

    public function getTransactionInfo(): string
    {
        return "DEPOSIT_TRANSACTION";
    }
}
