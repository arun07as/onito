<?php

namespace App\Services;

use App\Entities\MovieData;
use App\Models\Movie;

class MovieService
{
    public function longestDurationMovies(int $limit = 10, int $offset = 0)
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
}
