<?php

namespace Tests\Unit\Entities;

use App\Entities\MovieVotes;
use PHPUnit\Framework\TestCase;

class MovieVotesTest extends TestCase
{
    public function testConstructInitializedEntity(): void
    {
        $movieVotes = new MovieVotes(
            'My Title',
            20
        );

        $this->assertEquals($movieVotes->getPrimaryTitle(), 'My Title');
        $this->assertEquals($movieVotes->getNumVotes(), 20);
    }
}
