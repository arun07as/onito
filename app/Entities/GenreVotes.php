<?php

namespace App\Entities;

class GenreVotes
{
    public function __construct(
        private string $genre,
        private int $totalVotes,
        /** @var MovieVotes[] $movieVotes */
        private array $movieVotes
    ) {
    }

    public function getGenre(): string
    {
        return $this->genre;
    }

    public function getTotalVotes(): int
    {
        return $this->totalVotes;
    }

    /**
     * @return MovieVotes[]
     */
    public function getMovieVotes(): array
    {
        return $this->movieVotes;
    }
}
