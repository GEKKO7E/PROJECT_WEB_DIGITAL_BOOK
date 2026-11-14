<?php
declare(strict_types=1);

namespace App\Models;

class Ebook extends Media
{
    public function __construct(
        string $id,
        string $title,
        string $author,
        string $category,
        float $dailyRate,
        private int $pages,
        private string $format = 'PDF/EPUB',
        private string $synopsis = '',
        private string $coverGradient = 'linear-gradient(135deg, #1e3a8a, #3b82f6)',
        private string $coverImage = '',
        bool $available = true
    ) {
        parent::__construct($id, $title, $author, $category, $dailyRate, $available);
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

    public function getCoverGradient(): string
    {
        return $this->coverGradient;
    }

    public function getCoverImage(): string
    {
        return $this->coverImage;
    }

    public function getMediaType(): string
    {
        return 'E-Book';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'category' => $this->category,
            'dailyRate' => $this->dailyRate,
            'pages' => $this->pages,
            'format' => $this->format,
            'synopsis' => $this->synopsis,
            'coverGradient' => $this->coverGradient,
            'coverImage' => $this->coverImage,
            'available' => $this->available,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) $data['id'],
            title: (string) $data['title'],
            author: (string) $data['author'],
            category: (string) $data['category'],
            dailyRate: (float) $data['dailyRate'],
            pages: (int) ($data['pages'] ?? 100),
            format: (string) ($data['format'] ?? 'PDF'),
            synopsis: (string) ($data['synopsis'] ?? 'Panduan praktis dan referensi mendalam untuk pengembangan perangkat lunak modern.'),
            coverGradient: (string) ($data['coverGradient'] ?? 'linear-gradient(135deg, #1e3a8a, #3b82f6)'),
            coverImage: (string) ($data['coverImage'] ?? ''),
            available: (bool) ($data['available'] ?? true)
        );
    }
}