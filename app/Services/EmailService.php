<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;

/**
 * Class EmailService
 *
 * @package App\Services
 */
class EmailService
{
    /**
     * Send code on email for forgot password
     *
     * @param User $user
     */
    public function sendForgotPassword(User $user)
    {
        Mail::send('emails.forgot', ['user' => $user], function ($message) use ($user) {
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $message->subject(env('MAIL_APP_NAME') . ' - Forgot password code');

            $message->to($user->email);
        });
    }

    /**
     * Send code on email for change password
     * @param User $user
     * @return boolean
     */

    public function sendChangePassword(User $user)
    {
        Mail::send('emails.change', ['user' => $user], function ($message) use ($user) {
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $message->subject(env('MAIL_APP_NAME') . ' - Forgot password code');

            $message->to($user->email);
        });
    }

    /**
     * Send  email for verifying accunt
     * @param User $user
     * @param string $url
     * @return boolean
     */
    public function sendVerifyAccount(User $user)
    {
        Mail::send('emails.verify', ['user' => $user], function ($message) use ($user) {
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $message->subject(env('MAIL_APP_NAME') . ' - Verify your account');

            $message->to($user->email);
        });

        return (count(Mail::failures()) == 0);
    }


}
