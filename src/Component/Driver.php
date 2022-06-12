<?php

declare(strict_types=1);

namespace Best2Go\Best2GoParameters\Component;

use Best2GoParameters\src\Component\ParameterCollection;
use Psr\Log\LoggerInterface;

abstract class Driver implements DriverInterface
{
    abstract public function resolve(): ParameterCollection;

    public function setup(LoggerInterface $logger = null): void
    {
    }
}
