<?php

namespace Tests\Feature\Services;

use App\Entities\MovieData;
use App\Models\Movie;
use App\Services\MovieService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MovieServiceTest extends TestCase
{
    use RefreshDatabase;

    private MovieService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new MovieService();
    }

    public function testLongestDurationMoviesReturnsArrayOfTenMovieDataSortedByRuntimeMinutes(): void
    {
        $insertData = $this->generateMovies(10);

        usort(
            $insertData,
            fn ($i, $j) => ($i['runtime_minutes'] <=> $j['runtime_minutes']) * -1
        );
        $insertData = array_values($insertData);

        $expectedResult = array_map(
            fn (array $data) => new MovieData(
                $data['id'],
                $data['tconst'],
                $data['title_type'],
                $data['primary_title'],
                $data['runtime_minutes'],
                $data['genres'],
                $data['created_at'],
                $data['updated_at'],
            ),
            $insertData
        );

        $result = $this->service->longestDurationMovies();
        $this->assertEquals($result, $expectedResult);
    }

    public function testLongestDurationMoviesReturnsLimitedMoviesWhenLimitApplied(): void
    {
        $insertData = $this->generateMovies(10);

        usort(
            $insertData,
            fn ($i, $j) => ($i['runtime_minutes'] <=> $j['runtime_minutes']) * -1
        );
        $insertData = array_slice(array_values($insertData), 0, 5);

        $expectedResult = array_map(
            fn (array $data) => new MovieData(
                $data['id'],
                $data['tconst'],
                $data['title_type'],
                $data['primary_title'],
                $data['runtime_minutes'],
                $data['genres'],
                $data['created_at'],
                $data['updated_at'],
            ),
            $insertData
        );

        $result = $this->service->longestDurationMovies(5, 0);
        $this->assertEquals($result, $expectedResult);
    }

    public function testLongestDurationMoviesReturnsLimitedMoviesWhenLimitAndOffsetApplied(): void
    {
        $insertData = $this->generateMovies(10);

        usort(
            $insertData,
            fn ($i, $j) => ($i['runtime_minutes'] <=> $j['runtime_minutes']) * -1
        );
        $insertData = array_slice(array_values($insertData), 5, 5);

        $expectedResult = array_map(
            fn (array $data) => new MovieData(
                $data['id'],
                $data['tconst'],
                $data['title_type'],
                $data['primary_title'],
                $data['runtime_minutes'],
                $data['genres'],
                $data['created_at'],
                $data['updated_at'],
            ),
            $insertData
        );

        $result = $this->service->longestDurationMovies(5, 5);
        $this->assertEquals($result, $expectedResult);
    }

    public function testLongestDurationMoviesReturnsEmptyArrayWhenNoMovies(): void
    {
        $result = $this->service->longestDurationMovies();
        $this->assertEquals($result, []);
    }

    private function generateMovies(int $count = 10)
    {
        $insertData = [];
        for ($i = 0; $i < $count; $i++) {
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
}
