<?php

namespace App\Presenter;

use App\Entities\MovieData;

class MoviePresenter
{
    public function __construct(
        /** @var MovieData[] */
        private array $movies
    ) {
    }

    public function toArray()
    {
        return array_map(fn (MovieData $movie) => [
            'tconst' => $movie->getTconst(),
            'primaryTitle' => $movie->getPrimaryTitle(),
            'runtimeMinutes' => $movie->getRuntimeMinutes(),
            'genres' => $movie->getGenres(),
        ], $this->movies);
    }
}
