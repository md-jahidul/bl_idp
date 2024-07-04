<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidPasswordException;
use App\Exceptions\OtpException;
use App\Http\Requests\ApiCustomerPasswordChangeRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Traits\FileTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Auth\Grants\OtpVerifierFactory;
use App\Exceptions\UserNotFoundException;
use App\Http\Resources\CustomerStoreResource;
use App\Http\Requests\ApiCustomerStoreRequest;
use App\Http\Resources\CustomerUpdateResource;
use App\Http\Requests\ApiCustomerUpdateRequest;
use App\Http\Requests\ApiCustomerPasswordUpdateRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Passport\Bridge\RefreshToken;
use Laravel\Passport\Token;
use Lcobucci\JWT\Parser;
use mysql_xdevapi\Exception;

class CustomerController extends Controller
{
    use FileTrait;

    /**
     * Registering Customer
     *
     * @param  \App\Http\Requests\ApiCustomerStoreRequest  $request
     * @return \Illuminate\Http\Response
     *
     *
     * @SWG\Post(
     *      path="/customers",
     *      operationId="store",
     *      tags={"Customers"},
     *      summary="Cusomer Registration Api",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      description="This api registers Custmer",
     *      @SWG\Parameter(
     *         description="Body data",
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *             properties={
     *               @SWG\Property(property="name", type="string", description="Customer Name"),
     *               @SWG\Property(property="email", type="string", description="Customer Email"),
     *               @SWG\Property(property="password", type="string", description="Password"),
     *               @SWG\Property(property="password_confirmation", type="string", description="Confire Password"),
     *               @SWG\Property(property="mobile", type="string", description="Customer Mobile"),
     *             },
     *         ),
     *      ),
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="authorization header token",
     *          required=true,
     *          type="string",
     *          in="header"
     *      ),
     *
     *      @SWG\Response(response=200, description="Successful Operation"),
     *      @SWG\Response(response=400, description="Bad Request"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *      @SWG\Response(response=404, description="Resource Not Found"),
     *      @SWG\Response(response=500, description="Server Error"),
     * )
     */
    public function store(ApiCustomerStoreRequest $request)
    {
        $customer = new User();
        $customer->name = $request->has('name') ? $request->input('name') : null;
        $customer->email = $request->has('email') ? $request->input('email') : null;
        $customer->first_name = $request->has('first_name') ? $request->input('first_name') : null;
        $customer->last_name = $request->has('last_name') ? $request->input('last_name') : null;
        $customer->address = $request->has('address') ? $request->input('address') : null;
        $customer->is_password_set = $request->has('is_password_set') ? $request->input('is_password_set') : 0;

        $customer->username = $request->input('mobile');
        $customer->mobile = $request->input('mobile');
        $customer->msisdn = '88'.$request->input('mobile');
        $customer->password = Hash::make($request->input('password'));
        $customer->password_grant = null;
        $customer->status = User::ACTIVE;
        $customer->user_type = 'CUSTOMER';

        if ($request->hasFile('profile_photo')) {
            $uploadResult = $this->uploadImage($request);
            if ($uploadResult['status'] == 'success') {
                $customer->profile_image_base64 = $uploadResult['path'];
            }
        }

        $customer->save();

        $customer->assignRole([3]); // Giving idp-customer role

        if ($customer) {
            return new CustomerStoreResource($customer);
        }

        throw new \InvalidArgumentException('Cannot save customer');
    }

    public function check_base64($base64)
    {
        return (bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $base64);
    }

