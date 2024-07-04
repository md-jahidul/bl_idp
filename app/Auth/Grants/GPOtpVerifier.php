<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 10/7/19
 * Time: 4:26 PM
 */

namespace App\Auth\Grants;

use App\Auth\Grants\OTPVerifierInterface;

class GPOtpVerifier implements OTPVerifierInterface
{

    public function verify($otp)
    {
        return ($otp == "12345");
    }
}
