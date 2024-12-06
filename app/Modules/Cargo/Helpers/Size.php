<?php

namespace App\Modules\Cargo\Helpers;

class Size
{
    public function __construct(
        private readonly float $length,
        private readonly float $width,
        private float $height
    ) {}

    /**
     * @return float
     */
    public function getVolume(): float
    {
        return $this->length * $this->width * $this->height;
    }

    /**
     * @return float
     */
    public function getHeight(): float
    {
        return $this->height;
    }

    /**
     * @return float
     */
    public function getLength(): float
    {
        return $this->length;
    }

    /**
     * @return float
     */
    public function getWidth(): float
    {
        return $this->width;
    }

    public function getArea(): float
    {
        return $this->length * $this->width;
    }

    function doHigher(float $value): void {
        $this->height += $value;
    }
}
