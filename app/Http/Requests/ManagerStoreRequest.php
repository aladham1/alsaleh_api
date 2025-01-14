<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManagerStoreRequest extends FormRequest
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
               'password'     => ['required', 'string'],
//               'email'        => ['email', 'nullable', 'unique:users'],
               'username'        => ['string', 'nullable', 'unique:users'],
               'phone'        => ['nullable'],
               'avatar'       => ['nullable'],
               'whatsapp'     => ['nullable'],
               'status'       => [],
          ];
     }
}
