<?php

namespace App\Http\Controllers;

use App\Modules\Cargo\Enums\Stacking;
use App\Modules\Cargo\Enums\TransportType;
use App\Modules\Cargo\Helpers\Cargo;
use App\Modules\Cargo\Helpers\Size;
use App\Modules\Cargo\Requests\CargoFormRequest;
use App\Modules\Cargo\Services\AllocationService;
use Illuminate\Http\JsonResponse;

class AllocController extends Controller
{
    public function __construct(
        protected AllocationService $allocator
    ) {
    }

    public function index(CargoFormRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $cargo = array_map(function ($item) {
            $size = new Size(...['length' => $item['length'], 'width' => $item['width'], 'height' => $item['height']]);
            return new Cargo(...[
                'stacking' => Stacking::from($item['stacking']),
                'size' => $size,
                'quantity' => $item['quantity'],
                'weight' => $item['weight'],
            ]);
        }, $validated['cargo']);

        $transportTypes = array_map(function ($item) {
            return TransportType::from($item);
        }, $validated['transport_types']);

        if ($transport = $this->allocator->allocate($cargo, $transportTypes)) {
            return response()->json(['transport' => $transport->getName()]);
        }

        return response()->json(['message' => 'transport not found'], 404);
    }
}
