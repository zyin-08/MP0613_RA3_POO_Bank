<?php
namespace ComBank\Support\Traits;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 2:35 PM
 */

use ComBank\Exceptions\InvalidArgsException;
use ComBank\Exceptions\ZeroAmountException;

// Trait: métodos reutilizables que incluyen BankAccount y BaseTransaction con 'use'
trait AmountValidationTrait
{
    /**
     * @param float $amount
     * @throws InvalidArgsException
     * @throws ZeroAmountException
     */
    public function validateAmount(float $amount): void
    {
        // El 0 y los negativos no son válidos
        if ($amount <= 0.0) {
            throw new ZeroAmountException("The amount must be greater than zero");
        }
    }

    // private: solo se puede llamar desde la clase que incluye el trait
    private function validateEmail(string $email): void
    {
        // filter_var es una función de PHP que comprueba el formato del email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgsException("Invalid email format");
        }
    }
}
