<?php

declare(strict_types=1);

namespace Best2Go\Best2GoParameters\Component;

class Parameter
{
    private string $name;
    private ?string $value;
    private bool $enabled;
    private bool $override;

    public function __construct(string $name, ?string $value, bool $enabled = true, bool $override = false)
    {
        $this->name = $name;
        $this->value = $value;
        $this->enabled = $enabled;
        $this->override = $override;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isOverride(): bool
    {
        return $this->override;
    }
}
