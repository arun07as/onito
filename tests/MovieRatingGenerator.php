<?php

namespace Tests;

use App\Models\Movie;
use App\Models\Rating;
use Illuminate\Support\Carbon;

trait MovieRatingGenerator
{
    private function generateMovies(int $count = 10): array
    {
        $insertData = [];
        for ($i = 1; $i <= $count; $i++) {
            $insertData[] = [
                'id' => $i,
                'tconst' => 'tt' . $i,
                'title_type' => 'type' . $i,
                'primary_title' => 'title' . $i,
                'runtime_minutes' => rand(5, 1000),
                'genres' => 'genre' . $i,
                'created_at' => Carbon::parse('2023-01-01T00:00:00+00:00')->addDay($i),
                'updated_at' => Carbon::parse('2023-01-01T00:00:00+00:00')->addHour($i),
            ];
        }

        Movie::insert($insertData);

        return $insertData;
    }

    private function generateMoviesWithRating(int $count = 10): array
    {
        $insertData = $this->generateMovies($count);

        $ratingInsertData = [];
        foreach ($insertData as $index => &$movieData) {
            $data = [
                'tconst' => $movieData['tconst'],
                'average_rating' => $index % 10,
                'num_votes' => rand(1, 10000),
                'created_at' => Carbon::parse('2023-01-01T00:00:00+00:00')->addDay($index),
                'updated_at' => Carbon::parse('2023-01-01T00:00:00+00:00')->addHour($index),
            ];
            $ratingInsertData[] = $data;
            $movieData['rating'] = $data;
        }
        Rating::insert($ratingInsertData);

        return $insertData;
    }
}
