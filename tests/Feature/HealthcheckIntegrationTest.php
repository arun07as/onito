<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthcheckIntegrationTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testHealthcheckRetuensSuccessResponse(): void
    {
        $response = $this->get('/healthcheck');

        $response->assertStatus(200);
        $response->assertContent("Running");
    }
}
