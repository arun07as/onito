<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ErrorResponseIntegrationTest extends TestCase
{
    public function testInvalidRouteReturnsNotFoundResponse(): void
    {
        $response = $this->get('/invalid-route');

        $response->assertNotFound();
        $response->assertExactJson([
            'data' => [],
            'message' => 'Route not found',
            'errors' => [],
            'error_code' => 'ROUTE_NOT_FOUND'
        ]);
    }
}
