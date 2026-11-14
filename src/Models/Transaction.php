<?php
declare(strict_types=1);

namespace App\Models;

use App\Contracts\RentableInterface;

class Transaction
{
    private string $transactionId;
    private string $createdAt;
    private float $totalCost;

    public function __construct(
        private User $user,
        private RentableInterface $media,
        private int $days
    ) {
        $this->transactionId = 'TRX-' . strtoupper(substr(uniqid(), -6));
        $this->createdAt = date('d M Y, H:i');
        $this->totalCost = $this->user->calculateRentalCost($this->media->getDailyRate(), $this->days);
    }

    public function toArray(): array
    {
        return [
            'transactionId' => $this->transactionId,
            'borrowerName' => $this->user->getName(),
            'borrowerEmail' => $this->user->getEmail(),
            'memberType' => $this->user->getMemberType(),
            'mediaId' => $this->media->getId(),
            'mediaTitle' => $this->media->getTitle(),
            'days' => $this->days,
            'totalCost' => $this->totalCost,
            'createdAt' => $this->createdAt,
        ];
    }
}