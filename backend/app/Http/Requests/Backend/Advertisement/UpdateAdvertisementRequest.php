<?php

namespace App\Http\Requests\Backend\Advertisement;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdvertisementRequest extends FormRequest
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
        return [
            //
            'skill_id'      => ['required'],
            'title'         => ['required'],
            'description'   => ['required'],
            'image'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'badge_img'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'phone'         => ['required', 'numeric', 'digits:10'],
            'link'          => ['required', 'url'],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['required', 'date', 'after:start_date'],
            'cost'          => ['required', 'numeric', 'min:1'],
        ];
    }
}
