<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOfferRequest extends FormRequest
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
            'client_id'                    => ['required', Rule::exists('users', 'id')],
            'provider_id'                  => ['required', Rule::exists('providers', 'id')],
            'qualification_ids'            => ['required', 'array'],
            'qualification_ids.*'          => ['required', Rule::exists('qualifications', 'id')],
            'state_ids'                    => ['nullable', 'array'],
            'state_ids.*'                  => ['nullable', Rule::exists('states', 'id')],
            'offer'                        => ['required', 'string', 'max:255'],
            'creative'                     => ['required', 'string', 'max:255'],
            'billable_payout'              => ['nullable', 'numeric'],
            'media_payout'                 => ['nullable', 'numeric'],
            'dispositions'                 => ['nullable', 'string', 'max:255'],
            'billable_call_duration'       => ['nullable', 'numeric'],
            'de_dupe'                      => ['nullable', 'numeric'],
            'start_at'                     => ['required', 'date'],
            'end_at'                       => ['required', 'date'],
        ];
    }
}
