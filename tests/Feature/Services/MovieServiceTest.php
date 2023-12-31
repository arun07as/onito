<?php

namespace Tests\Feature\Services;

use App\Entities\GenreVotes;
use App\Entities\MovieData;
use App\Entities\MovieRating;
use App\Entities\MovieVotes;
use App\Models\Movie;
use App\Models\Rating;
use App\Services\MovieService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\MovieRatingGenerator;
use Tests\TestCase;

class MovieServiceTest extends TestCase
{
    use RefreshDatabase;
    use MovieRatingGenerator;

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

    public function testSaveInsertsRecordIntoDatabase()
    {
        Carbon::setTestNow('2023-01-01T00:00:00+00:00');

        $movie = new MovieData(
            null,
            null,
            'movie',
            'My movie',
            120,
            'Action'
        );

        $this->service->save($movie);

        $insertedData = Movie::first();
        $this->assertEquals(
            $insertedData->tconst,
            'tt' . str_pad((string) $insertedData->id, 7, '0', STR_PAD_LEFT)
        );
        $this->assertEquals($insertedData->title_type, 'movie');
        $this->assertEquals($insertedData->primary_title, 'My movie');
        $this->assertEquals($insertedData->runtime_minutes, 120);
        $this->assertEquals($insertedData->genres, 'Action');
    }

    public function testTopMoviesReturnsArrayOfMovieRating()
    {
        $insertData = $this->generateMoviesWithRating(20);

        usort(
            $insertData,
            fn ($i, $j) => ($i['rating']['average_rating'] <=> $j['rating']['average_rating']) * -1
        );
        $insertData = array_filter($insertData, fn ($data) => $data['rating']['average_rating'] > 6);
        $insertData = array_values($insertData);

        $expectedResult = array_map(
            fn (array $data) => new MovieRating(
                $data['tconst'],
                $data['primary_title'],
                $data['rating']['average_rating'],
                $data['genres']
            ),
            $insertData
        );

        $result = $this->service->topMovies();
        $this->assertEquals($result, $expectedResult);
    }

    public function testTopMoviesReturnsArrayOfMovieRatingFilteredByMinAverageRating()
    {
        $insertData = $this->generateMoviesWithRating(20);

        usort(
            $insertData,
            fn ($i, $j) => ($i['rating']['average_rating'] <=> $j['rating']['average_rating']) * -1
        );
        $insertData = array_filter($insertData, fn ($data) => $data['rating']['average_rating'] > 2);
        $insertData = array_values($insertData);

        $expectedResult = array_map(
            fn (array $data) => new MovieRating(
                $data['tconst'],
                $data['primary_title'],
                $data['rating']['average_rating'],
                $data['genres']
            ),
            $insertData
        );

        $result = $this->service->topMovies(2);
        $this->assertEquals($result, $expectedResult);
    }

    public function testTopMoviesReturnsEmptyArrayWhenNoMovies(): void
    {
        $result = $this->service->topMovies();
        $this->assertEquals($result, []);
    }

    public function testGenreMoviesWithSubTotalsReturnsArrayOfGenreVotes()
    {
        Movie::insert([
            [
                'id' => 1,
                'tconst' => '1',
                'title_type' => 'a',
                'primary_title' => 'b',
                'runtime_minutes' => 10,
                'genres' => 'Documentary',
            ],
            [
                'id' => 2,
                'tconst' => '2',
                'title_type' => 'aa',
                'primary_title' => 'ba',
                'runtime_minutes' => 15,
                'genres' => 'Animation',
            ],
            [
                'id' => 3,
                'tconst' => '3',
                'title_type' => 'aaa',
                'primary_title' => 'baa',
                'runtime_minutes' => 40,
                'genres' => 'Documentary',
            ],
        ]);

        Rating::insert([
            [
                'id' => 1,
                'tconst' => '1',
                'average_rating' => 1,
                'num_votes' => 30,
            ],
            [
                'id' => 2,
                'tconst' => '2',
                'average_rating' => 2,
                'num_votes' => 40,
            ],
            [
                'id' => 3,
                'tconst' => '3',
                'average_rating' => 3,
                'num_votes' => 100,
            ],
        ]);

        $expectedResult = [
            new GenreVotes('Documentary', 130, [
                new MovieVotes('b', 30),
                new MovieVotes('baa', 100),
            ]),
            new GenreVotes('Animation', 40, [
                new MovieVotes('ba', 40),
            ]),
        ];

        $result = $this->service->genreMoviesWithSubTotals();

        $this->assertEquals($result, $expectedResult);
    }

    public function testGenreMoviesWithSubTotalsReturnsEmptyArrayWhenNoMovies(): void
    {
        $result = $this->service->genreMoviesWithSubTotals();
        $this->assertEquals($result, []);
    }

    public function testUpdateRuntimeMinutesUpdatesValuesCorrectly()
    {
        Movie::insert([
            [
                'id' => 1,
                'tconst' => '1',
                'title_type' => 'a',
                'primary_title' => 'b',
                'runtime_minutes' => 10,
                'genres' => 'Documentary',
            ],
            [
                'id' => 2,
                'tconst' => '2',
                'title_type' => 'aa',
                'primary_title' => 'ba',
                'runtime_minutes' => 15,
                'genres' => 'Animation',
            ],
            [
                'id' => 3,
                'tconst' => '3',
                'title_type' => 'aaa',
                'primary_title' => 'baa',
                'runtime_minutes' => 40,
                'genres' => 'Documentary',
            ],
            [
                'id' => 4,
                'tconst' => '4',
                'title_type' => 'aaaa',
                'primary_title' => 'baaa',
                'runtime_minutes' => 400,
                'genres' => 'SomethingElse',
            ],
        ]);

        $this->service->updateRuntimeMinutes();

        $movies = Movie::orderBy('id')->find([1, 2, 3, 4]);

        $this->assertEquals($movies[0]->runtime_minutes, 25);
        $this->assertEquals($movies[1]->runtime_minutes, 45);
        $this->assertEquals($movies[2]->runtime_minutes, 55);
        $this->assertEquals($movies[3]->runtime_minutes, 445);
    }
}
