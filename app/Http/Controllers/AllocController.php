<?php

namespace App\Http\Controllers;

use App\Modules\Cargo\Enums\Stacking;
use App\Modules\Cargo\Enums\TransportType;
use App\Modules\Cargo\Models\Cargo;
use App\Modules\Cargo\Services\Allocator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AllocController extends Controller
{
    public function __construct(
        protected Allocator $allocator
    ) {
    }

    public function index(): View
    {
        // Getting the data
        $content = File::get(public_path('cargo_for_task.json'));
        $json = json_decode($content, true);

        $validator = Validator::make($json, [
            'transport_type' => ['required', Rule::enum(TransportType::class)],
            'cargo' => ['required', 'array'],
            'cargo.*.length' => ['required', 'numeric'],
            'cargo.*.width' => ['required', 'numeric'],
            'cargo.*.height' => ['required', 'numeric'],
            'cargo.*.weight' => ['required', 'numeric'],
            'cargo.*.quantity' => ['required', 'numeric'],
            'cargo.*.stacking' => ['required', Rule::enum(Stacking::class)],
        ]);

        var_dump($validator->fails());
        var_dump($validator->errors());

        die;

        // Prepare data for service
        $transportTypes = array_map(
            fn($item) => TransportType::from($item),
            $json['transport_type'] ? [$json['transport_type']] : []
        );
        $cargos = array_map(fn($cargoAttrs) => (new Cargo())->fill($cargoAttrs), $json['cargo']);

        // Calling service
        $this->allocator->allocate($transportTypes, $cargos);

        return view('alloc.index', [
            'data' => []
        ]);
    }
}
