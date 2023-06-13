<?php

namespace App\Entities;

class MovieVotes
{
    public function __construct(
        private string $primaryTitle,
        private int $numVotes
    ) {
    }

    public function getPrimaryTitle(): string
    {
        return $this->primaryTitle;
    }

    public function getNumVotes(): int
    {
        return $this->numVotes;
    }
}
