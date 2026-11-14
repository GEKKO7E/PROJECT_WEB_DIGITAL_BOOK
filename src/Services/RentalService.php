<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Ebook;
use App\Models\User;
use App\Models\Transaction;
use Exception;

class RentalService
{
   

    public function returnSingleEbook(string $ebookId): void
    {
        $ebooks = $this->getAllEbooks();
        foreach ($ebooks as $ebook) {
            if ($ebook->getId() === $ebookId) {
                $ebook->setAvailable(true);
            }
        }
        $serializedEbooks = array_map(fn(Ebook $b) => $b->toArray(), $ebooks);
        file_put_contents($this->ebooksFile, json_encode($serializedEbooks, JSON_PRETTY_PRINT));
    }

    public function returnAllEbooks(): void
    {
        $ebooks = $this->getAllEbooks();
        foreach ($ebooks as $ebook) {
            $ebook->setAvailable(true);
        }
        $serializedEbooks = array_map(fn(Ebook $b) => $b->toArray(), $ebooks);
        file_put_contents($this->ebooksFile, json_encode($serializedEbooks, JSON_PRETTY_PRINT));
    }

    public function getAllTransactions(): array
    {
        $raw = file_get_contents($this->transactionsFile);
        return json_decode($raw ?: '[]', true) ?: [];
    }
}