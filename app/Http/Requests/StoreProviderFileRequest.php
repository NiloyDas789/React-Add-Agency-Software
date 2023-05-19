<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProviderFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'provider_id' => ['required', 'exists:providers,id'],
            'received_at' => ['required', 'date'],
            'file'        => ['required', 'file'],
            'fieldMaps'   => ['required', 'string'],
        ];
    }
}
