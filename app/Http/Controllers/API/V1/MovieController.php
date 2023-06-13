<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\SaveMovieRequest;
use App\Presenter\MoviePresenter;
use App\Services\MovieService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    public function save(SaveMovieRequest $request): Response
    {
        $this->service->save($request->toEntity());
        return response('success');
    }
}
