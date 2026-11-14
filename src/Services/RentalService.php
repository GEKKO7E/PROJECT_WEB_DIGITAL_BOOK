<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Ebook;
use App\Models\User;
use App\Models\Transaction;
use Exception;

class RentalService
{
    public function __construct(
        private string $ebooksFile,
        private string $transactionsFile
    ) {
        if (!file_exists($this->ebooksFile)) {
            file_put_contents($this->ebooksFile, json_encode([], JSON_PRETTY_PRINT));
        }
        if (!file_exists($this->transactionsFile)) {
            file_put_contents($this->transactionsFile, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    /**
     * @return Ebook[]
     */
    public function getAllEbooks(): array
    {
        $raw = file_get_contents($this->ebooksFile);
        $data = json_decode($raw ?: '[]', true) ?: [];
        return array_map(fn(array $item) => Ebook::fromArray($item), $data);
    }

    public function rentEbook(User $user, string $ebookId, int $days): Transaction
    {
        if ($days < 1) {
            throw new Exception('Durasi penyewaan minimal 1 hari.');
        }

        $ebooks = $this->getAllEbooks();
        $targetEbook = null;

        foreach ($ebooks as $ebook) {
            if ($ebook->getId() === $ebookId) {
                if (!$ebook->isAvailable()) {
                    throw new Exception('Maaf, e-book ini sedang disewa.');
                }
                $ebook->setAvailable(false);
                $targetEbook = $ebook;
                break;
            }
        }

        if ($targetEbook === null) {
            throw new Exception('E-book tidak ditemukan dalam katalog.');
        }

        $transaction = new Transaction($user, $targetEbook, $days);

        $serializedEbooks = array_map(fn(Ebook $b) => $b->toArray(), $ebooks);
        file_put_contents($this->ebooksFile, json_encode($serializedEbooks, JSON_PRETTY_PRINT));

        $transactions = $this->getAllTransactions();
        array_unshift($transactions, $transaction->toArray());
        file_put_contents($this->transactionsFile, json_encode($transactions, JSON_PRETTY_PRINT));

        return $transaction;
    }

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