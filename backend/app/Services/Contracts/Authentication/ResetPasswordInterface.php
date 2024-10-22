<?php
namespace App\Services\Contracts\Authentication;

use Illuminate\Http\Request;

/**
 * Interface ResetPasswordInterface
 * 
 * Provides methods for handling reset password processes.
 */
interface ResetPasswordInterface {
    /**
     * Reset password.
     * 
     * @param  \Illuminate\Http\Request  $request  The HTTP request containing the verification data.
     * @return mixed  The result of the reset password process, which may vary based on implementation.
     */
    public function resetPassword(Request $request) : mixed;
}