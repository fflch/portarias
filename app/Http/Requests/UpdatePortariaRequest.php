<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;
use App\Enums\PortariaStatus;
use App\Enums\PortariaType;

class UpdatePortariaRequest extends FormRequest
{
    public function authorize(): bool {
        $portaria = $this->route('portaria');

        return $portaria->status->isEditable();
    }

    public function failedAuthorization() {
        $portaria = $this->route('portaria');

        throw new HttpResponseException(
            redirect()->route('portarias.show', $poratria)
                ->with('alert-danger', "Esta portaria não pode mais ser alterada.")
        );
    }

    public function rules(): array {
        $portaria = $this->route('portaria');

        $canEditNumber = auth()->user()->can('admin');

        $rules = [
            'type'   => ['required', new Enum(PortariaType::class)],
            'title'  => ['required', 'string', 'max:500'],
            'status' => ['required', new Enum(PortariaStatus::class)],
            'file'   => ['nullable', 'file', 'mimes:docx', 'max:10240'],
        ];

        if ($canEditNumber) {
            $rules['number'] = [
                'nullable',
                'integer',
                'min:1',
                Rule::unique('portarias')->ignore($portaria->id)->where(function ($query) use ($portaria) {
                    return $query->where('year', $this->input('year', $portaria->year));
                })
            ];

            $rules['year'] = ['nullable', 'integer', 'digits:4'];
        }
        return $rules;
    }

    public function messages(): array {
        return [
            'number.unique' => "A portaria nº {$this->input('number')}/{$this->input('year')} já existe no sistema!"
        ];
    }
}
