<?php

namespace Tests\Unit\Traits;

use App\Traits\JsonResponses;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use PHPUnit\Framework\Attributes\DataProvider;
use Stringable;
use Tests\TestCase;

class JsonResponsesTest extends TestCase
{
    private TempClass $responseGenerator;

    protected function setUp(): void
    {
        $this->createApplication();
        $this->responseGenerator = new TempClass();
    }

    #[DataProvider('sendResponseCases')]
    public function testSendResponseReturnsSuccessResponse(
        array $arguments,
        int $expectedStatusCode,
        array $expectedResponse,
        array $expectedHeaders
    ): void {
        $response = $this->responseGenerator->sendResponse(...$arguments);

        $this->assertEquals($response->getStatusCode(), $expectedStatusCode);
        $this->assertEquals($response->getData(true), $expectedResponse);

        foreach ($expectedHeaders as $header => $value) {
            $this->assertEquals($value, $response->headers->all($header));
        }
    }

    public static function sendResponseCases(): array
    {
        return [
            'Simple Case' => [
                [
                    ['Hello' => 'World'],
                    200
                ],
                200,
                [
                    'data' => ['Hello' => 'World'],
                    'message' => 'Success',
                    'errors' => [],
                    'error_code' => null
                ],
                []
            ],
            'Default Response' => [
                [],
                200,
                [
                    'data' => [],
                    'message' => 'Success',
                    'errors' => [],
                    'error_code' => null
                ],
                []
            ],
            'String as Data' => [
                ['Data'],
                200,
                [
                    'data' => 'Data',
                    'message' => 'Success',
                    'errors' => [],
                    'error_code' => null
                ],
                []
            ],
            'Array as Data' => [
                [['a' => 'b']],
                200,
                [
                    'data' => ['a' => 'b'],
                    'message' => 'Success',
                    'errors' => [],
                    'error_code' => null
                ],
                []
            ],
            'Stringable as Data' => [
                [new TempStringable('Hello World')],
                200,
                [
                    'data' => 'Hello World',
                    'message' => 'Success',
                    'errors' => [],
                    'error_code' => null
                ],
                []
            ],
            'Arrayable as Data' => [
                [new TempArrayable('World')],
                200,
                [
                    'data' => ['Hello' => 'World'],
                    'message' => 'Success',
                    'errors' => [],
                    'error_code' => null
                ],
                []
            ],
            'JsonSerializable as Data' => [
                [new TempJsonSerializable('World')],
                200,
                [
                    'data' => ['Hello' => 'World'],
                    'message' => 'Success',
                    'errors' => [],
                    'error_code' => null
                ],
                []
            ],
            'Custom Message' => [
                ['Data', 200, 'Different Message'],
                200,
                [
                    'data' => 'Data',
                    'message' => 'Different Message',
                    'errors' => [],
                    'error_code' => null
                ],
                []
            ],
            'Custom Headers' => [
                ['Data', 200, 'Success', ['Foo' => 'Bar']],
                200,
                [
                    'data' => 'Data',
                    'message' => 'Success',
                    'errors' => [],
                    'error_code' => null
                ],
                ['Foo' => ['Bar']]
            ],
        ];
    }
}

class TempClass
{
    use JsonResponses;
}

class TempStringable implements Stringable
{
    public function __construct(
        private string $string
    ) {
    }

    public function __toString(): string
    {
        return $this->string;
    }
}

class TempArrayable implements Arrayable
{
    public function __construct(
        private string $string
    ) {
    }

    public function toArray(): array
    {
        return ['Hello' => $this->string];
    }
}

class TempJsonSerializable implements JsonSerializable
{
    public function __construct(
        private string $string
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return ['Hello' => $this->string];
    }
}
