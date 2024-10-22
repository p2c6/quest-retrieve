<?php
namespace App\Services\Contracts\Authentication;

use Illuminate\Http\Request;

/**
 * Interface SendResetPasswordLinkInterface
 * 
 * Provides methods for handling email reset password link processes.
 */
interface SendResetPasswordLinkInterface {
    /**
     * Send an email reset password link.
     * 
     * @param  \Illuminate\Http\Request  $request  The HTTP request containing the necessary data.
     * @return mixed  The result of sending the reset password link, which may vary based on implementation.
     */
    public function sendResetPasswordLink(Request $request) : mixed;
}