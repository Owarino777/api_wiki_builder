<?php

use App\Models\ApiKey;

it('hashes a key deterministically with sha256', function (): void {
    $plain = 'test-key-abc';
    $hash = ApiKey::hashKey($plain);

    expect($hash)->toBe(hash('sha256', $plain));
    expect(ApiKey::hashKey($plain))->toBe($hash);
});

it('produces different hashes for different keys', function (): void {
    expect(ApiKey::hashKey('key-a'))->not->toBe(ApiKey::hashKey('key-b'));
});
