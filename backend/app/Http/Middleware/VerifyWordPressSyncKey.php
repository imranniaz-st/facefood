<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWordPressSyncKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('services.wordpress.sync_key', '');

        if ($expected === '') {
            return response()->json(['message' => 'WordPress sync is not configured.'], 503);
        }

        $provided = (string) ($request->header('X-Facefood-Sync-Key') ?? $request->query('sync_key', ''));

        if (! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Invalid sync key.'], 401);
        }

        return $next($request);
    }
}
