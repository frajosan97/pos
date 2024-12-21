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

    /**
     * Send an OTP to the user via email or phone.
     */
    public function sendOtp(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'method' => 'required|in:email,phone',
            ]);

            $userId = Session::get('otp_user_id'); // Get the user ID from the session
            $user = User::findOrFail($userId);    // Retrieve the user

            // Generate OTP
            $otp = rand(100000, 999999); // Generate a 6-digit OTP
            $user->otp = $otp;          // Assuming 'otp' is a column in the User model
            $user->save();

            // Retrieve verification methods from the session
            $verify_methods = Session::get('verify_methods');
            $verifyPhone = $verify_methods['verifyPhone'] ?? null;
            $verifyEmail = $verify_methods['verifyEmail'] ?? null;

            // Create the SMS message
            $message = "Dear " . strtoupper($user->name) . ",\nYour login OTP is " . $otp . ".\nPlease don't share it with anyone.";

            // Send OTP based on the selected method
            if ($request->method === 'email') {
                $this->mailService->sendOtpEmail($user, $otp);

                return redirect()->route('otp.verify-otp')
                    ->with('status', 'OTP sent to your email ' . $verifyEmail);
            } elseif ($request->method === 'phone') {
                $response = $this->smsService->sendSms($user->phone, $message);

                if ($response && $response->getStatusCode() === 200) {
                    return redirect()->route('otp.verify-otp')
                        ->with('status', 'OTP sent to your phone number ' . $verifyPhone);
                } else {
                    Log::error('SMS sending failed', ['response' => $response]);
                    return redirect()->back()
                        ->with('error', 'Error encountered while sending SMS. Please try again or use another verification method.');
                }
            }
        } catch (\Throwable $th) {
            Log::error('Error: ' . $th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);

            return redirect()->back()->with('error', 'Error: ' . $th->getMessage());
        }
    }

    /**
     * Verify the OTP entered by the user.
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

                return redirect()->intended($this->redirectPath())
                    ->with('status', 'Login successful.');
            }

            return redirect()->back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
        } catch (\Throwable $th) {
            Log::error('Error: ' . $th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);

            return redirect()->back()->with('error', 'Error: ' . $th->getMessage());
        }
    }

    /**
     * Display the form to send OTP via email or phone.
     */
    public function showOtpSendForm(Request $request)
    {
        try {
            $verify_methods = Session::get('verify_methods', []);
            $options = [
                [
                    'method' => 'email',
                    'label' => 'Send code to my email',
                    'icon' => 'fas fa-envelope',
                    'value' => $verify_methods['verifyEmail'] ?? null,
                ],
                [
                    'method' => 'phone',
                    'label' => 'Send code to my phone',
                    'icon' => 'fas fa-sms',
                    'value' => $verify_methods['verifyPhone'] ?? null,
                ],
            ];

            return view('auth.otp.send-otp', compact('options'));
        } catch (\Throwable $th) {
            Log::error('Error: ' . $th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);

            return redirect()->back()->with('error', 'Error: ' . $th->getMessage());
        }
    }

    /**
     * Show the OTP verification form.
     */
    public function showOtpVerificationForm()
    {
        return view('auth.otp.verify-otp');
    }

    /**
     * Send an email verification link.
     */
    public function sendVerificationLink(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            $user = User::where('email', $request->email)->first();

            if ($user && !$user->email_verified_at) {
                $this->mailService->sendVerificationEmail($user);

                return back()->with('status', 'A verification link has been sent to your email address. Please check your inbox.');
            }

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
     * Activate a user account via email verification.
     */
    public function activateAccount($email)
    {
        try {
            $user = User::where('email', $email)->first();

            if (!$user) {
                return redirect()->intended($this->redirectPath())
                    ->with('status', 'Invalid activation link.');
            }

            if ($user->email_verified_at) {
                return redirect()->intended($this->redirectPath())
                    ->with('success', 'Your account has already been activated. You can now log in.');
            }

            $user->email_verified_at = now();
            $user->save();

            return redirect()->intended($this->redirectPath())
                ->with('success', 'Your account has been activated. You can now log in.');
        } catch (\Throwable $th) {
            Log::error('Error: ' . $th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);

            return redirect()->intended($this->redirectPath())
                ->with('status', 'Failed to activate your account. Please try again.');
        }
    }
}
