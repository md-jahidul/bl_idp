<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 10/7/19
 * Time: 4:26 PM
 */

namespace App\Auth\Grants;

use App\Auth\Grants\OTPVerifierInterface;
use App\Services\ApiHubCommunicator;

class BLOtpVerifier implements OTPVerifierInterface
{
    use ApiHubCommunicator;

    protected const VERIFY_OTP_ENDPOINT = "/otp/one-time-passwords/validate";

    public function verify($otp, $mobile)
    {
        $param = [
            'msisdn' => $mobile,
            'token' => $otp
        ];

        $response = $this->post(self::VERIFY_OTP_ENDPOINT, $param);

        if (!$response['status_code'])
            throw new \Exception('Otp service unavailable');

        return $response['status_code'] == 200;
    }
}
