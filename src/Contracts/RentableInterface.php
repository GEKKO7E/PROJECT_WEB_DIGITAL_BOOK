<?php
declare(strict_types=1);

namespace App\Contracts;

interface RentableInterface
{
    public function getId(): string;
    public function getTitle(): string;
    public function getDailyRate(): float;
    public function isAvailable(): bool;
    public function setAvailable(bool $status): void;
}