    /**
     * Updating Customer
     *
     * @param  \App\Http\Requests\ApiCustomerUpdateRequest  $request
     * @return \Illuminate\Http\Response
     *
     *
     * @SWG\Put(
     *      path="/customers/{msisdn}",
     *      operationId="update",
     *      tags={"Customers"},
     *      summary="Update Customer's Information",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      description="This api updates Custmer",
     *      @SWG\Parameter(
     *          name="msisdn",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *         description="Body data",
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *             properties={
     *               @SWG\Property(property="name", type="string", description="Customer Name"),
     *               @SWG\Property(property="email", type="string", description="Customer Email")
     *             },
     *         ),
     *      ),
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="authorization header token",
     *          required=true,
     *          type="string",
     *          in="header"
     *      ),
     *
     *      @SWG\Response(response=200, description="Successful Operation"),
     *      @SWG\Response(response=400, description="Bad Request"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *      @SWG\Response(response=404, description="Resource Not Found"),
     *      @SWG\Response(response=500, description="Server Error"),
     * )
     */
    public function update(Request $request)
    {
        $customer = Auth::user();

        $rules = [
            'name' => 'string|max:40|min:3',
            'mobile' => 'string|max:11|min:11|regex:/(01)[0-9]{9}/|unique:users,mobile,'.$customer->id,
        ];
        if (isset($request->email) && $request->email != '') {
            $rules ['email'] = 'email|unique:users,email,'.$customer->id;
        }

        $request->validate($rules);


        $uploadError = '';
        //TODO:remove mobile and msisdn param
        $data = $request->all();
        if (!is_null($customer)) {
            if ($request->has('profile_photo')) {
                if (!$this->check_base64($request->profile_photo)) {
                    return ['status' => 'error', 'message' => 'Invalid image extension'];
                }
                $data['profile_image_base64'] = $request->profile_photo;
            }

            $customer->update($data);

            if ($customer) {
                return new CustomerUpdateResource($customer);
            }
        } else {
            throw new UserNotFoundException();
        }

        return response()->json([
            'message' => 'Customer has been updated successfully! '.$uploadError,
        ], 200);
    }

    public function invalidateAccessTokens($user_id, $token_id = null)
    {
       // Log::info('current token : ' . $token_id);
        try {
            $token = Token::where('user_id', $user_id);

            if ($token_id) {
                $token->where('id', '<>', $token_id);
            }

            $token->update(['revoked' => true]);

            $tokens = Token::where('user_id', $user_id);

            if ($token_id) {
                $tokens->where('id', '<>', $token_id);
            }

            $tokens = $tokens->pluck('id')->toArray();

            if (!empty($tokens)) {
                DB::table('oauth_refresh_tokens')
                    ->whereIn('access_token_id', $tokens)->update(['revoked' => true]);
            }

            //Log::info("Invalidate Token : Called");
        } catch (\Exception $e) {
            Log::error("Invalidate Token : ".$e->getMessage());
        }
    }

    /**
     * Updating Customer Password
     *
     * @param  \App\Http\Requests\ApiCustomerPasswordUpdateRequest  $request
     * @return \Illuminate\Http\Response
     *
     *
     * @SWG\Put(
     *      path="/customers/forget/password",
     *      operationId="forgetPassword",
     *      tags={"Customers"},
     *      summary="Update Customer's Password",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      description="This api updates Custmer's Password",
     *      @SWG\Parameter(
     *         description="Body data",
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *             properties={
     *               @SWG\Property(property="otp", type="string", description="OTP"),
     *               @SWG\Property(property="mobile", type="string", description="User Mobile No"),
     *               @SWG\Property(property="password", type="string", description="Password"),
     *               @SWG\Property(property="password_confirmation", type="string", description="Confirm Password")
     *             },
     *         ),
     *      ),
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="authorization header token",
     *          required=true,
     *          type="string",
     *          in="header"
     *      ),
     *
     *      @SWG\Response(response=200, description="Successful Operation"),
     *      @SWG\Response(response=400, description="Bad Request"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *      @SWG\Response(response=404, description="Resource Not Found"),
     *      @SWG\Response(response=500, description="Server Error"),
     * )
     */
    public function forgetPassword(ApiCustomerPasswordUpdateRequest $request)
    {
        $otpVerifierType = ($request->has('otp_verifier')) ? $request->input('otp_verifier') : 'BL_INTERNAL';
        $otpVerifier = OtpVerifierFactory::getOtpVerifier($otpVerifierType);

        if (is_null($otpVerifier)) {
            return response()->json(([
                "error" => "invalid_otp_verifier.",
                "error_description" => "Please provide valid OTP verifier. By default, BL_INTERNAL OTP verifier is used.",
                "message" => "Please provide valid OTP verifier. By default, BL_INTERNAL OTP verifier is used."
            ]), 401);
        }

        $otp = $request->input('otp');
        $mobile = $request->input('mobile');
        $isValidOtp = $otpVerifier->verify($otp, $mobile);

        if (!$isValidOtp) {
            return response()->json(([
                "error" => "invalid_otp",
                "error_description" => "Invalid OTP.",
                "message" => "Invalid OTP."
            ]), 401);
        }

        $customer = $this->getUserByMobile($request->input('mobile'));

        if (!is_null($customer)) {
            $customer->update([
                'password' => Hash::make($request->input('password'))
            ]);

            // invalidate all access tokens

            $this->invalidateAccessTokens($customer->id);
        } else {
            throw new UserNotFoundException();
        }

        return response()->json([
            'message' => 'Password updated successfully!',
        ], 200);
    }

