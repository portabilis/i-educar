<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostComponentBatchCallbackRequest;
use App\Models\ComponentBatchOperation;
use App\Models\Enums\ComponentBatchStatus;
use App\Services\ComponentBatchManagerService;
use Illuminate\Http\Response;

class ComponentBatchCallbackController extends Controller
{
    public function __invoke(PostComponentBatchCallbackRequest $request, ComponentBatchManagerService $service)
    {
        $operation = ComponentBatchOperation::query()->findOrFail($request->integer('operation_id'));

        // Callback repetido não reprocessa a operação, como no resource legado.
        if ($operation->status() !== ComponentBatchStatus::RUNNING) {
            return response()->json([
                'message' => 'Operação não está em execução. Status atual: ' . $operation->status()->label(),
            ], Response::HTTP_OK);
        }

        $idiarioResult = [
            'success' => $request->boolean('success'),
            'deleted' => $request->integer('deleted'),
        ];

        if ($request->filled('error')) {
            $idiarioResult['error'] = $request->input('error');
        }

        $service->processCallback($operation, $idiarioResult);

        return response()->json([
            'message' => 'Callback processado com sucesso.',
        ], Response::HTTP_OK);
    }
}
