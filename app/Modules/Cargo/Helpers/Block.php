<?php

namespace App\Modules\Cargo\Helpers;

use App\Modules\Cargo\Helpers\Cargo;

class Block
{
    private Size $size;

    public function __construct(
        private readonly Cargo $bottom,
        private readonly ?Cargo $top = null
    ) {
        $this->size = clone $this->bottom->getSize();
        if (!is_null($this->top)) {
            $this->size->doHigher($this->top->getSize()->getHeight());
        }
    }

    public function getSize(): Size
    {
        return $this->size;
    }

    public function getBottom(): Cargo
    {
        return $this->bottom;
    }

    public function getTop(): ?Cargo
    {
        return $this->top;
    }

    public function getWeight(): float
    {
        return $this->bottom->getWeight() + ($this->top?->getWeight() ?? 0);
    }
}
