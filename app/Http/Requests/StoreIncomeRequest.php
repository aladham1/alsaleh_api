<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncomeRequest extends FormRequest
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
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'total' => ['required', 'numeric'],
            'description' => ['required'],
            'paid_at' => ['required'],
            'paid_to' => ['required'],
            'images' => ['array'],
        ];
    }
}