    /**
     * Change Customer Password
     *
     * @param  \App\Http\Requests\ApiCustomerPasswordChangeRequest  $request
     * @return \Illuminate\Http\Response
     *
     *
     * @SWG\Put(
     *      path="/customers/change/password",
     *      operationId="changePassword",
     *      tags={"Customers"},
     *      summary="Update Customer's Password",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      description="This api updates Customer's Password",
     *      @SWG\Parameter(
     *         description="Body data",
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *             properties={
     *               @SWG\Property(property="oldPassword", type="string", description="Old password of the user"),
     *               @SWG\Property(property="mobile", type="string", description="User Mobile No"),
     *               @SWG\Property(property="newPassword", type="string", description="New Password")
     *             },
     *         ),
     *      ),
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="authorization header token",
     *          required=true,
     *          type="string",
     *          in="header"
     *      ),
     *
     *      @SWG\Response(response=200, description="Successful Operation"),
     *      @SWG\Response(response=400, description="Bad Request"),
     *      @SWG\Response(response=401, description="Unauthorized"),
     *      @SWG\Response(response=404, description="Resource Not Found"),
     *      @SWG\Response(response=500, description="Server Error"),
     * )
     */
    public function changePassword(ApiCustomerPasswordChangeRequest $request)
    {
        $oldPassword = $request->input('oldPassword');
        $newPassword = $request->input('newPassword');
        $mobile = $request->input('mobile');
        $customer = $this->getUserByMobile($mobile);

        if (!is_null($customer)) {
            if (Hash::check($oldPassword, $customer->password)) {
                $customer->update([
                    'password' => Hash::make($newPassword)
                ]);

                // invalidate all access tokens
                $token_id = null;
                if($request->has('customer_token')){
                    $value = $request->customer_token;
                    $token_id= (new Parser())->parse($value)->getHeader('jti');
                }

                $this->invalidateAccessTokens($customer->id, $token_id);
            } else {
                throw new InvalidPasswordException('Invalid current password');
            }
        } else {
            throw new UserNotFoundException();
        }

        return response()->json([
            'message' => 'Password updated successfully!',
        ], 200);
    }


    /**
     * @param Request $request
     * @return CustomerUpdateResource|array|\Illuminate\Http\JsonResponse
     * @throws UserNotFoundException
     */
   public function updateProfileInfo(Request $request)
   {
       $data = $request->all();

       if(isset($data['mobile'])){
           $customer = $this->getUserByMobile($data['mobile']);

           if (empty($customer)){
               return $this->sendErrorResponse('Sorry! customer not found', [], 404);
           }

       } else {

           return $this->sendErrorResponse('mobile field is required', [], 422);
           //return ['status' => 'error', 'message' => 'mobile field is required '];
       }


       $rules = [
           'name' => 'string|max:40|min:3',
           'mobile' => 'string|max:11|min:11|regex:/(01)[0-9]{9}/|unique:users,mobile,'.$customer->id,
       ];
       if (isset($request->email) && $request->email != '') {
           $rules ['email'] = 'email|unique:users,email,'.$customer->id;
       }

       $request->validate($rules);

       $uploadError = '';
       if (!is_null($customer)) {
           if ($request->has('profile_photo')) {
               if (!$this->check_base64($request->profile_photo)) {

                   return $this->sendErrorResponse('Invalid image extension', [], 422);

               }
               $data['profile_image_base64'] = $request->profile_photo;

           }

           try{
               $customer->update($data);

           } catch (Exception $exception){
               return $this->sendErrorResponse($exception->getMessage(), [], 422);
           }


           if ($customer) {
               $formatted_data = new CustomerUpdateResource($customer);
               return $this->sendSuccessResponse( $formatted_data, 'Customer Info', [], 200);
           }
       } else {
           throw new UserNotFoundException();
       }

       return response()->json([
           'message' => 'Customer has been updated successfully! '.$uploadError,
       ], 200);

   }


