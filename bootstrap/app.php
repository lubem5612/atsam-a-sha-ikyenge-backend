<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
    web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AllowIfAdmin::class,
        'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
        'ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
        'auth' => \App\Http\Middleware\CustomAuthenticate::class,
    ])->append([
        \App\Http\Middleware\ForceJsonHeader::class,
        \App\Http\Middleware\Cors::class,
        \Illuminate\Session\Middleware\StartSession::class,
    ]);;
})
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Exception $e, Request $request) {
            if ($e->getMessage() == 'Unauthenticated.') {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => [],
                ], Response::HTTP_UNAUTHORIZED);

            }elseif ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => formattedErrorTrace($e->getTrace()),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    })->create();



function formattedErrorTrace(array $errorTrace)
{
    $errors = [];
    foreach ($errorTrace as $error) {
        if (array_key_exists('line', $error ) && $error['line'] && array_key_exists('file', $error) && $error['file']) {
            $message = "error on line {$error['line']} in the file {$error['file']}";
            array_push($errors, $message);
        }
    }
    return $errors;
}
