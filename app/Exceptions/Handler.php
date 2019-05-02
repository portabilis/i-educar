<?php

namespace App\Exceptions;

use Exception;
use iEducar\Modules\ErrorTracking\Tracker;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
     * @param Exception $exception
     *
     * @return void
     *
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        if (config('app.trackerror') && $this->shouldReport($exception)) {
            app(Tracker::class)->notify($exception, $this->getContext());
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request   $request
     * @param Exception $exception
     *
     * @return Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * Return context data for the error.
     *
     * @return array
     */
    private function getContext()
    {
        if (app()->runningInConsole()) {
            return [];
        }

        return app('request')->all();
    }
}
