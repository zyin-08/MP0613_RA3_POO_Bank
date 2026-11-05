<?php
namespace ComBank\OverdraftStrategy;

use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;

// Descubierto Gold: el saldo puede llegar hasta -500. Interés del 10%
class GoldOverdraft implements OverdraftInterface
{
    // Saldo resultante -600 -> 500 + (-600) = -100, no se permite
    public function isGrantOverdraftFunds(float $overdraft): bool
    {
        return $this->getOverdraftFundsAmount() + $overdraft >= 0;
    }

    public function getOverdraftFundsAmount(): float
    {
        return 500.0;
    }

    public function getInterestRate(): int
    {
        return 10;
    }

}
