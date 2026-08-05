<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;
use App\Enums\PortariaStatus;
use App\Enums\PortariaType;

class StorePortariaRequest extends FormRequest
{
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'type' => ['required', new Enum(PortariaType::class)],
            'title' => 'required|string|max:500',
            'file' => 'required|file|mimes:docx|max:10240',
            'numbering_type' => 'required|in:auto,manual',
            'number' => [
                'exclude_if:numbering_type,auto',
                'required_if:numbering_type,manual',
                'integer',
                'min:1',
                
                Rule::unique('portarias')->where(fn ($query) => $query->where('year', $request->year))
            ],
            'year' => [
                'exclude_if:numbering_type,auto',
                'required_if:numbering_type,manual',
                'integer',
                'min:1900',
                'max:' . (now()->year + 1)
            ]
        ];
    }

    public function messages(): array {
        return [
            'number.unique' => "A portaria nº {$this->input('number')}/{$this->input('year')} já está cadastrada no sistema!"
        ];
    }
}
