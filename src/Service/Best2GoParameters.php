<?php

declare(strict_types=1);

namespace Best2Go\Best2GoParameters\Service;

use Best2Go\Best2GoParameters\Component\DriverFactory;

class Best2GoParameters
{
    public static function init(string $init): void
    {
        //$_SERVER['B2G_VAR'] = 'test'; // unset($_SERVER['B2G_VAR'])
        //$_ENV['B2G_VAR'] = 'test'; // unset($_ENV['B2G_VAR'])
        // (new Dotenv(false))->parse('_var=' . $init) => resolve value
        [$scheme, $config] = explode('://', $init, 2);
        $factory = new DriverFactory();
        $driver = $factory->create($scheme, $config);
        $driver->setup();
        foreach ($driver->resolve() as $parameter) {
            if (!$parameter->isEnabled()) {
                continue;
            }

            self::populate($parameter->getName(), $parameter->getValue(), $parameter->isOverride());
        }
    }

    protected static function populate(string $name, ?string $value, bool $override): void
    {
        if (!$override && isset($_ENV[$name])) {
            return;
        }

        $loadedVars = array_flip(explode(',', $_ENV['B2G_PARAMETERS_VARS'] ?? ''));
        if (!$override && isset($loadedVars[$name])) {
            return;
        }

        unset($loadedVars['']);
        $loadedVars[$name] = true;

        if ($value === null) {
            unset($_ENV[$name]);
        } else {
            $_ENV[$name] = $value;
        }

        $_ENV['B2G_PARAMETERS_VARS'] = implode(',', array_keys($loadedVars));
    }
}
