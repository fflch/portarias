<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;
use App\Enums\PortariaStatus;
use App\Enums\PortariaType;

class DestroyPortariaRequest extends FormRequest
{

    public function authorize(): bool {
        $portaria = $this->route('portaria');

        $isAdmin = auth()->user()->can('admin');
        $isOwnerAndEditable = (auth()->id() === $portaria->created_by) && $portaria->status->isEditable();

        return $isAdmin || $isOwnerAndEditable;
    }
    
    protected function failedAuthorization() {
        throw new HttpResponseException(
            redirect()->route('portarias.index')
                ->with('alert-danger', 'Você não tem permissão para excluir esta portaria ou ela já foi processada.')
        );
    }

    public function rules(): array {
        return [];
    }
}
