<?php

use App\Models\User;
use App\Services\UserPrivacyService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the guest auth and privacy pages', function (): void {
    $this->get('/login')->assertOk();
    $this->get('/register')->assertOk();
    $this->get('/privacy')->assertOk();
});

it('requires privacy acknowledgement during registration', function (): void {
    $response = $this->post('/register', [
        'username' => 'privacyless-user',
        'email' => 'privacyless@example.test',
        'display_name' => 'Privacyless User',
        'password' => 'builder123',
        'password_confirmation' => 'builder123',
    ]);

    $response->assertSessionHasErrors('privacy_policy_acknowledged');
    $this->assertGuest();
});

it('registers a user and stores consent metadata', function (): void {
    $response = $this->post('/register', [
        'username' => 'fresh-user',
        'email' => 'fresh@example.test',
        'display_name' => 'Fresh User',
        'password' => 'builder123',
        'password_confirmation' => 'builder123',
        'privacy_policy_acknowledged' => '1',
        'marketing_opt_in' => '1',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();

    $user = User::query()->where('email', 'fresh@example.test')->firstOrFail();

    expect($user->privacy_policy_version)->toBe(UserPrivacyService::POLICY_VERSION);
    expect($user->privacy_policy_accepted_at)->not->toBeNull();
    expect($user->marketing_consent_at)->not->toBeNull();
    expect($user->last_login_at)->not->toBeNull();
});

it('protects the account page behind web auth', function (): void {
    $this->get('/account')
        ->assertRedirect('/login');
});

it('allows an authenticated user to export their personal data', function (): void {
    $user = User::factory()->create([
        'role' => 'user',
        'password' => 'builder123',
        'privacy_policy_accepted_at' => now(),
        'privacy_policy_version' => UserPrivacyService::POLICY_VERSION,
    ]);

    $response = $this->actingAs($user)->get('/account/export');

    $response->assertOk();
    $response->assertHeader('content-type', 'application/json; charset=UTF-8');

    $content = $response->streamedContent();

    expect($content)->toContain($user->email);
    expect($content)->toContain('"policy_version": "'.UserPrivacyService::POLICY_VERSION.'"');
});

it('requires the current password to delete an account', function (): void {
    $user = User::factory()->create([
        'role' => 'user',
        'password' => 'builder123',
    ]);

    $response = $this->actingAs($user)->delete('/account', [
        'delete_password' => 'wrong-password',
        'confirm_delete' => '1',
    ]);

    $response->assertSessionHasErrors('delete_password');
    expect(User::query()->find($user->id))->not->toBeNull();
});

it('deletes the authenticated user account with password confirmation', function (): void {
    $user = User::factory()->create([
        'role' => 'user',
        'password' => 'builder123',
    ]);

    $response = $this->actingAs($user)->delete('/account', [
        'delete_password' => 'builder123',
        'confirm_delete' => '1',
    ]);

    $response->assertRedirect(route('home'));
    expect(User::query()->find($user->id))->toBeNull();
});
