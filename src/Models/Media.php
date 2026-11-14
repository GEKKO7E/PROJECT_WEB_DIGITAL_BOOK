<?php
declare(strict_types=1);

namespace App\Models;

use App\Contracts\RentableInterface;
use InvalidArgumentException;

abstract class Media implements RentableInterface
{
    protected string $id;
    protected string $title;
    protected string $author;
    protected string $category;
    protected float $dailyRate;
    protected bool $isAvailable;

    public function __construct(
        string $id,
        string $title,
        string $author,
        string $category,
        float $dailyRate,
        bool $isAvailable = true
    ) {
        $this->id = trim($id);
        $this->setTitle($title);
        $this->setAuthor($author);
        $this->category = trim($category);
        $this->setDailyRate($dailyRate);
        $this->isAvailable = $isAvailable;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $clean = trim($title);
        if ($clean === '') {
            throw new InvalidArgumentException('Judul media tidak boleh kosong.');
        }
        $this->title = $clean;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $clean = trim($author);
        if ($clean === '') {
            throw new InvalidArgumentException('Nama penulis/kreator tidak boleh kosong.');
        }
        $this->author = $clean;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getDailyRate(): float
    {
        return $this->dailyRate;
    }

    public function setDailyRate(float $dailyRate): void
    {
        if ($dailyRate < 0) {
            throw new InvalidArgumentException('Tarif sewa harian tidak boleh negatif.');
        }
        $this->dailyRate = $dailyRate;
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function setAvailable(bool $status): void
    {
        $this->isAvailable = $status;
    }

    abstract public function getMediaType(): string;

    abstract public function toArray(): array;
}