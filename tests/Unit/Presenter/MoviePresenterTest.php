<?php

namespace Tests\Unit\Presenter;

use App\Entities\MovieData;
use App\Presenter\MoviePresenter;
use Illuminate\Support\Carbon;
use Mockery;
use PHPUnit\Framework\TestCase;

class MoviePresenterTest extends TestCase
{
    public function testConstructInitializedPresenter(): void
    {
        new MoviePresenter(
            [Mockery::mock(MovieData::class)]
        );

        $this->addToAssertionCount(1);
    }

    public function testToArrayReturnsArray(): void
    {
        $presenter = new MoviePresenter(
            [
                new MovieData(
                    1,
                    'abcd',
                    'abc',
                    'def',
                    5,
                    'fgh',
                    Carbon::parse('2023-06-01T00:00:00+00:00'),
                    Carbon::parse('2023-06-02T00:00:00+00:00'),
                ),
                new MovieData(
                    2,
                    'abcde',
                    'abcd',
                    'defe',
                    6,
                    'fghi',
                    Carbon::parse('2023-06-03T01:00:00+00:00'),
                    Carbon::parse('2023-06-04T02:00:00+00:00'),
                ),
            ]
        );

        $result = $presenter->toArray();

        $this->assertEquals($result, [
            [
                'tconst' => 'abcd',
                'primaryTitle' => 'def',
                'runtimeMinutes' => 5,
                'genres' => 'fgh',
            ],
            [
                'tconst' => 'abcde',
                'primaryTitle' => 'defe',
                'runtimeMinutes' => 6,
                'genres' => 'fghi',
            ],
        ]);
    }

    public function testToArrayReturnsArrayEmptyArrayWhenNoMovies(): void
    {
        $presenter = new MoviePresenter([]);

        $result = $presenter->toArray();

        $this->assertEquals($result, []);
    }
}
