<?php

namespace App\Modules\Cargo\Services;

use App\Modules\Cargo\Enums\TransportType;
use App\Modules\Cargo\Helpers\Cargo;
use App\Modules\Cargo\Helpers\Size;
use App\Modules\Cargo\Helpers\Transport;
use App\Modules\Cargo\Helpers\Block;

class AllocationService
{
    const TRANSPORTS = [
        [
            'name' => '20DC',
            'type' => TransportType::Sea,
            'size' => ['length' => 590.5, 'width' => 235, 'height' => 238.1],
            'maxWeight' => 21770
        ],
        [
            'name' => '40DC',
            'type' => TransportType::Sea,
            'size' => ['length' => 1204.5, 'width' => 235, 'height' => 238.1],
            'maxWeight' => 26700
        ],
        [
            'name' => 'Еврофура 82 м3',
            'type' => TransportType::Track,
            'size' => ['length' => 1360, 'width' => 245, 'height' => 245],
            'maxWeight' => 24000
        ],
        [
            'name' => 'Еврофура 90 м3',
            'type' => TransportType::Track,
            'size' => ['length' => 1360, 'width' => 245, 'height' => 270],
            'maxWeight' => 24000
        ],
        [
            'name' => 'Фура 100 м3',
            'type' => TransportType::Track,
            'size' => ['length' => 1360, 'width' => 245, 'height' => 300],
            'maxWeight' => 24000
        ]
    ];

    public function __construct(
        private BlockMakingService $blockMakingService,
        private BlockLoader $blockLoader,
        private array $transports = []
    ) {
        $this->transports = array_map(function ($transport) {
            $transport['size'] = new Size(...$transport['size']);
            return new Transport(...$transport);
        }, self::TRANSPORTS);
    }

    /**
     * @param  array<Cargo>  $cargo
     * @param  array<TransportType>  $transportTypes
     * @return Transport|null
     */
    public function allocate(array $cargo, array $transportTypes): ?Transport
    {
        $candidateTransports = array_filter(
            $this->transports,
            function ($transport) use ($cargo, $transportTypes) {
                return in_array($transport->getType(), $transportTypes)
                    && $transport->getMaxWeight() >= $this->getCargoTotalWeigh($cargo)
                    && $transport->getSize()->getVolume() >= $this->getCargoTotalVolume($cargo);
            }
        );

        $blocks = $this->blockMakingService->makeBlocks($cargo);

        return match (true) {
            count($candidateTransports) === 0 => null,
            count($candidateTransports) === 1 => array_pop($candidateTransports),
            default => $this->searchForTransport($candidateTransports, $blocks)
        };
    }

    /**
     * @param  array<Transport>  $candidateTransports
     * @param  array<Block>  $blocks
     * @return Transport|null
     */
    private function searchForTransport(array $candidateTransports, array $blocks): ?Transport
    {
        $availableTransports = array_filter(
            $candidateTransports,
            fn($transport) => $this->blockLoader->canBeLoad($transport, $blocks)
        );
        if (count($availableTransports) === 0) {
            return null;
        }
        return $availableTransports[0];
    }

    /**
     * @param  array<Cargo>  $cargo
     * @return float
     */
    private function getCargoTotalWeigh(array $cargo): float
    {
        return array_reduce($cargo, fn($acc, $item) => $acc + $item->getWeight(), 0);
    }

    /**
     * @param  array<Cargo>  $cargo
     * @return float
     */
    public function getCargoTotalVolume(array $cargo): float
    {
        return array_reduce($cargo, fn($acc, $item) => $acc + $item->getSize()->getVolume(), 0);
    }
}
