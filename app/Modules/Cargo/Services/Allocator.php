<?php

namespace App\Modules\Cargo\Services;

use App\Modules\Cargo\Models\Cargo;
use App\Modules\Cargo\Models\Transport;
use App\Modules\Cargo\Enums\TransportType;
use App\Modules\Cargo\Helpers\Size;

class Allocator
{
    private array $transports = [];

    public function __construct()
    {
        $this->transports = [
            Transport::make(
                name: '20DC',
                type: TransportType::Sea,
                size: Size::make(length: 590.5, width: 235, height: 238.1),
                maxWeight: 21770
            ),
            Transport::make(
                name: '40DC',
                type: TransportType::Sea,
                size: Size::make(length: 1204.5, width: 235, height: 238.1),
                maxWeight: 26700
            ),
            Transport::make(
                name: 'Еврофура 82 м3',
                type: TransportType::Track,
                size: Size::make(length: 1360, width: 245, height: 245),
                maxWeight: 24000
            ),
            Transport::make(
                name: 'Еврофура 90 м3',
                type: TransportType::Track,
                size: Size::make(length: 1360, width: 245, height: 270),
                maxWeight: 24000
            ),
            Transport::make(
                name: 'Фура 100 м3',
                type: TransportType::Track,
                size: Size::make(length: 1360, width: 245, height: 300),
                maxWeight: 24000
            )
        ];
    }

    /**
     * @param  TransportType[]  $transportTypes
     * @param    $data
     * @return Transport|null
     */
    public function allocate(array $transportTypes = [], array $data = []): ?Transport
    {
        $candidates = array_filter($this->transports, fn($transport) => $transport->isCandidate($transportTypes));

        return null;
    }
}
