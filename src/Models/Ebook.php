<?php
declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

class Ebook
{
    private string $id;
    private string $title;
    private string $author;
    private string $category;
    private float $dailyRate;
    private int $pages;
    private string $format;
    private string $synopsis;
    private string $cover;
    private bool $isAvailable;

    public function __construct(
        string $id,
        string $title,
        string $author,
        string $category,
        float $dailyRate,
        int $pages,
        string $format = 'PDF / EPUB',
        string $synopsis = '',
        string $cover = '',
        bool $isAvailable = true
    ) {
        $this->id = trim($id);
        $this->setTitle($title);
        $this->setAuthor($author);
        $this->category = trim($category);
        $this->setDailyRate($dailyRate);
        $this->pages = max(1, $pages);
        $this->format = trim($format);
        $this->synopsis = trim($synopsis);
        $this->cover = trim($cover);
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
            throw new InvalidArgumentException('Judul e-book tidak boleh kosong.');
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
            throw new InvalidArgumentException('Nama penulis tidak boleh kosong.');
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

    public function getPages(): int
    {
        return $this->pages;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getSynopsis(): string
    {
        return $this->synopsis;
    }

    public function getCover(): string
    {
        return $this->cover;
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function setAvailable(bool $status): void
    {
        $this->isAvailable = $status;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['id'] ?? ''),
            (string) ($data['title'] ?? ''),
            (string) ($data['author'] ?? ''),
            (string) ($data['category'] ?? 'Umum'),
            (float) ($data['dailyRate'] ?? 0),
            (int) ($data['pages'] ?? 100),
            (string) ($data['format'] ?? 'PDF / EPUB'),
            (string) ($data['synopsis'] ?? ''),
            (string) ($data['cover'] ?? ''),
            (bool) ($data['isAvailable'] ?? true)
        );
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'author'      => $this->author,
            'category'    => $this->category,
            'dailyRate'   => $this->dailyRate,
            'pages'       => $this->pages,
            'format'      => $this->format,
            'synopsis'    => $this->synopsis,
            'cover'       => $this->cover,
            'isAvailable' => $this->isAvailable,
        ];
    }
}