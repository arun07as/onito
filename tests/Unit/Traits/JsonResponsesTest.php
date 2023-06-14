<?php

namespace Tests\Unit\Traits;

use App\Enums\ErrorCodes;
use App\Traits\JsonResponses;
use Exception;
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

    #[DataProvider('sendErrorCases')]
    public function testSendErrorReturnsErrorResponse(
        array $arguments,
        int $expectedStatusCode,
        array $expectedResponse,
        array $expectedHeaders
    ): void {
        $response = $this->responseGenerator->sendError(...$arguments);

        $this->assertEquals($response->getStatusCode(), $expectedStatusCode);
        $this->assertEquals($response->getData(true), $expectedResponse);

        foreach ($expectedHeaders as $header => $value) {
            $this->assertEquals($value, $response->headers->all($header));
        }
    }

    public function testSendErrorThrowsSameErrorIfPassedErrorAsMessage()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Foo Bar');

        $this->responseGenerator->sendError(new Exception('Foo Bar'));
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

    public static function sendErrorCases()
    {
        return [
            'Simple Case' => [
                [
                    "Sample Error Message",
                    400
                ],
                400,
                [
                    'data' => [],
                    'message' => 'Sample Error Message',
                    'errors' => [],
                    'error_code' => null
                ],
                []
            ],
            'Default Response' => [
                [],
                500,
                [
                    'data' => [],
                    'message' => 'Technical Error. Please try again later',
                    'errors' => [],
                    'error_code' => null
                ],
                []
            ],
            'Custom Message' => [
                [
                    'Custom Message'
                ],
                500,
                [
                    'data' => [],
                    'message' => 'Custom Message',
                    'errors' => [],
                    'error_code' => null
                ],
                []
            ],
            'Array as Errors' => [
                ['Message', 400, ['email' => ['Invalid']]],
                400,
                [
                    'data' => [],
                    'message' => 'Message',
                    'errors' => ['email' => ['Invalid']],
                    'error_code' => null
                ],
                []
            ],
            'Arrayable as Errors' => [
                ['Message', 400, new TempArrayable('World')],
                400,
                [
                    'data' => [],
                    'message' => 'Message',
                    'errors' => ['Hello' => 'World'],
                    'error_code' => null
                ],
                []
            ],
            'JsonSerializable as Errors' => [
                ['Msg', 400, new TempJsonSerializable('World')],
                400,
                [
                    'data' => [],
                    'message' => 'Msg',
                    'errors' => ['Hello' => 'World'],
                    'error_code' => null
                ],
                []
            ],
            'NULL as Errors' => [
                ['Msg', 400, null],
                400,
                [
                    'data' => [],
                    'message' => 'Msg',
                    'errors' => [],
                    'error_code' => null
                ],
                []
            ],
            'Custom Headers' => [
                ['Message', 400, [], null, ['Foo' => 'Bar']],
                400,
                [
                    'data' => [],
                    'message' => 'Message',
                    'errors' => [],
                    'error_code' => null
                ],
                ['Foo' => ['Bar']]
            ],
            'Error code' => [
                ['Message', 404, [], ErrorCodes::ROUTE_NOT_FOUND],
                404,
                [
                    'data' => [],
                    'message' => 'Message',
                    'errors' => [],
                    'error_code' => 'ROUTE_NOT_FOUND'
                ],
                []
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
