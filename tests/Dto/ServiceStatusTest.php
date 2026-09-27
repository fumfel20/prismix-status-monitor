<?php

declare(strict_types=1);

namespace App\Tests\Dto;

use App\Dto\ServiceStatus;
use PHPUnit\Framework\TestCase;

final class ServiceStatusTest extends TestCase
{
    public function testFromArrayWithCompleteData(): void
    {
        $data = [
            'name' => 'OpenAI',
            'description' => 'All Systems Operational',
            'latencyMs' => 191,
            'uptime30dPct' => 99.27,
        ];

        $status = ServiceStatus::fromArray($data);

        $this->assertSame('OpenAI', $status->name);
        $this->assertSame('All Systems Operational', $status->description);
        $this->assertSame(191, $status->latencyMs);
        $this->assertSame(99.27, $status->uptime30dPct);
    }

    public function testFromArrayWithMissingData(): void
    {
        $data = [];

        $status = ServiceStatus::fromArray($data);

        $this->assertSame('Unknown Service', $status->name);
        $this->assertSame('No description', $status->description);
        $this->assertNull($status->latencyMs);
        $this->assertNull($status->uptime30dPct);
    }

    public function testFromArrayWithNumericStringAndFloatLatency(): void
    {
        $data = [
            'name' => 'Anthropic',
            'description' => 'Degraded',
            'latencyMs' => '155.6',
            'uptime30dPct' => '98.5',
        ];

        $status = ServiceStatus::fromArray($data);

        $this->assertSame('Anthropic', $status->name);
        $this->assertSame('Degraded', $status->description);
        $this->assertSame(156, $status->latencyMs);
        $this->assertSame(98.5, $status->uptime30dPct);
    }
}
