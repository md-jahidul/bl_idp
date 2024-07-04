<?php

namespace App\Exceptions;

use Exception;

class UserNotFoundException extends Exception{

   /**
     * Render an exception into an HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        return response()->json(([
            'error'  => 'not_found',
            'error_description' => 'Phone number does not match to any users',
            'message' => 'User not found',
        ]), 404);
    }
}