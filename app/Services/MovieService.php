<?php

namespace App\Services;

use App\Entities\MovieData;
use App\Models\Movie;

class MovieService
{
    /**
     * @param int $limit
     * @param int $offset
     * @return MovieData[]
     */
    public function longestDurationMovies(int $limit = 10, int $offset = 0): array
    {
        $movies = Movie::orderBy('runtime_minutes', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->all();

        return array_map(
            fn (Movie $movie) => new MovieData(
                $movie->id,
                $movie->tconst,
                $movie->title_type,
                $movie->primary_title,
                $movie->runtime_minutes,
                $movie->genres,
                $movie->created_at,
                $movie->updated_at,
            ),
            $movies
        );
    }

    public function save(MovieData $movie): void
    {
        $newMovie = new Movie();
        if (!$movie->getId()) {
            $newMovie->tconst = '';
        }
        $newMovie->title_type = $movie->getTitleType();
        $newMovie->primary_title = $movie->getPrimaryTitle();
        $newMovie->runtime_minutes = $movie->getRuntimeMinutes();
        $newMovie->genres = $movie->getGenres();

        $newMovie->save();

        if (!$movie->getId()) {
            $newMovie->tconst = $this->generateNewTConst($newMovie->id);
            $newMovie->save();
        }
    }

    private function generateNewTConst(int $id): string
    {
        return 'tt' . str_pad((string) $id, 7, '0', STR_PAD_LEFT);
    }
}
