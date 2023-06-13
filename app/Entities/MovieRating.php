<?php

namespace App\Entities;

class MovieRating
{
    public function __construct(
        private string $tconst,
        private string $primaryTitle,
        private string $averageRating,
        private string $genres,
    ) {
    }

    public function getTconst(): string
    {
        return $this->tconst;
    }

    public function getPrimaryTitle(): string
    {
        return $this->primaryTitle;
    }

    public function getAverageRating(): string
    {
        return $this->averageRating;
    }

    public function getGenres(): string
    {
        return $this->genres;
    }
}
