<?php
declare(strict_types=1);

namespace App\Models;

use App\Contracts\RentableInterface;

abstract class Media implements RentableInterface
{
    public function __construct(
        protected string $id,
        protected string $title,
        protected string $author,
        protected string $category,
        protected float $dailyRate,
        protected bool $available = true
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getDailyRate(): float
    {
        return $this->dailyRate;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $status): void
    {
        $this->available = $status;
    }

    abstract public function getMediaType(): string;
    abstract public function toArray(): array;
}