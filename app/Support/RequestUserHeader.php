<?php

namespace App\Support;

use Illuminate\Http\Request;

class RequestUserHeader
{
    public const CANDIDATES = [
        'X-WIKI-ACTOR-ID',
        'X-BUILDER-USER-ID',
        'X-USER-ID',
    ];

    public static function extractUserId(Request $request): ?int
    {
        foreach (self::CANDIDATES as $header) {
            $value = $request->header($header);

            if (is_string($value) && ctype_digit($value)) {
                return (int) $value;
            }
        }

        return null;
    }
}
