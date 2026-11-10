<?php
namespace ComBank\Transactions;

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Transactions\Contracts\BankTransactionInterface;

// Transferencia: saca el dinero de la cuenta origen y lo ingresa en la cuenta destino
class TransferTransaction extends BaseTransaction implements BankTransactionInterface
{
    private BankAccountInterface $destinationAccount;

    // El padre valida que la cantidad sea un número finito mayor que 0
    public function __construct(float $amount, BankAccountInterface $destinationAccount)
    {
        parent::__construct($amount);
        $this->destinationAccount = $destinationAccount;
    }

    public function applyTransaction(BankAccountInterface $account): float
    {
        // Origen y destino iguales: BankAccount guardaría el saldo del origen encima del
        // ingreso y el dinero desaparecería
        if ($account === $this->destinationAccount) {
            throw new InvalidArgsException("Source and destination accounts must be different");
        }

        // El origen debe estar abierto aunque se llame a este método sin pasar por BankAccount
        if (!$account->isOpen()) {
            throw new BankAccountException("Cannot perform transactions on a closed account");
        }

        // WithdrawTransaction comprueba los fondos con la estrategia de descubierto del origen
        $withdraw = new WithdrawTransaction($this->amount);
        $newBalance = $withdraw->applyTransaction($account);

        // Si el destino rechaza el ingreso (cuenta cerrada) salta una excepción y el origen no cambia
        $this->destinationAccount->transaction(new DepositTransaction($this->amount));

        // Hace falta si se llama a este método directamente: sin esto el destino recibiría el dinero
        // y el origen no lo perdería. Por BankAccount::transaction() es redundante, porque después
        // guarda este mismo saldo
        $account->setBalance($newBalance);

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

    public function setDestinationAccount(BankAccountInterface $account): void
    {
        $this->destinationAccount = $account;
    }
}
