<?php

namespace App\Modules\Cargo\Helpers;

use App\Modules\Cargo\Enums\Stacking;

readonly class Cargo
{
    public function __construct(
        private readonly Stacking $stacking,
        private readonly Size $size,
        private readonly float $weight,
        private readonly float $quantity,
    ) {
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getStacking(): Stacking
    {
        return $this->stacking;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function canBePlacesOn(self $cargo): bool
    {
        return $this !== $cargo
            && in_array($this->getStacking(), [Stacking::AnyStacking, Stacking::OnlyTop])
            && in_array($cargo->getStacking(), [Stacking::AnyStacking, Stacking::OnlyBottom])
            && ($cargo->getSize()->getArea() - $this->getSize()->getArea()) >= 0
            && $cargo->getSize()->getWidth() >= $this->getSize()->getWidth()
            && $cargo->getSize()->getLength() >= $this->getSize()->getLength();
    }
}
