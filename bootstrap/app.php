<?php

use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $exception, Request $request) {
            if ($request->expectsJson()) {

                $errors = $exception->validator->errors()->toArray();
                $error = $errors[array_key_first($errors)][0] ?? '';

                return ApiResponseHelper::sendResponse(new Result($errors,
                    null, $error, false, Response::HTTP_UNPROCESSABLE_ENTITY));
            }

            return back(Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->expectsJson()) {
                return ApiResponseHelper::sendResponse(new Result(null,
                    null, 'UnAuthenticated', false, Response::HTTP_UNAUTHORIZED));
            }

            return back(Response::HTTP_UNPROCESSABLE_ENTITY);
        });
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {

            if ($request->is('api/*')) {
                return ApiResponseHelper::sendResponse(new Result(null,
                    null, $e->getMessage() ?? 'Record not found.', false,
                    Response::HTTP_NOT_FOUND));
            }

            return back(Response::HTTP_UNPROCESSABLE_ENTITY);
        });
    })->create();