    /**
     * @param Request $request
     * @return CustomerUpdateResource|array|\Illuminate\Http\JsonResponse
     * @throws UserNotFoundException
     */
    public function updateProfileImage(Request $request)
    {
        $data = $request->all();

        if(isset($data['mobile'])){
            $customer = $this->getUserByMobile($data['mobile']);

            if (empty($customer)){
                return $this->sendErrorResponse('Sorry! customer not found', [], 404);
            }

        } else {

            return $this->sendErrorResponse('mobile field is required', [], 422);
        }


        $rules = [
            'name' => 'string|max:40|min:3',
            'mobile' => 'string|max:11|min:11|regex:/(01)[0-9]{9}/|unique:users,mobile,'.$customer->id,
        ];
        if (isset($request->email) && $request->email != '') {
            $rules ['email'] = 'email|unique:users,email,'.$customer->id;
        }

        $request->validate($rules);

        $uploadError = '';
        if (!is_null($customer)) {
            if ($request->has('profile_photo')) {
                if (!$this->check_base64($request->profile_photo)) {

                    return $this->sendErrorResponse('Invalid image extension', [], 422);
                }
                $data['profile_image_base64'] = $request->profile_photo;
            }

            try{
                $customer->update( ["profile_image_base64" => $data['profile_image_base64']]);
            } catch (Exception $exception){
                return $this->sendErrorResponse($exception->getMessage(), [], 422);
            }

            if ($customer) {
                $formatted_data = new CustomerUpdateResource($customer);
                return $this->sendSuccessResponse( $formatted_data, 'Customer Info', [], 200);
            }
        } else {
            throw new UserNotFoundException();
        }

    }


    /**
     * @param $result
     * @param $message
     * @param array $pagination
     * @param int $http_status
     * @param int $status_code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSuccessResponse(
        $result,
        $message,
        $pagination = [],
        $http_status = 200,
        $status_code = 200
    ) {
        $response = [
            'status' => 'SUCCESS',
            'status_code' => $status_code,
            'message' => $message,
            'data' => $result
        ];

        if (!empty($pagination)) {
            $response ['pagination'] = $pagination;
        }

        return response()->json($response, $http_status);
    }


    /**
     * Return error response.
     *
     * @param $message
     * @param array $errorMessages
     * @param int $status_code
     * @return JsonResponse
     */
    public function sendErrorResponse($message, $errorMessages = [], $status_code = 422)
    {
        $response = [
            'status' => 'FAIL',
            'status_code' => $status_code,
            'message' => $message,
        ];

        if (!empty($errorMessages)) {
            $response['error'] = $errorMessages;
        }

        return response()->json($response, $status_code);
    }


    public function resetPasswordRequest(Request $request)
    {
        $otp = $request->input('otp');
        $mobile = $request->input('mobile');
        $otpVerifier = OtpVerifierFactory::getOtpVerifier('BL_INTERNAL');

        if ($otpVerifier->verify($otp, $mobile)) {
            //TODO: store random token to db
            return Str::random();
        }

        return new \InvalidArgumentException('Invalid request');
    }

    public function resetPassword(Request $request)
    {
        $token = $request->input('sessionToken');
        if (!$this->verifyRandomToken($token)) {
            return new AuthenticationException('Invalid session token');
        }
        $password = $request->input('password');
        $mobile = $request->input('mobile');
        $customer = $this->getUserByMobile($mobile);

        if (!is_null($customer)) {
            $customer->update([
                'password' => Hash::make($password),
                'is_password_set' => true
            ]);
            // invalidate all access tokens

            //$this->invalidateAccessTokens($customer->id);
        } else {
            throw new UserNotFoundException();
        }

        return response()->json([
            'message' => 'Password updated successfully!',
        ], 200);
    }

