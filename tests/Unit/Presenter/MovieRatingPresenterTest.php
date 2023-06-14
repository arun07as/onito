<?php

namespace Tests\Unit\Presenter;

use App\Entities\MovieRating;
use App\Presenter\MovieRatingPresenter;
use Illuminate\Support\Carbon;
use Mockery;
use PHPUnit\Framework\TestCase;

class MovieRatingPresenterTest extends TestCase
{
    public function testConstructInitializedPresenter(): void
    {
        new MovieRatingPresenter(
            [Mockery::mock(MovieData::class)]
        );

        $this->addToAssertionCount(1);
    }

    public function testToArrayReturnsArray(): void
    {
        $presenter = new MovieRatingPresenter(
            [
                new MovieRating(
                    'abcd',
                    'abc',
                    '5.6',
                    'def',
                ),
                new MovieRating(
                    'abcde',
                    'abcd',
                    '9.5',
                    'defe',
                ),
            ]
        );

        $result = $presenter->toArray();

        $this->assertEquals($result, [
            [
                'tconst' => 'abcd',
                'primaryTitle' => 'abc',
                'averageRating' => '5.6',
                'genres' => 'def',
            ],
            [
                'tconst' => 'abcde',
                'primaryTitle' => 'abcd',
                'averageRating' => '9.5',
                'genres' => 'defe',
            ],
        ]);
    }

    public function testToArrayReturnsArrayEmptyArrayWhenNoMovies(): void
    {
        $presenter = new MovieRatingPresenter([]);

        $result = $presenter->toArray();

        $this->assertEquals($result, []);
    }
}
