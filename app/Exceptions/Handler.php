<?php

namespace App\Exceptions;

use App\Http\Controllers\LegacyController;
use App_Model_Exception;
use iEducar\Modules\ErrorTracking\Tracker;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        App_Model_Exception::class,
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
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if (config('app.trackerror') && $this->shouldReport($e)) {
                $data = [
                    'context' => $this->getContext(),
                    'controller' => $this->getController(),
                    'action' => $this->getAction(),
                ];

                app(Tracker::class)->notify($e, $data);
            }
        });
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

    /**
     * Return current controller
     *
     * @return array|mixed
     */
    private function getController()
    {
        if (app()->runningInConsole()) {
            return null;
        }

        $controller = explode('@', $this->getActionName())[0];

        if ($controller == class_basename(LegacyController::class)) {
            $controller = app('request')->path();
        }

        return $controller;
    }

    /**
     * Return current action.
     *
     * @return array|mixed
     */
    private function getAction()
    {
        if (app()->runningInConsole()) {
            return null;
        }

        $action = explode('@', $this->getActionName());

        return $action[1] ?? $action[0];
    }

    /**
     * Return current action name.
     *
     * @return string
     */
    private function getActionName()
    {
        $controller = app('request')->route()->getAction();
        $controller = class_basename($controller['controller']);

        return $controller;
    }
}
