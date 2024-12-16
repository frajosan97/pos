<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Services\MailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

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
     * Handle a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if the user exists before proceeding
        if ($user) {
            // Send password reset notification email
            $this->mailService->sendPasswordResetNotification($user);
        }

        // Redirect the user to the login page with a success message
        return redirect()->route('login')->with('success', 'Your password has been reset successfully. Please log in with your new password.');
    }

    /**
     * Reset the given user's password.
     *
     * @param  \App\Models\User  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        // Hash the new password and save it
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Ensure the user is logged out after password reset
        Auth::logout();
    }
}
