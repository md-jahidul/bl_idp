<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 11/12/19
 * Time: 11:30 AM
 */

namespace App\Exceptions;


class InvalidPasswordException extends \Exception
{
    /**
     * Render an exception into an HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        return response()->json(([
            'error'  => 'Invalid password',
            'error_description' => 'Invalid password',
            'message' => 'Invalid Password',
        ]), 401);
    }
}
