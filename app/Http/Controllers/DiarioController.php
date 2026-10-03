<?php

namespace App\Http\Controllers;

use App\Services\DiarioControllerService;
use iEducar\Support\Exceptions\Exception as iEducarException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DiarioController extends Controller
{
    public function __construct(
        protected DiarioControllerService $service
    ) {}

    /**
     * Erros do i-Educar levam o código e as informações extras, como no resource legado.
     */
    protected function unprocessableResponse(\Throwable $exception): JsonResponse
    {
        $body = [
            'message' => $exception->getMessage(),
        ];

        if ($exception instanceof iEducarException) {
            $body['code'] = $exception->getCode();
            $body['extra'] = $exception->getExtraInfo();
        }

        return response()->json($body, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
