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

// Cuenta bancaria: guarda el saldo, el estado y el descubierto, y ejecuta las transacciones
class BankAccount implements BankAccountInterface
{
    // Trait: añade a esta clase validateAmount() y validateEmail() sin usar herencia
    use AmountValidationTrait;

    private float $balance;
    private bool $status;
    private OverdraftInterface $overdraft;
    private string $email = '';

    // Constructor: el email es opcional. Toda cuenta nace abierta y sin descubierto
    public function __construct(float $balance, $email = null)
    {
        // Si el email no es válido se lanza una excepción y la cuenta no se crea
        if ($email !== null) {
            $this->setEmail($email);
        }
        $this->balance = $balance;
        $this->status = true;
        $this->overdraft = new NoOverdraft();
    }

    // Método central: acepta cualquier transacción gracias a la interfaz
    public function transaction(BankTransactionInterface $transaction)
    {
        // 1. Una cuenta cerrada no admite transacciones
        if (!$this->status) {
            throw new BankAccountException("Cannot perform transactions on a closed account");
        }
        // 2. La cantidad debe ser un número finito mayor que 0
        $this->validateAmount($transaction->getAmount());

        // 3. La transacción calcula el nuevo saldo. Esta cuenta solo cambia aquí si es una
        //    transferencia, que mueve el dinero de las dos cuentas a la vez
        try {
            $newBalance = $transaction->applyTransaction($this);
        } catch (InvalidOverdraftFundsException $e) {
            // Convierte el error interno en uno general. El saldo no se ha tocado
            throw new FailedTransactionException("Insufficient balance to complete the transaction");
        }

        // 4. Solo se guarda el saldo si todo ha ido bien
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

    // Cambia la estrategia de descubierto. Es el método público definido en la interfaz
    public function applyOverdraft(OverdraftInterface $overdraft)
    {
        $this->setOverdraft($overdraft);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    // Valida el formato antes de guardar; si es incorrecto lanza InvalidArgsException
    public function setEmail(string $email)
    {
        $this->validateEmail($email);
        $this->email = $email;
    }
}
