<?php

declare(strict_types=1);

namespace Best2Go\Best2GoParameters\Component;

use InvalidArgumentException;

class DriverFactory
{
    public function create(string $scheme, $config): DriverInterface
    {
        switch ($scheme) {
            case 'dbal+mysql':
                return $this->createDbalDriver($scheme, $config);
            case 'fqn': // FQN (user provider dataset)
            case 'json': // JSON-encoded parameters
            case 'chain': // comma-separated ENV variables with provider
            case 'consul': // https://www.consul.io/docs/dynamic-app-config/kv
            case 'aws-ssm': // https://docs.aws.amazon.com/secretsmanager/latest/userguide/intro.html
                return $this->createNullDriver($scheme, $config);
            default:
                throw new InvalidArgumentException('Unknown scheme "' . $scheme . '"');
        }
    }

    protected function createDbalDriver(string $scheme, string $config): Driver
    {
        return new DbalDriver($scheme, $config);
    }

    protected function createNullDriver(string $scheme, string $config): NullDriver
    {
        return new NullDriver();
    }
}
