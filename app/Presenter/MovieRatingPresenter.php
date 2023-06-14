<?php

namespace App\Presenter;

use App\Entities\MovieRating;

class MovieRatingPresenter
{
    public function __construct(
        /** @var MovieRating[] */
        private array $movieRatings
    ) {
    }

    public function toArray()
    {
        return array_map(fn (MovieRating $movieRating) => [
            'tconst' => $movieRating->getTconst(),
            'primaryTitle' => $movieRating->getPrimaryTitle(),
            'averageRating' => $movieRating->getAverageRating(),
            'genres' => $movieRating->getGenres(),
        ], $this->movieRatings);
    }
}
