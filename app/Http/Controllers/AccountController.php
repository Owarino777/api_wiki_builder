<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\AnonymizeAccountRequest;
use App\Http\Requests\Account\DeleteAccountRequest;
use App\Services\UserPrivacyService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountController extends Controller
{
    public function show(Request $request): View
    {
        return view('account.show', [
            'user' => $request->user(),
            'policyVersion' => UserPrivacyService::POLICY_VERSION,
        ]);
    }

    public function export(Request $request, UserPrivacyService $privacyService): StreamedResponse
    {
        $user = $request->user();
        $payload = $privacyService->export($user);
        $filename = 'wiki-builder-export-'.$user->id.'-'.now()->format('Ymd-His').'.json';

        return response()->streamDownload(function () use ($payload): void {
            echo json_encode(
                $payload,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
        }, $filename, [
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);
    }

    public function anonymize(AnonymizeAccountRequest $request, UserPrivacyService $privacyService): RedirectResponse
    {
        $user = $request->user();

        $privacyService->anonymize($user);

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('status', 'Ton compte a ete anonymise et la session a ete fermee.');
    }

    public function destroy(DeleteAccountRequest $request, UserPrivacyService $privacyService): RedirectResponse
    {
        $user = $request->user();

        $privacyService->delete($user);

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('status', 'Ton compte a ete supprime et la session a ete fermee.');
    }
}
