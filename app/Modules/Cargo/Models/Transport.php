<?php

namespace App\Modules\Cargo\Models;

use App\Modules\Cargo\Enums\TransportType;
use App\Modules\Cargo\Helpers\Size;

class Transport
{
    public function __construct(
        protected string $name,
        protected TransportType $type,
        protected Size $size,
        protected int $maxWeight
    ) {
    }

    public static function make(
        string $name,
        TransportType $type,
        Size $size,
        int $maxWeight
    ): self {
        return new self(name: $name, type: $type, size: $size, maxWeight: $maxWeight);
    }

    public function isCandidate($types = []): bool {
        return in_array($this->type, $types);
    }
}
