<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProviderRequest extends FormRequest
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
            'name'                   => ['required', 'string', 'max:255', 'unique:providers,name'],
            'delivery_method'        => ['nullable', 'string', 'max:255'],
            'response_type'          => ['nullable', 'string', 'max:255'],
            'timezone'               => ['nullable', 'string', 'max:255'],
            'delivery_days'          => ['nullable', 'string', 'max:255'],
            'auto_delivery'          => ['nullable', 'boolean'],
            'file_naming_convention' => ['nullable', 'string', 'max:255'],
            'contact_name'           => ['nullable', 'string', 'max:255'],
            'contact_email'          => ['nullable', 'email', 'max:255'],
        ];
    }
}
