<?php

namespace App\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiCustomerUpdateRequest extends FormRequest
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
        /*$user = Auth::user();*/
        $rules =  [
            'name' => 'string|max:40|min:3'
        ];

        if (isset($this->email) && $this->email != '') {
            $rules ['email'] = 'email|unique:users,email,' . 3;
        }

        return $rules;

    }

    /**
     * Validation error response
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     */
    protected function failedValidation($validator)
    {
        $errors = (new ValidationException($validator))->errors();

        $transformed_errors = [];

        foreach ($errors as $field => $message) {

            $split = explode(" ", $message[0]);

            if( $split[count($split)-1] == 'required.'){
                $errCode = 'ERR_1001';
            }else{
                $errCode = 'ERR_1002';
            }

            $transformed_errors[] =[
                'code'    =>$errCode,
                'message' => $message[0]
            ];
        }

        throw new HttpResponseException(response()->json([
            'error'  => 'invalid_request',
            'error_description' => 'Validation error',
            'message' => 'Validation error',
            'errors' => $transformed_errors,
        ], Response::HTTP_BAD_REQUEST));
    }
}
