<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 10/7/19
 * Time: 4:27 PM
 */

namespace App\Auth\Grants;

use App\Auth\Grants\BLOtpVerifier;


abstract class OtpVerifierFactory
{
    public static function getOtpVerifier($source)
    {
        switch ($source) {
            case 'BL_INTERNAL' :
                return new BLOtpVerifier();
            case 'GP' :
                return new GPOtpVerifier();
        }
    }
}
