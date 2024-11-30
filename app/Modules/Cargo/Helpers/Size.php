<?php

namespace App\Modules\Cargo\Helpers;

class Size
{
    public function __construct(
        protected float $length,
        protected float $width,
        protected float $height
    ) {}

    public static function make(
        float $length,
        float $width,
        float $height
    ): self
    {
        return new self(length: $length, width: $width, height: $height);
    }
}
