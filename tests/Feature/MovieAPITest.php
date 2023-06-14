<?php

namespace Tests\Feature;

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
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

    public function testNewMovieSucceeds(): void
    {
        $response = $this->post('/api/v1/new-movie', [
            'titleType' => 'movie',
            'primaryTitle' => 'my movie name',
            'runtimeMinutes' => 50,
            'genres' => 'Comedy',
        ]);

        $response->assertStatus(200);
        $response->assertContent('success');

        $this->assertDatabaseHas('movies', [
            'title_type' => 'movie',
            'primary_title' => 'my movie name',
            'runtime_minutes' => 50,
            'genres' => 'Comedy'
        ]);
    }

    #[DataProvider('saveMovieValidationErrors')]
    public function testNewMovieReturnsValidationErrors(array $postData, array $expectedResult): void
    {
        $response = $this->post('/api/v1/new-movie', $postData);

        $response->assertStatus(422);
        $response->assertExactJson($expectedResult);
    }

    public static function saveMovieValidationErrors(): array
    {
        return [
            'title_type longer than 10 chars' => [
                [
                    'titleType' => 'testThatIsLongerThanTenCharacters',
                    'primaryTitle' => 'Title',
                    'runtimeMinutes' => 10,
                    'genres' => 'Documentary',
                ],
                [
                    'data' => [],
                    'message' => 'The title type field must not be greater than 10 characters.',
                    'errors' => [
                        'titleType' => [
                            'The title type field must not be greater than 10 characters.'
                        ]
                    ],
                    'error_code' => 'VALIDATION_ERROR'
                ]
            ],
            'title_type missing' => [
                [
                    'titleType' => null,
                    'primaryTitle' => 'Title',
                    'runtimeMinutes' => 10,
                    'genres' => 'Documentary',
                ],
                [
                    'data' => [],
                    'message' => 'The title type field is required.',
                    'errors' => [
                        'titleType' => [
                            'The title type field is required.'
                        ]
                    ],
                    'error_code' => 'VALIDATION_ERROR'
                ]
            ],
            'title_type not string' => [
                [
                    'titleType' => ['Foo'],
                    'primaryTitle' => 'Title',
                    'runtimeMinutes' => 10,
                    'genres' => 'Documentary',
                ],
                [
                    'data' => [],
                    'message' => 'The title type field must be a string.',
                    'errors' => [
                        'titleType' => [
                            'The title type field must be a string.'
                        ]
                    ],
                    'error_code' => 'VALIDATION_ERROR'
                ]
            ],
            'primaryTitle longer than 150 chars' => [
                [
                    'titleType' => 'movie',
                    'primaryTitle' => Str::random(200),
                    'runtimeMinutes' => 10,
                    'genres' => 'Documentary',
                ],
                [
                    'data' => [],
                    'message' => 'The primary title field must not be greater than 150 characters.',
                    'errors' => [
                        'primaryTitle' => [
                            'The primary title field must not be greater than 150 characters.'
                        ]
                    ],
                    'error_code' => 'VALIDATION_ERROR'
                ]
            ],
            'primaryTitle missing' => [
                [
                    'titleType' => 'movie',
                    'primaryTitle' => null,
                    'runtimeMinutes' => 10,
                    'genres' => 'Documentary',
                ],
                [
                    'data' => [],
                    'message' => 'The primary title field is required.',
                    'errors' => [
                        'primaryTitle' => [
                            'The primary title field is required.'
                        ]
                    ],
                    'error_code' => 'VALIDATION_ERROR'
                ]
            ],
            'primaryTitle not string' => [
                [
                    'titleType' => 'movie',
                    'primaryTitle' => ['Foo'],
                    'runtimeMinutes' => 10,
                    'genres' => 'Documentary',
                ],
                [
                    'data' => [],
                    'message' => 'The primary title field must be a string.',
                    'errors' => [
                        'primaryTitle' => [
                            'The primary title field must be a string.'
                        ]
                    ],
                    'error_code' => 'VALIDATION_ERROR'
                ]
            ],
            'genres longer than 50 chars' => [
                [
                    'titleType' => 'movie',
                    'primaryTitle' => 'Movie 1',
                    'runtimeMinutes' => 10,
                    'genres' => Str::random(60),
                ],
                [
                    'data' => [],
                    'message' => 'The genres field must not be greater than 50 characters.',
                    'errors' => [
                        'genres' => [
                            'The genres field must not be greater than 50 characters.'
                        ]
                    ],
                    'error_code' => 'VALIDATION_ERROR'
                ]
            ],
            'genres missing' => [
                [
                    'titleType' => 'movie',
                    'primaryTitle' => 'Movie 1',
                    'runtimeMinutes' => 10,
                    'genres' => null,
                ],
                [
                    'data' => [],
                    'message' => 'The genres field is required.',
                    'errors' => [
                        'genres' => [
                            'The genres field is required.'
                        ]
                    ],
                    'error_code' => 'VALIDATION_ERROR'
                ]
            ],
            'genres not string' => [
                [
                    'titleType' => 'movie',
                    'primaryTitle' => 'Movie1',
                    'runtimeMinutes' => 10,
                    'genres' => ['Foo'],
                ],
                [
                    'data' => [],
                    'message' => 'The genres field must be a string.',
                    'errors' => [
                        'genres' => [
                            'The genres field must be a string.'
                        ]
                    ],
                    'error_code' => 'VALIDATION_ERROR'
                ]
            ],
            'runtimeMinutes missing' => [
                [
                    'titleType' => 'movie',
                    'primaryTitle' => 'Movie 1',
                    'runtimeMinutes' => null,
                    'genres' => 'Documentary',
                ],
                [
                    'data' => [],
                    'message' => 'The runtime minutes field is required.',
                    'errors' => [
                        'runtimeMinutes' => [
                            'The runtime minutes field is required.'
                        ]
                    ],
                    'error_code' => 'VALIDATION_ERROR'
                ]
            ],
            'runtimeMinutes not integer' => [
                [
                    'titleType' => 'movie',
                    'primaryTitle' => 'My Movie',
                    'runtimeMinutes' => 'Not an integer',
                    'genres' => 'Documentary',
                ],
                [
                    'data' => [],
                    'message' => 'The runtime minutes field must be an integer.',
                    'errors' => [
                        'runtimeMinutes' => [
                            'The runtime minutes field must be an integer.'
                        ]
                    ],
                    'error_code' => 'VALIDATION_ERROR'
                ]
            ],
            'runtimeMinutes less than 1' => [
                [
                    'titleType' => 'movie',
                    'primaryTitle' => 'My Movie',
                    'runtimeMinutes' => -5,
                    'genres' => 'Documentary',
                ],
                [
                    'data' => [],
                    'message' => 'The runtime minutes field must be at least 1.',
                    'errors' => [
                        'runtimeMinutes' => [
                            'The runtime minutes field must be at least 1.'
                        ]
                    ],
                    'error_code' => 'VALIDATION_ERROR'
                ]
            ],
            'runtimeMinutes greater than 65535' => [
                [
                    'titleType' => 'movie',
                    'primaryTitle' => 'My Movie',
                    'runtimeMinutes' => 100000,
                    'genres' => 'Documentary',
                ],
                [
                    'data' => [],
                    'message' => 'The runtime minutes field must not be greater than 65535.',
                    'errors' => [
                        'runtimeMinutes' => [
                            'The runtime minutes field must not be greater than 65535.'
                        ]
                    ],
                    'error_code' => 'VALIDATION_ERROR'
                ]
            ],
        ];
    }
}
