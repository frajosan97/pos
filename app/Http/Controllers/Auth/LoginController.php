<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Services\MailService;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * MailService instance.
     *
     * @var MailService
     */
    protected $mailService;

    /**
     * SmsService instance.
     *
     * @var SmsService
     */
    protected $smsService;

    /**
     * Create a new controller instance.
     *
     * @param MailService $mailService
     * @param SmsService $smsService
     */
    public function __construct(MailService $mailService, SmsService $smsService)
    {
        $this->middleware('guest')->except('logout');
        $this->mailService = $mailService;
        $this->smsService = $smsService;
    }

    /**
     * Handle user authentication.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        try {
            if (!$user->email_verified_at) {
                Auth::logout();
                return redirect()->back()->with('error', 'Your account is pending verification. <br><a href="/verify/resend-activation-link">Resend verification link</a>');
            }

            $verifyMethods = [
                [
                    'method' => 'email',
                    'label' => 'Send code to my email',
                    'icon' => 'fas fa-envelope',
                    'value' => obfuscateEmail($user->email),
                ],
                [
                    'method' => 'phone',
                    'label' => 'Send code to my phone',
                    'icon' => 'fas fa-sms',
                    'value' => obfuscatePhone($user->phone),
                ],
            ];

            Session::put('verify_methods', $verifyMethods);
            Session::put('otp_user_id', $user->id);

            Auth::logout();

            return redirect()->route('otp.send-otp');

            // return redirect()->route($this->redirectPath())->with('status', 'Login successful.');
        } catch (\Throwable $th) {
            Log::error('Authentication error: ' . $th->getMessage(), ['file' => $th->getFile(), 'line' => $th->getLine()]);
            Auth::logout();
            return redirect()->back()->with('error', 'An error occurred during authentication.');
        }
    }

    /**
     * Send OTP to user.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendOtp(Request $request)
    {
        try {
            $request->validate(['method' => 'required|in:email,phone']);

            $userId = Session::get('otp_user_id');
            $user = User::findOrFail($userId);

            $otp = random_int(100000, 999999);
            $user->otp = $otp;
            $user->save();

            if ($request->method === 'email') {
                $this->mailService->sendOtpEmail($user, $otp);
                return redirect()->route('otp.verify-otp')->with('status', 'OTP sent to your email.');
            }

            $response = $this->smsService->sendSms($user->phone, "Your OTP is $otp.");
            if ($response && $response->getStatusCode() === 200) {
                return redirect()->route('otp.verify-otp')->with('status', 'OTP sent to your phone.');
            }

            Log::error('Failed to send SMS.', ['response' => $response]);
            return redirect()->back()->with('error', 'Error sending SMS. Try again.');
        } catch (\Throwable $th) {
            Log::error('Error sending OTP: ' . $th->getMessage(), ['file' => $th->getFile(), 'line' => $th->getLine()]);
            return redirect()->back()->with('error', 'An error occurred while sending the OTP.');
        }
    }

    /**
     * Verify OTP.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyOtp(Request $request)
    {
        try {
            $request->validate(['otp' => 'required|digits:6']);

            $userId = Session::get('otp_user_id');
            $user = User::find($userId);

            if ($user && $user->otp === $request->otp) {
                Auth::login($user);
                $user->otp = null;
                $user->save();
                Session::forget('otp_user_id');
                $this->mailService->sendLoginEmail($user);

                return redirect()->intended($this->redirectPath())->with('status', 'Login successful.');
            }

            return redirect()->back()->withErrors(['otp' => 'Invalid OTP.']);
        } catch (\Throwable $th) {
            Log::error('OTP verification error: ' . $th->getMessage(), ['file' => $th->getFile(), 'line' => $th->getLine()]);
            return redirect()->back()->with('error', 'An error occurred during OTP verification.');
        }
    }

    /**
     * Resend verification link.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendVerificationLink(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email|exists:users,email']);

            $user = User::where('email', $request->email)->first();

            if ($user && !$user->email_verified_at) {
                $this->mailService->sendVerificationEmail($user);
                return back()->with('status', 'Verification link sent to your email.');
            }

            return back()->withErrors(['email' => 'No unverified account found with that email.']);
        } catch (\Throwable $th) {
            Log::error('Verification link error: ' . $th->getMessage(), ['file' => $th->getFile(), 'line' => $th->getLine()]);
            return back()->with('error', 'Error sending verification link.');
        }
    }

    /**
     * Activate user account and update password.
     *
     * @param Request $request
     * @param string $email
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activateAccount(Request $request, string $email)
    {
        try {
            $request->validate([
                'old_password' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $email)->first();
            if (!$user) {
                return redirect()->route('login')->with('status', 'Invalid activation link.');
            }

            if ($user->email_verified_at) {
                return redirect()->route('login')->with('success', 'Account already activated.');
            }

            if (!Hash::check($request->old_password, $user->password)) {
                return back()->withErrors(['old_password' => 'Incorrect old password.']);
            }

            $user->password = Hash::make($request->input('password'));
            $user->email_verified_at = now();
            $user->save();

            return redirect()->route('login')->with('success', 'Account activated. You can now log in.');
        } catch (\Throwable $th) {
            Log::error('Account activation error: ' . $th->getMessage(), ['file' => $th->getFile(), 'line' => $th->getLine()]);
            return back()->with('error', $th->getMessage());
        }
    }
}
