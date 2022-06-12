<?php

declare(strict_types=1);

namespace Best2Go\Best2GoParameters\Collector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class Collector extends DataCollector
{
    public function collect(Request $request, Response $response): void
    {
        $this->data = [
            'count' => isset($_ENV['B2G_PARAMETERS_VARS']) ? count(explode(',', $_ENV['B2G_PARAMETERS_VARS'])) : 0,
            'vars' => explode(',', $_ENV['B2G_PARAMETERS_VARS'] ?? ''),
        ];
    }

    public function getName(): string
    {
        return 'best2go.data_collector.parameters';
    }

    public function reset(): void
    {
        // noop
    }

    public function getCount(): int
    {
        return $this->data['count'] ?? 0;
    }
}
