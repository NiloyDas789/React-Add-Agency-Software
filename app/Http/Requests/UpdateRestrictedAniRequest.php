<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRestrictedAniRequest extends FormRequest
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
            'restricted_ani'    => ['required', 'numeric'],
            'date'              => ['nullable', 'date'],
            'reason'            => ['nullable', 'string', 'max:255'],
            'status'            => ['nullable', 'boolean'],
        ];
    }
}
