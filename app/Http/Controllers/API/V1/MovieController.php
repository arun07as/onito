<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Presenter\MoviePresenter;
use App\Services\MovieService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function __construct(
        private MovieService $service
    ) {
    }

    public function longestDurationMovies(): JsonResponse
    {
        return $this->sendResponse(
            (new MoviePresenter($this->service->longestDurationMovies()))->toArray()
        );
    }
}
