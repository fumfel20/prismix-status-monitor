<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class ServiceStatus
{
    public function __construct(
        public string $name,
        public string $description,
        public ?int $latencyMs,
        public ?float $uptime30dPct,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $latency = isset($data['latencyMs']) && is_numeric($data['latencyMs'])
            ? (int) round((float) $data['latencyMs'])
            : null;

        $uptime = isset($data['uptime30dPct']) && is_numeric($data['uptime30dPct'])
            ? (float) $data['uptime30dPct']
            : null;

        return new self(
            name: (string) ($data['name'] ?? 'Unknown Service'),
            description: (string) ($data['description'] ?? 'No description'),
            latencyMs: $latency,
            uptime30dPct: $uptime,
        );
    }
}
