<?php

namespace Tests\Unit\Entities;

use App\Entities\GenreVotes;
use App\Entities\MovieVotes;
use PHPUnit\Framework\TestCase;

class GenreVotesTest extends TestCase
{
    public function testConstructInitializedEntity(): void
    {
        $genreVotes = new GenreVotes(
            'Documentary',
            100,
            $movieVotes = [
                new MovieVotes('Title', 100),
                new MovieVotes('Title 2', 200),
            ]
        );

        $this->assertEquals($genreVotes->getGenre(), 'Documentary');
        $this->assertEquals($genreVotes->getTotalVotes(), 100);
        $this->assertEquals($genreVotes->getMovieVotes(), $movieVotes);
    }
}
