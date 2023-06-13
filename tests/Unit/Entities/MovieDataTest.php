<?php

namespace Tests\Unit\Entities;

use App\Entities\MovieData;
use DateTime;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class MovieDataTest extends TestCase
{
    public function testConstructInitializedEntity(): void
    {
        $movieData = new MovieData(
            1,
            'tt0000001',
            'short',
            'My Movie',
            20,
            'Comedy',
            Carbon::parse('2023-06-12T00:00:00'),
            Carbon::parse('2023-06-13T00:00:00')
        );

        $this->assertEquals($movieData->getId(), 1);
        $this->assertEquals($movieData->getTconst(), 'tt0000001');
        $this->assertEquals($movieData->getTitleType(), 'short');
        $this->assertEquals($movieData->getPrimaryTitle(), 'My Movie');
        $this->assertEquals($movieData->getRuntimeMinutes(), 20);
        $this->assertEquals($movieData->getGenres(), 'Comedy');
        $this->assertEquals(
            $movieData->getCreatedAt()->format(DateTime::ATOM),
            '2023-06-12T00:00:00+00:00'
        );
        $this->assertEquals(
            $movieData->getUpdatedAt()->format(DateTime::ATOM),
            '2023-06-13T00:00:00+00:00'
        );
    }

    public function testConstructInitializedEntityWithOptionalParameters(): void
    {
        $movieData = new MovieData(
            null,
            null,
            'short',
            'My Movie',
            20,
            'Comedy'
        );

        $this->assertEquals($movieData->getId(), null);
        $this->assertEquals($movieData->getTconst(), null);
        $this->assertEquals($movieData->getTitleType(), 'short');
        $this->assertEquals($movieData->getPrimaryTitle(), 'My Movie');
        $this->assertEquals($movieData->getRuntimeMinutes(), 20);
        $this->assertEquals($movieData->getGenres(), 'Comedy');
        $this->assertEquals($movieData->getCreatedAt(), null);
        $this->assertEquals($movieData->getUpdatedAt(), null);
    }
}
