<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\ServiceStatus;

interface StatusServiceInterface
{
    /**
     * @return list<ServiceStatus>
     */
    public function getStatuses(): array;
}
