<?php

namespace Tests\Feature;

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\MovieRatingGenerator;
use Tests\TestCase;

class MovieAPITest extends TestCase
{
    use RefreshDatabase;
    use MovieRatingGenerator;

    public function testLongestDurationMinutesEndpointReturnsMovies(): void
    {
        Movie::insert([
            [
                'id' => 1,
                'tconst' => 'tconst1',
                'title_type' => 'movie',
                'primary_title' => 'Movie 1',
                'runtime_minutes' => 10,
                'genres' => 'Documentary',
            ],
            [
                'id' => 2,
                'tconst' => 'tconst2',
                'title_type' => 'short',
                'primary_title' => 'Movie 2',
                'runtime_minutes' => 150,
                'genres' => 'Animation',
            ],
            [
                'id' => 3,
                'tconst' => 'tconst3',
                'title_type' => 'movie',
                'primary_title' => 'Movie 3',
                'runtime_minutes' => 40,
                'genres' => 'Documentary',
            ],
        ]);

        $response = $this->get('/api/v1/longest-duration-movies');

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                [
                    'tconst' => 'tconst2',
                    'primaryTitle' => 'Movie 2',
                    'runtimeMinutes' => 150,
                    'genres' => 'Animation',
                ],
                [
                    'tconst' => 'tconst3',
                    'primaryTitle' => 'Movie 3',
                    'runtimeMinutes' => 40,
                    'genres' => 'Documentary',
                ],
                [
                    'tconst' => 'tconst1',
                    'primaryTitle' => 'Movie 1',
                    'runtimeMinutes' => 10,
                    'genres' => 'Documentary',
                ]
            ],
            'message' => 'Success',
            'errors' => [],
            'error_code' => null
        ]);
    }

    public function testLongestDurationMinutesEndpointReturnsMaxTenMovies(): void
    {
        $this->generateMovies(30);

        $response = $this->get('/api/v1/longest-duration-movies');

        $response->assertStatus(200);

        $data = json_decode($response->getContent(), true)['data'];
        $this->assertCount(10, $data);
    }

    public function testLongestDurationMinutesEndpointReturnsEmptyArrayWhenNoMovies(): void
    {
        $response = $this->get('/api/v1/longest-duration-movies');

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [],
            'message' => 'Success',
            'errors' => [],
            'error_code' => null
        ]);
    }
}
