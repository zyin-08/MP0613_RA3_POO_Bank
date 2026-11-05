<?php
namespace ComBank\OverdraftStrategy;

use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 12:27 PM
 */

// Sin descubierto: el saldo no puede ser negativo. Interés del 1%
class NoOverdraft implements OverdraftInterface
{
    // Saldo resultante -1 -> 0 + (-1) = -1, no se permite
    public function isGrantOverdraftFunds(float $overdraft): bool
    {
        return $this->getOverdraftFundsAmount() + $overdraft >= 0;
    }

    public function getOverdraftFundsAmount(): float
    {
        return 0.0;
    }

    public function getInterestRate(): int
    {
        return 1;
    }

}