    public function setPassword(Request $request)
    {
        $customer = Auth::user();
        $rules = [
            'otp' => 'required',
            'password' => [
                'required',
                'min:8',
                'regex:/[a-zA-Z]/',      // must contain at least one lowercase letter
                'regex:/[0-9]/'      // must contain at least one digit
            ],
        ];

        $request->validate($rules);

        $password = $request->input('password');

        $otpVerifier = OtpVerifierFactory::getOtpVerifier('BL_INTERNAL');

        if (!$otpVerifier->verify($request->otp, $customer->mobile)) {
            return response()->json(([
                'error' => 'otp_invalid',
                'error_description' => 'OTP is invalid',
                'message' => 'OTP is invalid',
            ]), 400);
        }

        if ($customer->is_password_set) {
            return response()->json(([
                'error' => 'password_already_set',
                'error_description' => 'User password is set already',
                'message' => 'User password is set already',
            ]), 400);
        }

        if (!is_null($customer)) {
            $customer->update([
                'password' => Hash::make($password),
                'is_password_set' => true
            ]);
        } else {
            throw new UserNotFoundException();
        }

        return response()->json([
            'message' => 'Password set successfully!',
        ], 200);
    }


    private function verifyRandomToken($token)
    {
        //TODO: Retrieve session token from db and validate with time
        return true;
    }

    /**
     * Get User By Mobile
     *
     * @param  string  $mobile
     * @return App\Models\User;
     *
     */
    public function getUserByMobile($mobile)
    {
        return User::where('username', $mobile)->first();
    }


    /**
     * Get User By Mobile
     *
     * @param string $mobile
     * @return App\Models\User;
     *
     */
    public function getCustomerInfoWithoutImageByMobile($mobile)
    {
        return User::where('username', $mobile)
                ->select('name', 'first_name', 'last_name', 'address', 'birth_date',
                    'username', 'msisdn', 'email', 'mobile', 'status', 'user_type',
                    'is_password_set', 'gender', 'alternate_phone')
                ->first();
    }

    /**
     * @param $mobile
     * @return mixed
     */
    public function getCustomerProfileImageByMobile($mobile)
    {
        return User::where('username', $mobile)
            ->select('name', 'mobile', 'profile_image_base64')->first();
    }


    /**
     * Get User Details
     *
     * @param  string  $mobile
     * @return App\Models\User;
     *
     * @throws UserNotFoundException
     */
    public function show($mobile)
    {
        $customer = $this->getUserByMobile($mobile);

        if (!is_null($customer)) {
            $data = new \stdClass();
            $data->name = $customer->name;
            $data->username = $customer->username;
            $data->status = $customer->status;
            $data->msisdn = $customer->msisdn;
            $data->mobile = $customer->mobile;
            $data->created_at = $customer->created_at;
            $data->first_name = $customer->first_name;
            $data->last_name = $customer->last_name;
            $data->birth_date = $customer->birth_date;
            $data->address = $customer->address;
            $data->gender = $customer->gender;
            $data->email = $customer->email;
            $data->alternate_phone = $customer->alternate_phone;
            $data->is_password_set = $customer->is_password_set;
            $data->profile_image = $customer->profile_image_base64;// ? config('filesystems.profile_image_path').$customer->profile_image : null;

            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
        } else {
            throw new UserNotFoundException();
        }
    }

    /**
     * Get User Details Without Image
     *
     * @param string $mobile
     * @return App\Models\User;
     *
     * @throws UserNotFoundException
     */
    public function getCustomerInfoWithoutProfileImage($mobile)
    {
        $customer = $this->getCustomerInfoWithoutImageByMobile($mobile);

        if (!is_null($customer)) {

            $data = new \stdClass();
            $data->name = $customer->name;
            $data->username = $customer->username;
            $data->msisdn = $customer->msisdn;
            $data->mobile = $customer->mobile;
            $data->first_name = $customer->first_name;
            $data->last_name = $customer->last_name;
            $data->birth_date = $customer->birth_date;
            $data->address = $customer->address;
            $data->gender = $customer->gender;
            $data->email = $customer->email;
            $data->alternate_phone = $customer->alternate_phone;
            $data->is_password_set = $customer->is_password_set;

            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
        } else {
            throw new UserNotFoundException();
        }

    }


