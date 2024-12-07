<?php

namespace App\Modules\Cargo\Helpers;

class PlacedBlock
{
    public function __construct(
        private Block $block,
        public readonly float $x,
        public readonly float $y,
        public readonly float $z,
    ) {
    }

    public function getBlock(): Block
    {
        return $this->block;
    }
}
