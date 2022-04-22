<?php

declare(strict_types=1);

namespace CirclicalAutoWire\Annotations;

use function str_replace;

/**
 * @Annotation
 * @Target({"METHOD","CLASS"})
 */
final class Route
{
    /** @Required */
    public string $value = '';

    public ?string $name = null;
    public ?string $type = null;
    public ?array $constraints = null;
    public ?array $defaults = null;
    public ?bool $terminate = null;
    public ?string $parent = null;
    public ?int $priority = null;

    public function setPrefix(string $path): void
    {
        $this->value = str_replace('//', '/', $path . $this->value);
    }
}
