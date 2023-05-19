<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOfferTollFreeNumberRequest extends FormRequest
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
            'client_id'           => ['required', 'exists:users,id'],
            'offer'               => ['required', 'exists:offers,offer'],
            'creative'            => ['required', 'exists:offers,creative'],
            'toll_free_number_id' => ['required', 'exists:toll_free_numbers,id'],
            'station_id'          => ['required', 'exists:stations,id'],
            'lead_sku'            => ['nullable', 'string', 'max:255'],
            'length'              => ['required', 'string', 'max:255'],
            'state'               => ['nullable', 'string', 'max:255', 'required_if:source_type,2'],
            'master'              => ['required', 'string', 'max:255'],
            'ad_id'               => ['required', 'string', 'max:255'],
            'source_type'         => ['required', 'in:1,2'],
            'website'             => ['nullable', 'string', 'max:255'],
            'terminating_number'  => ['nullable', 'numeric'],
            'data_type'           => ['required', 'in:1,2,3'],
            'assigned_at'         => ['required', 'date'],
            'start_at'            => ['required', 'date'],
            'end_at'              => ['nullable', 'date'],
            'test_call_at'        => ['required', 'date'],
        ];
    }

    public function messages()
    {
        return [
            'state.required_if' => 'The state field is required when source type is Shared',
        ];
    }
}
