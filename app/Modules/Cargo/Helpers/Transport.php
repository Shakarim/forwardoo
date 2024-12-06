<?php

namespace App\Modules\Cargo\Helpers;

use App\Modules\Cargo\Enums\TransportType;

readonly class Transport
{
    public function __construct(
        private readonly string $name,
        private readonly TransportType $type,
        private readonly Size $size,
        private readonly float $maxWeight
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMaxWeight(): float
    {
        return $this->maxWeight;
    }

    public function getSize(): Size
    {
        return $this->size;
    }

    public function getType(): TransportType
    {
        return $this->type;
    }
}
