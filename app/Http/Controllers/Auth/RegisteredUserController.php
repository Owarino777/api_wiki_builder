<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\UserPrivacyService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::query()->create([
            'username' => (string) $request->string('username'),
            'email' => (string) $request->string('email'),
            'password' => (string) $request->string('password'),
            'role' => UserRole::User,
            'display_name' => $request->filled('display_name')
                ? (string) $request->string('display_name')
                : (string) $request->string('username'),
            'privacy_policy_accepted_at' => now(),
            'privacy_policy_version' => UserPrivacyService::POLICY_VERSION,
            'marketing_consent_at' => $request->boolean('marketing_opt_in') ? now() : null,
            'last_login_at' => now(),
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
