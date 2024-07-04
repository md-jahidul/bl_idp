<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:20|min:3',
            'email' => 'required|string|email|max:191|unique:users,email,' . $this->user->id,
            'mobile' => 'required|string|max:11|min:11|regex:/(01)[0-9]{9}/|unique:users,mobile,' . $this->user->id,
            'status' => 'required'
        ];
    }
}
