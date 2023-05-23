<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\GetMessage;
use Route;


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
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        $e = parent::render($request, $exception);
        if (method_exists($e, "getStatusCode")) {
            // 400
            // if ($e->getStatusCode() == 400) {
            //     \Session::flash('error-msg',GetMessage::getMessageByID("error027"));
            //     return response()->view('errors.400');
            // }
            // // 401
            // if ($e->getStatusCode() == 401) {
            //     \Session::flash('error-msg',GetMessage::getMessageByID("error028"));
            //     return response()->view('errors.401');
            // }
            // // 403
            // if ($e->getStatusCode() == 403) {
            //     \Session::flash('error-msg',GetMessage::getMessageByID("error029"));
            //     return response()->view('errors.403');
            // }
            // // 422
            // if ($e->getStatusCode() == 422) {
            //     \Session::flash('error-msg',GetMessage::getMessageByID("error030"));
            //     return response()->view('errors.422');
            // }
            // // 404
            // if ($e->getStatusCode() == 404) {
            //     \Session::flash('error-msg',GetMessage::getMessageByID("error031"));
            //     return response()->view('errors.404');
            // }
            // // 500
            // \Session::flash('error-msg',GetMessage::getMessageByID("error026"));
            // return response()->view('errors.500');
            // 419
            if ($e->getStatusCode() == 419) {
                if (Route::is("phone.*"))
                    return redirect('/loginphone');
                return redirect('/login');
            }
        }
        return parent::render($request, $exception);
    }
}
