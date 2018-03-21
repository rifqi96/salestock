<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // This will replace our 404 response of MODEL NOT FOUND with a json response
        if ($exception instanceof ModelNotFoundException &&
            $request->wantsJson())
        {
            return response([
                'status' => 404,
                'code' => $exception->getCode(),
                'message' => 'Resource not found'
            ], 404);
        }

        // This will replace our 405 response of METHOD NOT ALLOWED with a json response
        if ($exception instanceof MethodNotAllowedHttpException && $request->wantsJson()){
            return response([
                'status' => 405,
                'code' => $exception->getCode(),
                'message' => 'Method not allowed'
            ], 405);
        }

        // Exception for common error and custom message if any
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = 500;
        }

        switch ($statusCode) {
            case 404:
                $message = 'Not Found';
                break;

            case 403:
                $message = 'Forbidden Request';
                break;

            default:
                $message = $exception->getMessage();
                break;
        }

        return response([
            'status' => $statusCode,
            'code' => $exception->getCode(),
            'message' => $message
        ], $statusCode);

//        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response([
            'status' => 401,
            'code' => 0,
            'message' => 'Unauthenticated'
        ], 401);
    }
}
