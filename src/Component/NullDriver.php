<?php

declare(strict_types=1);

namespace Best2Go\Best2GoParameters\Component;

use Best2GoParameters\src\Component\ParameterCollection;

class NullDriver extends Driver
{
    public function resolve(): ParameterCollection
    {
        return new ParameterCollection([]);
    }
}
