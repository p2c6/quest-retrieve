<?php
namespace App\Services\Contracts\Authentication;

use Illuminate\Http\Request;

/**
 * Interface EmailVerificationInterface
 * 
 * Provides methods for handling email verification processes.
 */
interface EmailVerificationInterface {
    /**
     * Send an email verification.
     * 
     * @param  \Illuminate\Http\Request  $request  The HTTP request containing the necessary data.
     * @return mixed  The result of sending the email verification, which may vary based on implementation.
     */
    public function sendEmailVerification(Request $request) : mixed;

    /**
     * Verify the email based on the request.
     * 
     * @param  \Illuminate\Http\Request  $request  The HTTP request containing the verification data.
     * @return mixed  The result of the verification process, which may vary based on implementation.
     */
    public function verify(Request $request) : mixed;
}