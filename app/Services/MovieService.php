<?php

namespace App\Services;

use App\Entities\GenreVotes;
use App\Entities\MovieData;
use App\Entities\MovieRating;
use App\Entities\MovieVotes;
use App\Models\Movie;
use Illuminate\Support\Facades\DB;

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

    /**
     * @param float $averageRating
     * @return MovieRating[]
     */
    public function topMovies(float $averageRating = 6.0): array
    {
        $movies = Movie::select([
            'movies.tconst',
            'primary_title',
            'genres',
            'average_rating'
        ])
            ->leftJoin('ratings', 'movies.tconst', '=', 'ratings.tconst')
            ->where('average_rating', '>', $averageRating)
            ->orderBy('average_rating', 'desc')
            ->get()
            ->all();

        return array_map(
            fn (Movie $movie) => new MovieRating(
                $movie->tconst,
                $movie->primary_title,
                $movie->average_rating,
                $movie->genres,
            ),
            $movies
        );
    }

    public function genreMoviesWithSubTotals()
    {
        $moviesByGenre = Movie::select([
            'genres',
            'primary_title',
            'num_votes'
        ])
            ->leftJoin('ratings', 'movies.tconst', '=', 'ratings.tconst')
            ->get()
            ->groupBy('genres');

        $sumVotesByGenre = Movie::select([
            'genres',
            DB::raw('SUM(num_votes) as votes_sum')
        ])
            ->leftJoin('ratings', 'movies.tconst', '=', 'ratings.tconst')
            ->groupBy('genres')
            ->pluck('votes_sum', 'genres');


        foreach ($moviesByGenre as $genre => $movies) {
            yield new GenreVotes(
                $genre,
                $sumVotesByGenre[$genre] ?? 0,
                $movies->map(fn (Movie $movie) => new MovieVotes(
                    $movie->primary_title,
                    $movie->num_votes
                ))->all()
            );
        }
    }

    private function generateNewTConst(int $id): string
    {
        return 'tt' . str_pad((string) $id, 7, '0', STR_PAD_LEFT);
    }
}
