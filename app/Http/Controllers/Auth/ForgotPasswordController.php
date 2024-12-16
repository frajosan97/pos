<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\Services\MailService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * The mail service instance.
     *
     * @var \App\Services\MailService
     */
    protected $mailService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\MailService $mailService
     * @return void
     */
    public function __construct(MailService $mailService)
    {
        $this->middleware('guest');
        $this->mailService = $mailService;
    }

    /**
     * Summary of sendResetLinkEmail
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $user = $this->getUserByEmail($request->input('email'));

        if (!$user) {
            return $this->sendResetLinkFailedResponse($request, Password::INVALID_USER);
        }

        // Generate password reset token
        $token = Password::getRepository()->create($user);

        try {
            // Send custom email using MailService
            $emailSent = $this->mailService->sendPasswordResetLink($user, $token);

            return $this->sendResetLinkResponse($request, Password::RESET_LINK_SENT);
        } catch (\Throwable $th) {
            // Log the error and return failure response
            Log::error('Password reset email failed: ' . $th->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while sending the reset link. Please try again.');
        }
    }

    /**
     * Get the user by email.
     *
     * @param string $email
     * @return mixed
     */
    protected function getUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }
}
