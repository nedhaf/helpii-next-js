<?php

namespace App\Http\Requests\Backend\Auth\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if( request()->routeIs('administrator.backend_update_user_account') ) {
            $returns = [
                'first_name'     => ['required', 'max:255'],
                'last_name'  => ['required', 'max:255'],
                'email'    => ['required', 'email', 'max:255', 'unique:users,email,'.$this->id.',uuid'],
            ];

            if( isset(request()->avatar_location) && !empty(request()->avatar_location) ){
                $returns += [
                    'avatar_location' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:1024']
                ];
            }

        } elseif ( request()->routeIs('administrator.backend_update_user_security') ) {
            $returns = [
                'password' => ['nullable','same:password_confirmation','min:6'],
                'password_confirmation' => ['nullable', 'required_with:password', 'same:password', 'min:6'],
            ];
        }

        return $returns;
    }
}
