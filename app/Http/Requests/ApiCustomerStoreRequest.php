<?php

namespace App\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiCustomerStoreRequest extends FormRequest
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
            'name' => 'string|max:20|min:3',
            'email' => ['string', 'email', 'max:191', Rule::unique('users')],
            'mobile' => ['required', Rule::unique('users'), 'regex:/(01)[0-9]{9}/'],
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[a-zA-Z]/',      // must contain at least one lowercase letter
                'regex:/[0-9]/',      // must contain at least one digit
            ],
        ];
    }

    /**
     * Converting request data to json
     *
     * @return mixed
     */
    public function all($keys = null){
        if(empty($keys)){
            return parent::json()->all();
        }

        return collect(parent::json()->all())->only($keys)->toArray();
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
