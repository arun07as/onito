<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthcheckIntegrationTest extends TestCase
{
    public function testHealthcheckRetuensSuccessResponse(): void
    {
        $response = $this->get('/healthcheck');

        $response->assertStatus(200);
        $response->assertContent("Running");
    }
}
