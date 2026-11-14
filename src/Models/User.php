<?php
declare(strict_types=1);

namespace App\Models;

abstract class User
{
    public function __construct(
        protected string $name,
        protected string $email
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    abstract public function getMemberType(): string;
    abstract public function getDiscountRate(): float;

    public function calculateRentalCost(float $dailyRate, int $days): float
    {
        $subtotal = $dailyRate * $days;
        $discount = $subtotal * $this->getDiscountRate();
        return max(0, $subtotal - $discount);
    }
}