<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Exceptions\BookingAlreadyCancelledException;
use App\Exceptions\BookingConflictException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (
            BookingConflictException|BookingAlreadyCancelledException $exception,
            Request $request,
        ) {
            $message = $exception->getMessage();
            $field = $exception instanceof BookingConflictException ? 'start_time' : 'booking';
            $errors = [$field => [$message]];

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'errors' => $errors,
                ], $exception instanceof BookingConflictException ? 409 : 422);
            }

            return redirect()->back()->withInput()->withErrors($errors);
        });
    })->create();
