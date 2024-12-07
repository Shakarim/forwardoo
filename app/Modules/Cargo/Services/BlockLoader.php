<?php

namespace App\Modules\Cargo\Services;

use App\Modules\Cargo\Helpers\Block;
use App\Modules\Cargo\Helpers\PlacedBlock;
use App\Modules\Cargo\Helpers\Transport;

class BlockLoader
{
    /** @var array<PlacedBlock> $placedBlocks  */
    private array $placedBlocks = [];

    /**
     * @param  Transport  $transport
     * @param  array<Block>  $blocks
     * @return bool
     */
    public function canBeLoad(Transport $transport, array $blocks): bool
    {
        foreach ($blocks as $block) {
            $placed = false;
            // Перебираем возможные позиции для размещения
            for ($x = 0; $x <= $transport->getSize()->getLength() - $block->getSize()->getLength(); $x++) {
                for ($y = 0; $y <= $transport->getSize()->getWidth() - $block->getSize()->getWidth(); $y++) {
                    for ($z = 0; $z <= $transport->getSize()->getHeight() - $block->getSize()->getHeight(); $z++) {
                        if ($this->canPlaceBox($transport, $block, $x, $y, $z)) {
                            $this->placeBox($block, $x, $y, $z);
                            $placed = true;
                            break 3;  // Выход из всех циклов после успешного размещения
                        }
                    }
                }
            }
            return $placed;
        }
    }

    private function canPlaceBox(Transport $transport, Block $block, float $x, float $y, float $z): bool
    {
        if ($x + $block->getSize()->getLength() > $transport->getSize()->getLength() ||
            $y + $block->getSize()->getWidth() > $transport->getSize()->getWidth() ||
            $z + $block->getSize()->getHeight() > $transport->getSize()->getHeight()) {
            return false;
        }

        // Проверка на пересечение с другими ящиками
        foreach ($this->placedBlocks as $placedBlock) {
            if ($x < $placedBlock->x + $placedBlock->getBlock()->getSize()->getLength() &&
                $x + $block->getSize()->getLength() > $placedBlock->x &&
                $y < $placedBlock->y + $placedBlock->getBlock()->getSize()->getWidth() &&
                $y + $block->getSize()->getWidth() > $placedBlock->y) {
                return false;
            }
        }

        return true;
    }

    public function placeBox(Block $block, float $x, float $y, float $z): void
    {
        $this->placedBlocks[] = new PlacedBlock($block, $x, $y, $z);
    }
}
