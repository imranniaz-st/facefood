<?php

namespace App\Http\Responses;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class SafeApiErrorResponse
{
    public static function from(Throwable $e, Request $request): ?JsonResponse
    {
        if (! $request->is('api/*') && ! $request->expectsJson()) {
            return null;
        }

        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => self::sanitize($e->getMessage()),
                'errors' => $e->errors(),
            ], $e->status);
        }

        if ($e instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
        $message = self::publicMessage($e, $status);

        return response()->json([
            'message' => $message,
        ], $status);
    }

    public static function sanitize(string $message): string
    {
        $message = strip_tags($message);
        $message = preg_replace('#/[\\w./\\\\-]+\\.(php|env|json|xml|yml|yaml)#i', '[hidden]', $message) ?? $message;
        $message = preg_replace('#\\b[A-Z]:\\\\[\\w\\\\.-]+#i', '[hidden]', $message) ?? $message;
        $message = preg_replace('#/home/[\\w./-]+#i', '[hidden]', $message) ?? $message;

        if (preg_match('/(Symfony|Illuminate|Stack trace|vendor\\\\|\.php on line|SQLSTATE)/i', $message)) {
            return 'Something went wrong. Please try again.';
        }

        return trim($message) !== '' ? trim($message) : 'Something went wrong. Please try again.';
    }

    private static function publicMessage(Throwable $e, int $status): string
    {
        if ($status === 404) {
            return 'The requested resource was not found.';
        }

        if ($status === 403) {
            return 'You do not have permission to perform this action.';
        }

        if ($status === 401) {
            return 'Unauthenticated.';
        }

        if ($status === 503) {
            return self::sanitize($e->getMessage());
        }

        if ((bool) config('app.debug', false)) {
            return self::sanitize($e->getMessage());
        }

        if ($status >= 500) {
            return 'Server error. Please try again later.';
        }

        return self::sanitize($e->getMessage());
    }
}
