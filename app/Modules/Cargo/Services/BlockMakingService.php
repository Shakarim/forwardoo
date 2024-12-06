<?php

namespace App\Modules\Cargo\Services;

use App\Modules\Cargo\Enums\Stacking;
use App\Modules\Cargo\Helpers\Block;
use App\Modules\Cargo\Helpers\Cargo;
use App\Modules\Cargo\Helpers\Size;

class BlockMakingService
{
    /**
     * @param  array<Cargo>  $data
     */
    public function __construct(
        private array $data = []
    ) {
    }

    /**
     * @return array<Block>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param  array<Cargo>  $cargo
     * @return array<Block>
     */
    public function makeBlocks(array &$cargo): array
    {
        // Sort cargo list from lowest to highest by height
        usort($cargo, fn($a, $b) => $a->getSize()->getHeight() <=> $b->getSize()->getHeight());

        // make blocks for cargo we have information about
        $this
            ->makeBlocksForNoStackingCargo($cargo)
            ->makeBlocksForOnlyTopCargo($cargo)
            ->makeBlocksForAnyStacking($cargo);

        // if left a cargo with no places in blocks should be placed his own blocks
        if (count($cargo) > 0) {
            $this->makeBlocksForAllLeftCargo($cargo);
        }

        return $this->getData();
    }

    private function makeBlocksForNoStackingCargo(array &$cargo): self
    {
        $data = array_filter($cargo, fn($item) => $item->getStacking() === Stacking::NoStacking);

        $this->data = [...$this->data, ...$this->makeUnpairedBlocks($data, $cargo)];

        return $this;
    }

    private function makeBlocksForAllLeftCargo(array &$cargo): self
    {
        $this->data = [...$this->data, ...$this->makeUnpairedBlocks($cargo, $cargo)];

        return $this;
    }

    private function makeBlocksForOnlyTopCargo(array &$cargo): self
    {
        // Make an array with objects we need to find a pair
        $data = array_filter($cargo, fn($item) => $item->getStacking() === Stacking::OnlyTop);

        // Override the data with the array contains better block configuration
        $this->data = [...$this->data, ...$this->makePairsForBlocks($data, $cargo)];

        return $this;
    }

    private function makeBlocksForAnyStacking(array &$cargo): self
    {
        // Make an array with objects we need to find a pair
        $data = array_filter($cargo, fn($item) => $item->getStacking() === Stacking::AnyStacking);

        // Override the data with the array contains better block configuration
        $this->data = [...$this->data, ...$this->makePairsForBlocks($data, $cargo)];

        return $this;
    }

    private function makeUnpairedBlocks(array $bottomCargo, &$cargo): array
    {
        return array_reduce($bottomCargo, function ($acc, $item) use (&$cargo) {
            $acc[] = new Block(bottom: $item);
            self::removeCargoFromList($item, $cargo);
            return $acc;
        }, []);
    }

    /**
     * @param  array<Cargo>  $topCargo
     * @param $cargo
     * @return array
     */
    private function makePairsForBlocks(array $topCargo, &$cargo): array
    {
        // Sort items from higher to lower by his area
        usort($topCargo, fn($a, $b) => $b->getSize()->getArea() <=> $a->getSize()->getArea());

        return array_reduce($topCargo, function ($acc, $topLoad) use (&$cargo) {
            // Make a list of a possible blocks
            $candidateBlocks = array_reduce($cargo, function ($acc, $candidateBottom) use ($topLoad) {
                if ($topLoad->canBePlacesOn($candidateBottom)) {
                    $acc[] = new Block(bottom: $candidateBottom, top: $topLoad);
                }
                return $acc;
            }, []);

            // sort possible blocks from lowest to biggest by height
            usort($candidateBlocks, fn($a, $b) => $a->getSize()->getHeight() <=> $b->getSize()->getHeight());

            // if first item are exists - remove two found element from list and add new block to result data
            if (isset($candidateBlocks[0])) {
                $acc[] = $candidateBlocks[0];
                self::removeCargoFromList($candidateBlocks[0]->getTop(), $cargo);
                self::removeCargoFromList($candidateBlocks[0]->getBottom(), $cargo);
            }

            return $acc;
        }, []);
    }

    /**
     * @param  Cargo  $block
     * @param $list
     * @return void
     */
    private static function removeCargoFromList(Cargo $block, &$list): void
    {
        foreach ($list as $key => $item) {
            if ($block == $item) {
                unset($list[$key]);
            }
        }
    }
}
