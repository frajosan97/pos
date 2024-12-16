<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Services\MailService;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Summary of mailService
     * @var 
     */
    protected $mailService;

    /**
     * Summary of smsService
     * @var 
     */
    protected $smsService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\MailService $mailService
     * @return void
     */
    public function __construct(MailService $mailService, SmsService $smsService)
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
        $this->mailService = $mailService;
        $this->smsService = $smsService;
    }

    /**
     * Summary of authenticated
     * @param \Illuminate\Http\Request $request
     * @param mixed $user
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        try {
            if (!($user->email_verified_at)) {
                Auth::logout(); // Log the user out
                return redirect()->back()->with('error', 'Your account is pending verification,<br><a href="/verify/resend-activation-link">Resend verification link</a>');
            }

            // Prepare verification methods
            $verify_methods = [
                'verifyPhone' => obfuscatePhone(formatPhoneNumber($user->phone)),
                'verifyEmail' => obfuscateEmail($user->email)
            ];

            // Store verification methods in session
            Session::put('verify_methods', $verify_methods);
            Session::put('otp_user_id', $user->id);

            Auth::logout(); // Log the user out

            // Redirect to OTP verification page
            return redirect()->route('otp.send-otp');
        } catch (\Throwable $th) {
            Auth::logout();
            Log::error('Error: ' . $th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
            return redirect()->back()->with('error', 'Error ' . $th->getMessage());
        }
    }

    public function sendOtp(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'method' => 'required|in:email,phone',
            ]);

            $userId = Session::get('otp_user_id'); // Get the user ID from the session
            $user = User::findOrFail($userId); // Retrieve the user

            // Generate OTP
            $otp = rand(100000, 999999); // Generate a 6-digit OTP
            $user->otp = $otp; // Assuming you have an 'otp' column in your User model
            $user->save();

            // Retrieve verification methods from session
            $verify_methods = Session::get('verify_methods');

            // Now you can access verifyPhone and verifyEmail
            $verifyPhone = $verify_methods['verifyPhone'] ?? null;
            $verifyEmail = $verify_methods['verifyEmail'] ?? null;

            // Create the SMS
            $message = "Dear " . strtoupper($user->sur_name) . ",\nYour login OTP is " . $otp . ".\nPlease don't share it with anyone.";

            // Send OTP based on the selected method
            if ($request->method === 'email') {
                $this->mailService->sendOtpEmail($user, $otp);
                // Optionally: add a success message
                return redirect()->route('otp.verify-otp')->with('status', 'OTP sent to your email ' . $verifyEmail);
            } elseif ($request->method === 'phone') {
                $this->smsService->sendSms($user->phone, $message);
                // Optionally: add a success message
                return redirect()->route('otp.verify-otp')->with('status', 'OTP sent to your phone number ' . $verifyPhone);
            }
        } catch (\Throwable $th) {
            Log::error('Error: ' . $th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
            return redirect()->back()->with('error', 'Error ' . $th->getMessage());
        }
    }

    /**
     * Verify the OTP entered by the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'otp' => 'required|digits:6',
            ]);

            $userId = Session::get('otp_user_id');
            $user = User::find($userId);

            if ($user && $user->otp === $request->input('otp')) {
                // OTP is correct, log the user in
                Auth::login($user);

                // Clear the OTP and session data
                $user->otp = null;
                $user->save();
                Session::forget('otp_user_id');

                // Send login confirmation email
                $this->mailService->sendLoginEmail($user);

                return redirect()->intended($this->redirectPath())->with('status', 'Login successful.');
            }

            return redirect()->back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
        } catch (\Throwable $th) {
            Log::error('Error: ' . $th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
            return redirect()->back()->with('error', 'Error ' . $th->getMessage());
        }
    }

    /**
     * Summary of showOtpSendForm
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showOtpSendForm(Request $request)
    {
        try {
            // Retrieve verification methods from session
            $verify_methods = Session::get('verify_methods', []);

            // Define the verification options
            $options = [
                [
                    'method' => 'email',
                    'label' => 'Send code to my email',
                    'icon' => 'bi-person-circle',
                    'value' => $verify_methods['verifyEmail'] ?? null,
                ],
                [
                    'method' => 'phone',
                    'label' => 'Send code to my phone',
                    'icon' => 'bi-chat-dots',
                    'value' => $verify_methods['verifyPhone'] ?? null,
                ],
            ];

            // Proceed with your logic (e.g., display the OTP form)
            return view('auth.otp.send-otp', compact('options'));
        } catch (\Throwable $th) {
            Log::error('Error: ' . $th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
            return redirect()->back()->with('error', 'Error ' . $th->getMessage());
        }
    }

    /**
     * Show the OTP verification form.
     *
     * @return \Illuminate\View\View
     */
    public function showOtpVerificationForm()
    {
        return view('auth.otp.verify-otp');
    }

    public function sendVerificationLink(Request $request)
    {
        try {
            // Validate the email input
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            // Retrieve the user using the email address
            $user = User::where('email', $request->email)->first();

            // Check if the user exists and if their email is not already verified
            if ($user && !$user->email_verified_at) {
                // Call the mailService to send the verification email
                $this->mailService->sendVerificationEmail($user);

                // Return a success message to the user
                return back()->with('status', 'A verification link has been sent to your email address. Please check your inbox.');
            }

            // If the user does not exist or is already verified
            return back()->withErrors(['email' => 'No unverified account found with that email address.']);
        } catch (\Throwable $th) {
            Log::error('Error sending verification link: ' . $th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
            return back()->with('error', 'Error sending verification link. Please try again.');
        }
    }

    /**
     * Summary of activateAccount
     * @param mixed $email
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    public function activateAccount($email)
    {
        try {
            // Retrieve the user by email
            $user = User::where('email', $email)->first();

            // If the user does not exist, redirect with an error
            if (!$user) {
                return redirect()->intended($this->redirectPath())->with('status', 'Invalid activation link.');
            }

            // If the user is already verified, inform them
            if ($user->email_verified_at) {
                return redirect()->intended($this->redirectPath())->with('success', 'Your account has been activated. You can now log in.');
            }

            // Mark the account as activated by setting the `email_verified_at` field
            $user->email_verified_at = now();
            $user->save();

            // Redirect with a success message
            return redirect()->intended($this->redirectPath())->with('success', 'Your account has been activated. You can now log in.');
        } catch (\Throwable $th) {
            // Log the error in case of any failure
            Log::error('Error: ' . $th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);

            // Return an error message to the user
            return redirect()->intended($this->redirectPath())->with('status', 'Failed to activate your account. Please try again.');
        }
    }
}
