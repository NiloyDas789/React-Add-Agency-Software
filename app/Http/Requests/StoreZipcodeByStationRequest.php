<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreZipcodeByStationRequest extends FormRequest
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
            'state'         => ['required', 'string', 'min:2', 'max:2'],
            'area_code'     => ['nullable', 'string', 'min:3', 'max:3'],
            'zip_code'      => ['nullable', 'string', 'min:5', 'max:5'],
        ];
    }
}
