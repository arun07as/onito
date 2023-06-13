<?php

namespace Tests\Unit\Entities;

use App\Entities\MovieRating;
use PHPUnit\Framework\TestCase;

class MovieRatingTest extends TestCase
{
    public function testConstructInitializedEntity(): void
    {
        $movieRating = new MovieRating(
            'tt0000001',
            'My Movie Title',
            '9.8',
            'Comedy'
        );

        $this->assertEquals($movieRating->getTconst(), 'tt0000001');
        $this->assertEquals($movieRating->getPrimaryTitle(), 'My Movie Title');
        $this->assertEquals($movieRating->getAverageRating(), '9.8');
        $this->assertEquals($movieRating->getGenres(), 'Comedy');
    }
}
