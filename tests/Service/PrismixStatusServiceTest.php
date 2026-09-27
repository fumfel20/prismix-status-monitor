<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\PrismixStatusService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class PrismixStatusServiceTest extends TestCase
{
    public function testGetStatusesSuccessfullyReturnsMappedData(): void
    {
        $mockJson = json_encode([
            'fetchedAt' => '2026-09-27T17:25:18.720Z',
            'services' => [
                [
                    'name' => 'Anthropic',
                    'description' => 'All Systems Operational',
                    'latencyMs' => 201,
                    'uptime30dPct' => 92.31,
                ],
                [
                    'name' => 'OpenRouter',
                    'description' => 'API endpoint reachable',
                    'latencyMs' => 155,
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $mockResponse = new MockResponse($mockJson, [
            'http_code' => 200,
            'response_headers' => ['Content-Type' => 'application/json'],
        ]);

        $httpClient = new MockHttpClient([$mockResponse]);
        $service = new PrismixStatusService($httpClient, 'https://test.api/statuses');

        $statuses = $service->getStatuses();

        $this->assertCount(2, $statuses);
        $this->assertSame('Anthropic', $statuses[0]->name);
        $this->assertSame('All Systems Operational', $statuses[0]->description);
        $this->assertSame(201, $statuses[0]->latencyMs);
        $this->assertSame(92.31, $statuses[0]->uptime30dPct);

        $this->assertSame('OpenRouter', $statuses[1]->name);
        $this->assertSame('API endpoint reachable', $statuses[1]->description);
        $this->assertSame(155, $statuses[1]->latencyMs);
        $this->assertNull($statuses[1]->uptime30dPct);
    }

    public function testGetStatusesHandlesHttpErrorGracefully(): void
    {
        $mockResponse = new MockResponse('Forbidden', [
            'http_code' => 403,
        ]);

        $httpClient = new MockHttpClient([$mockResponse]);
        $service = new PrismixStatusService($httpClient, 'https://test.api/statuses');

        $statuses = $service->getStatuses();

        $this->assertSame([], $statuses);
    }

    public function testGetStatusesHandlesMalformedJsonGracefully(): void
    {
        $mockResponse = new MockResponse('invalid json', [
            'http_code' => 200,
            'response_headers' => ['Content-Type' => 'application/json'],
        ]);

        $httpClient = new MockHttpClient([$mockResponse]);
        $service = new PrismixStatusService($httpClient, 'https://test.api/statuses');

        $statuses = $service->getStatuses();

        $this->assertSame([], $statuses);
    }
}
