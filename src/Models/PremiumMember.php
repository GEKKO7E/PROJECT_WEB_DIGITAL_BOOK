<?php
declare(strict_types=1);

namespace App\Models;

class PremiumMember extends User
{
    public function getMemberType(): string
    {
        return 'Premium Member (Diskon 20%)';
    }

    public function getDiscountRate(): float
    {
        return 0.20;
    }
}