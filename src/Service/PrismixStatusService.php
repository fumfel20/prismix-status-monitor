<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\ServiceStatus;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final class PrismixStatusService implements StatusServiceInterface
{
    public const DEFAULT_API_URL = 'https://prismix.dev/api/v1/statuses';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiUrl = self::DEFAULT_API_URL,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    /**
     * @return list<ServiceStatus>
     */
    public function getStatuses(): array
    {
        try {
            $response = $this->httpClient->request('GET', $this->apiUrl, [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Symfony-Prismix-Status-Client/1.0',
                ],
                'timeout' => 10,
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                $this->logger->error('Prismix API returned unexpected status code: {status}', [
                    'status' => $statusCode,
                ]);
                return [];
            }

            $data = $response->toArray();
            $servicesData = $data['services'] ?? [];

            if (!is_array($servicesData)) {
                return [];
            }

            $statuses = [];
            foreach ($servicesData as $serviceItem) {
                if (is_array($serviceItem)) {
                    $statuses[] = ServiceStatus::fromArray($serviceItem);
                }
            }

            return $statuses;
        } catch (Throwable $e) {
            $this->logger->error('Error fetching statuses from Prismix API: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return [];
        }
    }
}
