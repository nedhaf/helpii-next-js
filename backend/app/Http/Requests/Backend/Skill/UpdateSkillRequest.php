<?php

namespace App\Http\Requests\Backend\Skill;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSkillRequest extends FormRequest
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
        $returns = [
            'name'     => ['required', 'max:255', 'unique:skills,name,'.$this->id.',id'],

        ];
        if( isset(request()->avatar) && !empty(request()->avatar) ){
            $returns += [
                'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
            ];
        }
        return $returns;
    }
}
