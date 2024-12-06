<?php

namespace App\Modules\Cargo\Requests;

use App\Modules\Cargo\Enums\Stacking;
use App\Modules\Cargo\Enums\TransportType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class CargoFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'transport_types' => ['required', 'array', Rule::in(array_column(TransportType::cases(), 'value'))],
            'cargo' => ['required', 'array'],
            'cargo.*.length' => ['required', 'numeric'],
            'cargo.*.width' => ['required', 'numeric'],
            'cargo.*.height' => ['required', 'numeric'],
            'cargo.*.weight' => ['required', 'numeric'],
            'cargo.*.quantity' => ['required', 'numeric'],
            'cargo.*.stacking' => ['required', 'string', Rule::enum(Stacking::class)],
        ];
    }
}
