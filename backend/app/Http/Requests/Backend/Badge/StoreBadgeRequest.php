<?php

namespace App\Http\Requests\Backend\Badge;

use Illuminate\Foundation\Http\FormRequest;

class StoreBadgeRequest extends FormRequest
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
            'badge_name'    => ['required', 'max:255', 'unique:user_badges,badge_name'],
            'image'         => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
        ];
    }
}
