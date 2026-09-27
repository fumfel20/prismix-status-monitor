<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Dto\ServiceStatus;
use App\Service\StatusServiceInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class StatusControllerTest extends WebTestCase
{
    public function testIndexPageRendersTableWithStatuses(): void
    {
        $client = static::createClient();

        $mockStatusService = $this->createStub(StatusServiceInterface::class);
        $mockStatusService->method('getStatuses')->willReturn([
            new ServiceStatus('TestService', 'All Systems Operational', 120, 99.9),
        ]);

        static::getContainer()->set(StatusServiceInterface::class, $mockStatusService);

        $crawler = $client->request('GET', '/statuses');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Service Statuses');
        $this->assertSelectorExists('table#status-table');
        $this->assertSelectorTextContains('#status-table th:nth-child(1)', 'Name');
        $this->assertSelectorTextContains('#status-table th:nth-child(2)', 'Description');
        $this->assertSelectorTextContains('#status-table th:nth-child(3)', 'Latency');
        $this->assertSelectorTextContains('#status-table th:nth-child(4)', 'Uptime 30d');
        $this->assertSelectorTextContains('#status-table tbody td:nth-child(1)', 'TestService');
        $this->assertSelectorTextContains('#status-table tbody td:nth-child(2)', 'All Systems Operational');
        $this->assertSelectorTextContains('#status-table tbody td:nth-child(3)', '120 ms');
        $this->assertSelectorTextContains('#status-table tbody td:nth-child(4)', '99.90%');
    }

    public function testHomePageRouteWorks(): void
    {
        $client = static::createClient();

        $mockStatusService = $this->createStub(StatusServiceInterface::class);
        $mockStatusService->method('getStatuses')->willReturn([]);

        static::getContainer()->set(StatusServiceInterface::class, $mockStatusService);

        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Service Statuses');
    }
}
