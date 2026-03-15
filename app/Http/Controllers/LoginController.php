<?php

namespace App\Http\Controllers;

use App\Models\Dac7Information;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Rules\PasswordChanged;
use App\Services\MailService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LoginController extends Controller
{
    private Request $request;

    private UserRepository $userRepository;

    public function __construct(
        Request $request,
        UserRepository $userRepository
    ) {
        $this->request = $request;
        $this->userRepository = $userRepository;
    }

    public function login(): View
    {
        // Als dit een directe toegang tot de inlogpagina is (niet via DAC7 link),
        // verwijder alle DAC7 sessiegegevens
        if (! request()->has('from_dac7')) {
            session()->forget(['dac7_redirect_url', 'dac7_user_uuid', 'dac7_info_exists', 'dac7_timestamp']);
        }

        // Return Login
        return view('login');
    }

    public function loginAsUser(): View|RedirectResponse
    {
        $validated = $this->request->validate([
            'email' => ['required', 'email:rfc,dns',  'regex:/^[^@]+(\.[^@]+)*@[^@]+\.[^@]+$/'],
            'password' => ['required'],
            'remember' => ['required', Rule::in('off', 'on')],
        ]);

        $user = $this->userRepository->findUserByEmail($validated['email']);

        if (
            is_null($user) ||
            ! Hash::check($validated['password'], $user->getAuthPassword())
        ) {
            return back()->withErrors(['msg' => 'De combinatie van e-mail en wachtwoord wordt niet herkend.']);
        }

        $remember = $validated['remember'] === 'on';

        $hasDac7Data = session()->has('dac7_redirect_url') && session()->has('dac7_user_uuid');
        $dac7Timestamp = session('dac7_timestamp');

        if ($hasDac7Data && $dac7Timestamp && (now()->timestamp - $dac7Timestamp > 30 * 60)) {
            session()->forget(['dac7_redirect_url', 'dac7_user_uuid', 'dac7_info_exists', 'dac7_timestamp']);
            $hasDac7Data = false;
        }

        if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']], $remember)) {
            if ($remember) {
                $user->update(['last_login_date' => now()->toDateTimeString()]);
            }

            $this->userRepository->updateLogin($user);

            if ($hasDac7Data) {
                $dac7RedirectUrl = session('dac7_redirect_url');
                $dac7UserUuid = session('dac7_user_uuid');
                $dac7InfoExists = session('dac7_info_exists', false);

                if ($dac7UserUuid !== Auth::user()->uuid) {
                    return redirect()->route('dashboard.adverts.active.home')
                        ->with('error', 'De gebruikte DAC7 link is niet bestemd voor dit account.');
                }

                session()->forget(['dac7_redirect_url', 'dac7_user_uuid', 'dac7_info_exists', 'dac7_timestamp']);

                if ($dac7InfoExists) {
                    return redirect()->route('dac7.success');
                }

                $dac7Info = Dac7Information::where('user_id', Auth::user()->uuid)->first();
                if ($dac7Info && $dac7Info->information_provided) {
                    return redirect()->route('dac7.success');
                }

                return redirect()->to($dac7RedirectUrl);
            }

            if ($this->request->user()->hasRole('cook')) {
                return redirect()->route('dashboard.adverts.active.home');
            }

            if ($this->request->user()->hasRole('admin')) {
                return redirect()->route('dashboard.admin.accounts');
            }

            return redirect()->route('home');
        }

        return back()->withErrors(['msg' => 'Er is een fout opgetreden bij het inloggen.']);
    }

    public function logout(): RedirectResponse
    {
        $user = $this->request->user();

        // Preserve DAC7 session data
        $dac7RedirectUrl = session('dac7_redirect_url');
        $dac7UserUuid = session('dac7_user_uuid');
        $dac7InfoExists = session('dac7_info_exists');

        if ($user) {
            $user->update(['last_login_date' => null]);
        }

        Auth::logout();

        // Restore DAC7 session data after logout
        if ($dac7RedirectUrl) {
            session(['dac7_redirect_url' => $dac7RedirectUrl]);
        }
        if ($dac7UserUuid) {
            session(['dac7_user_uuid' => $dac7UserUuid]);
        }
        if ($dac7InfoExists !== null) {
            session(['dac7_info_exists' => $dac7InfoExists]);
        }

        return redirect()->route('home');
    }

    // Rest of the controller methods remain unchanged
    public function forgotPassword(): View
    {
        return view('forgot');
    }

    public function sendForgotPasswordEmail(): RedirectResponse
    {
        Password::sendResetLink(
            $this->request->only('email'),
            function ($user, $token) {
                (new MailService)->sendPasswordResetNotification($user, $token);
            }
        );

        return redirect()->route('login.forgot.notification', ['passwordResetMail' => true]);
    }

    public function passwordReset(): View
    {
        $validated = $this->request->validate([
            'token' => 'required',
            'email' => ['required', 'email:rfc,dns',  'regex:/^[^@]+(\.[^@]+)*@[^@]+\.[^@]+$/'],
        ]);

        return view('password')->with([
            'token' => $validated['token'],
            'email' => $validated['email'],
        ]);
    }

    public function submitPasswordReset(): RedirectResponse
    {
        $this->request->validate([
            'password' => ['required', 'confirmed', new PasswordChanged($this->request->input('email'))],
            'email' => ['required', 'email:rfc,dns',  'regex:/^[^@]+(\.[^@]+)*@[^@]+\.[^@]+$/'],
            'token' => ['required', 'string'],
        ]);

        $status = Password::reset(
            $this->request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                /** @var User $user */
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                (new MailService)->sendPasswordChangedNotification($user);

                Auth::attempt(['email' => $user->getEmail(), 'password' => $password], true);
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login.forgot.notification', [
                'passwordResetMail' => false,
                'passwordReset' => true,
            ])
            : back()->withErrors(['email' => [trans($status)]]);
    }

    public function notificationPoint(): View
    {
        return view('password-notification', $this->request->all());
    }
}
