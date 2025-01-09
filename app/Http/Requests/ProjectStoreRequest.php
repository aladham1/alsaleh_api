<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name'              => ['required', 'string'],
            'description'       => ['required', 'string'],
            'summary'       => ['nullable', 'string'],
            'created_at'        => ['required', 'string'],
            'avatar'            => ['nullable'],
            'total_paid'        => [],
            'total_requested'   => [],
            'min_donation_fee'  => [],
            'increment_by'      => [],
            'bank_name'         => [],
            'bank_branch'       => [],
            'bank_iban'         => [],
            'country'           => ['sometimes'],
            'city'              => ['sometimes'],
            'gov'               => ['sometimes'],
            'lat'               => ['required'],
            'lng'               => ['required'],
            'images'            => ['array'],
            'videos'            => ['array'],
            'super_manager'     => ['sometimes'],
            'general_manager'   => ['sometimes'],
            'financial_manager' => ['sometimes'],
            'media_manager'     => ['sometimes'],
            'is_public'         => ['required'],
//            'whatsapp'          => ['required'],
	        'in_home'		    => ['sometimes'],
        ];
    }
}
