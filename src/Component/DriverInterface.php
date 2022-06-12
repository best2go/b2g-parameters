<?php

declare(strict_types=1);

namespace Best2Go\Best2GoParameters\Component;

use Best2GoParameters\src\Component\ParameterCollection;
use Psr\Log\LoggerInterface;

interface DriverInterface
{
    public function setup(LoggerInterface $logger = null): void;
    /** @return ParameterCollection|Parameter[] */
    public function resolve(): ParameterCollection;
}