    public function getCustomerProfileImage($mobile)
    {
        $customer = $this->getCustomerProfileImageByMobile($mobile);

        if (!is_null($customer)) {
            $data = new \stdClass();
            $data->name = $customer->name;
            $data->mobile = $customer->mobile;
            $data->profile_image = $customer->profile_image_base64;

            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
        } else {
            throw new UserNotFoundException();
        }
    }

    public function getCustomerOtp($msisdn)
    {
        $url = 'http://172.16.254.157:8080/otp/'.$msisdn;

        $ch = curl_init();
        $headers = [''];
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper('get'));

        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($ch);


        return response()->json($result);
    }

    public function setProfilePhoto(Request $request)
    {
        $customer = Auth::user();
        if (!is_null($customer)) {
            if ($request->hasFile('profile_photo')) {
                $uploadResult = $this->uploadImage($request);
                if ($uploadResult['status'] == 'success') {
                    //$data['profile_image'] = $uploadResult['path'];
                    $data['profile_image_base64'] = $uploadResult['path'];
                    if ($customer->profile_image_base64) {
                        $this->removeProfileImage($customer->profile_image_base64);
                    }
                } else {
                    $uploadError = $uploadResult['message'];
                }
            } else {
                return response()->json([
                    'message' => 'Error: Profile photo required',
                ], 401);
            }

            $customer->update($data);

            return response()->json([
                'status' => 'SUCCESS',
                'status_code' => 200,
                'data' => [
                    'image_path' => $customer->profile_image_base64],
                    'message' => 'Profile picture updated successfully!',
            ], 200);
        } else {
            throw new UserNotFoundException();
        }
    }

    public function removeProfilePhoto()
    {
        $customer = Auth::user();

        $this->removeProfileImage($customer->profile_image_base64);
        return response()->json([
            'status' => 'SUCCESS',
            'status_code' => 200,
            'data' => ['image_path' => $customer->profile_image_base64],
            'message' => 'Profile photo removed successfully!',
        ], 200);
    }

    /*    private function uploadImage($request)
        {
            try {
                $file = $request->file('profile_photo');
                $ext = $file->getClientOriginalExtension();
                $photoExt = array('jpg', 'JPG', 'JPEG', 'jpeg', 'png', 'PNG', 'gif', 'bmp');
                if (!in_array($ext, $photoExt)) {
                    return ['status' => 'error', 'message' => 'Invalid image extension'];
                }
                $path = $this->upload($request->file('profile_photo'), 'profile_images');
                return ['status' => 'success', 'path' => $path];
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return ['status' => 'error', 'message' => 'Error: cannot save file'];;
            }
        }*/

    private function uploadImage($request)
    {
        try {
            $file = $request->file('profile_photo');
            $ext = $file->getClientOriginalExtension();
            $photoExt = array('jpg', 'JPG', 'JPEG', 'jpeg', 'png', 'PNG', 'gif', 'bmp');
            if (!in_array($ext, $photoExt)) {
                return ['status' => 'error', 'message' => 'Invalid image extension'];
            }
            //$path = $this->upload($request->file('profile_photo'), 'profile_images');
            $path = base64_encode(file_get_contents($file));
            return ['status' => 'success', 'path' => $path];
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ['status' => 'error', 'message' => 'Error: cannot save file'];;
        }
    }

    private function removeProfileImage($filePath)
    {
        try {
            $customer = Auth::user();
            if (!empty($customer)) {
                //$this->deleteFile($filePath);
                # Remove profile image form idp database
               // $data = ['profile_image' => null];
                $data = ['profile_image_base64' => null];
                $customer->update($data);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    public function logout()
    {
        if (Auth::check()) {
            $accessToken = Auth::user()->token();
            DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $accessToken->id)
                ->update([
                    'revoked' => true
                ]);

            $accessToken->revoke();
            return response()->json([
                'status' => 'SUCCESS',
                'status_code' => 200,
                'data' => [],
                'message' => 'You are logged out successfully!',
            ], 200);
        }
    }

}
