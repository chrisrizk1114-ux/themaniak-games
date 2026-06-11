<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO
            | Request::HEADER_X_FORWARDED_AWS_ELB);
        $middleware->web(prepend: [
            \App\Http\Middleware\ForceCanonicalDomain::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\PreventAuthPageCache::class,
        ]);
        $middleware->alias([
            'owner' => \App\Http\Middleware\EnsureOwner::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\UpdateLastSeen::class,
            \App\Http\Middleware\TrackPageView::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (DecryptException $e, Request $request) {
            if ($request->hasSession()) {
                $request->session()->flush();
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Session expired. Refresh the page.'], 401);
            }

            return redirect('/reset-session');
        });

        $exceptions->render(function (QueryException $e, Request $request) {
            if ($request->is('up', 'health')) {
                return null;
            }

            if ($request->isMethod('POST') && $request->is('login', 'register')) {
                return null;
            }

            if ($request->hasSession()) {
                try {
                    auth()->logout();
                    $request->session()->flush();
                } catch (\Throwable) {
                    // ignore
                }
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Database temporarily unavailable. Try again.'], 503);
            }

            if ($request->isMethod('GET') && ! $request->is('login', 'register')) {
                if ($request->is('reset-session')) {
                    return redirect('/');
                }

                return redirect('/reset-session');
            }

            return null;
        });

        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Page expired. Please refresh and try again.'], 419);
            }

            $isAuthForm = $request->isMethod('POST')
                && ($request->is('login', 'register') || str_contains($request->path(), 'login') || str_contains($request->path(), 'register'));

            if ($isAuthForm) {
                if ($request->hasSession()) {
                    try {
                        $request->session()->regenerateToken();
                    } catch (\Throwable) {
                        // Session may be corrupt — redirect to a fresh login page.
                    }
                }

                $route = str_contains($request->path(), 'register') ? 'register' : 'login';

                return redirect()
                    ->route($route)
                    ->withInput($request->except('_token', 'password', 'password_confirmation'))
                    ->withErrors(['email' => 'Session expired — please refresh the page and try again.']);
            }

            return redirect()
                ->back()
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->withErrors(['session' => 'Your session expired. Please refresh the page and try again.']);
        });
    })->create();
