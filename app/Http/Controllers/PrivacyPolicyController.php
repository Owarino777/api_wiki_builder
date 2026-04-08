<?php

namespace App\Http\Controllers;

use App\Services\UserPrivacyService;
use Illuminate\Contracts\View\View;

class PrivacyPolicyController extends Controller
{
    public function __invoke(): View
    {
        return view('legal.privacy', [
            'policyVersion' => UserPrivacyService::POLICY_VERSION,
            'contactEmail' => config('mail.from.address', 'privacy@example.invalid'),
        ]);
    }
}
