<?php
namespace ComBank\OverdraftStrategy\Contracts;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:44 PM
 */

// Patrón Strategy: cada clase es una política de descubierto intercambiable
interface OverdraftInterface
{
    // Recibe el saldo que quedaría tras la operación (no la cantidad) y dice si se permite
    public function isGrantOverdraftFunds(float $overdraft): bool;

    // Cuánto puede quedar la cuenta en negativo
    public function getOverdraftFundsAmount(): float;

    // Porcentaje de interés
    public function getInterestRate(): int;
}
