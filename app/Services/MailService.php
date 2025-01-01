<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class MailService
{
    /**
     * Send a general email with the provided details.
     *
     * @param array $details
     * @throws \Exception
     * @return void
     */
    public function sendEmail(array $details): bool
    {
        try {
            $fromEmail = env('MAIL_FROM_ADDRESS', 'info@yourdomain.com');
            $fromName = env('MAIL_FROM_NAME', 'Our Service');

            Mail::send('layouts.email', ['details' => $details], function ($message) use ($details, $fromEmail, $fromName) {
                $message->from($fromEmail, $fromName);
                $message->to($details['email']);
                $message->subject($details['subject']);
            });

            // If no exceptions occurred, email was sent successfully
            return true;
        } catch (\Throwable $th) {
            // Log the error if needed
            Log::error("Failed to send email: " . $th->getMessage());
            // Return false to indicate failure
            return false;
        }
    }

    /**
     * Send account registration email to the user.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function sendAccRegEmail(User $user, string $password = ""): void
    {
        $activationUrl = route('verify.activate', ['email' => $user->email]);

        $details = [
            'title' => 'Account Registration',
            'subject' => 'Welcome to Our Service!',
            'body' => 'Your account has been successfully registered. Here are your details:',
            'more_info' => [
                'Default Password' => $password,
                'Activation Link' => '<a href="' . $activationUrl . '">Verify Email</a>'
            ],
            'footer' => 'Please keep this information safe.',
            'email' => $user->email
        ];

        $this->sendEmail($details);
    }

    /**
     * Send email verification link to the user.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function sendVerificationEmail(User $user): void
    {
        $activationUrl = route('verify.activate', ['email' => $user->email]);

        $details = [
            'title' => 'Email Verification',
            'subject' => 'Verify Your Email',
            'body' => 'Please click the link below to verify your email address:',
            'more_info' => ['Verification Link' => '<a href="' . $activationUrl . '">Verify Email</a>'],
            'footer' => 'If you did not create an account, no further action is required.',
            'email' => $user->email
        ];

        $this->sendEmail($details);
    }

    /**
     * Send login notification email to the user.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function sendLoginEmail(User $user): void
    {
        $details = [
            'title' => 'Login Notification',
            'subject' => 'Login Notification',
            'body' => 'A login to your account was detected. Here are your details:',
            'more_info' => [],
            'footer' => 'If this was not you, please contact us immediately.',
            'email' => $user->email
        ];

        $this->sendEmail($details);
    }

    /**
     * Send account update email to the user.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function sendAccountUpdateEmail(User $user): void
    {
        $details = [
            'title' => 'Account Update',
            'subject' => 'Your Account Update',
            'body' => 'Your account details have been updated. Here are your details:',
            'more_info' => [],
            'footer' => 'If you did not request this change, please contact us immediately.',
            'email' => $user->email,
        ];

        $this->sendEmail($details);
    }

    /**
     * Send password reset notification email to the user.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function sendPasswordResetNotification(User $user): void
    {
        $details = [
            'title' => 'Password Reset Notification',
            'subject' => 'Password Reset',
            'body' => 'Your password has been successfully reset. If you did not request this change, please contact us immediately.',
            'more_info' => [],
            'footer' => 'Thank you for using our services.',
            'email' => $user->email,
        ];

        $this->sendEmail($details);
    }

    /**
     * Send password reset link email to the user.
     *
     * @param \App\Models\User $user
     * @param string $token
     * @return void
     */
    public function sendPasswordResetLink(User $user, string $token): void
    {
        $details = [
            'title' => 'Password Reset Link',
            'subject' => 'Password Reset Request',
            'body' => 'You have requested a password reset. Please use the link below to reset your password:',
            'more_info' => [
                'Reset Password' => '<a href="' . url('password/reset/' . $token . '?email=' . urlencode($user->email)) . '">Reset Password</a>'
            ],
            'footer' => 'If you did not request this change, please contact us immediately.',
            'email' => $user->email,
        ];

        $this->sendEmail($details);
    }

    /**
     * Send OTP email to the user for login authentication.
     *
     * @param \App\Models\User $user
     * @param string $otp
     * @return void
     */
    public function sendOtpEmail(User $user, string $otp): void
    {
        $details = [
            'title' => 'Login OTP',
            'subject' => 'Login OTP',
            'body' => 'Your login OTP is ' . $otp,
            'more_info' => [],
            'footer' => 'If you did not request this, please contact us immediately.',
            'email' => $user->email,
        ];

        $this->sendEmail($details);
    }

    /**
     * Send account deletion notification email to the user.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function sendAccountDeletionEmail(User $user): void
    {
        $details = [
            'title' => 'Account Deleted',
            'subject' => 'Your Account has been Deleted',
            'body' => 'Your account has been deleted. Thank you for being part of us.',
            'more_info' => [],
            'footer' => 'Thank you for being with us.',
            'email' => $user->email,
        ];

        $this->sendEmail($details);
    }
}
