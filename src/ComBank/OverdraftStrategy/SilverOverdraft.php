<?php
namespace ComBank\OverdraftStrategy;

use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 1:39 PM
 */

// Descubierto Silver: el saldo puede llegar hasta -100. Interés del 5%
/**
 * @description: Grant 100.00 overdraft funds.
 * */
class SilverOverdraft implements OverdraftInterface
{
    // Saldo resultante -80 -> 100 + (-80) = 20, mayor o igual que 0: se permite
    public function isGrantOverdraftFunds(float $overdraft): bool
    {
        return $this->getOverdraftFundsAmount() + $overdraft >= 0;
    }

    public function getOverdraftFundsAmount(): float
    {
        return 100.0;
    }

    public function getInterestRate(): int
    {
        return 5;
    }

}
