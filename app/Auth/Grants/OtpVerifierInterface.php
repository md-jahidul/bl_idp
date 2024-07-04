<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 10/7/19
 * Time: 4:26 PM
 */

namespace App\Auth\Grants;


interface OTPVerifierInterface
{
    public function verify($otp, $mobile);
}
