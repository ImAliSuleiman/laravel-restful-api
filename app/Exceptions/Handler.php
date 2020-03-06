<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

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
     * @param \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // Return 404 response with JSON structure
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'data' => 'Data not found'
            ], 404);
        } else if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'data' => 'Resource not found'
            ], 404);
        }

        if ($exception instanceof AuthenticationException) {
            $this->unauthenticated($request, $exception);
        }
        
//        if ($exception instanceof AuthenticationException) {
//            // $this->unauthenticated($request, $exception);
//            return response()->json([
//                'message' => 'Unauthenticated'
//            ], 401);
//        }
        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // return parent::unauthenticated($request, $exception);

        return response()->json([
            'message' => 'Unauthenticated'
        ], 401);
    }
}
