<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManagerUpdateRequest extends FormRequest
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
               'name'         => ['required', 'string'],
               'email'        => ['email', Rule::unique('users')->whereNot('email', $this->input('email'))],
               'phone'        => ['nullable'],
               'avatar'       => ['nullable'],
               'phone'        => ['nullable'],
               'whatsapp'     => ['nullable'],
               'status'       => [],

          ];
     }
}
