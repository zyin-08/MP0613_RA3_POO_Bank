<?php
namespace ComBank\OverdraftStrategy;

use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;

class GoldOverdraft implements OverdraftInterface
{
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
