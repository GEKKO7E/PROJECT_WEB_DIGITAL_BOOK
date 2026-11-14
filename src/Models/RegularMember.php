<?php
declare(strict_types=1);

namespace App\Models;

class RegularMember extends User
{
    public function getMemberType(): string
    {
        return 'Regular Member';
    }

    public function getDiscountRate(): float
    {
        return 0.0;
    }
}