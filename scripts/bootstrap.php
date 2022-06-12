<?php

declare(strict_types=1);

use Best2Go\Best2GoParameters\Service\Best2GoParameters;

$b2gParameters = $_ENV['B2G_PARAMETERS'] ?? false;

if (!$b2gParameters) {
    return;
}

Best2GoParameters::init($b2gParameters);
