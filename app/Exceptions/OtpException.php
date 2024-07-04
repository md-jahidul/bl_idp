<?php

namespace App\Exceptions;

use League\OAuth2\Server\Exception\OAuthServerException;

class OtpException extends OAuthServerException{

    public static function invalidOtp()
    {
        return new static('Invalid OTP.', 6, 'invalid_otp', 401);
    }

    public static function invalidOtpVerifier()
    {
        return new static('Please provide valid OTP verifier. By default, BL_INTERNAL OTP verifier is used.', 6, 
                'invalid_otp_verifier', 401);
    }
}