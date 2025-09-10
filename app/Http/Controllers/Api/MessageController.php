<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Message\IndexMessageRequest;
use App\Http\Requests\Api\Message\StoreMessageRequest;
use App\Http\Requests\Api\Message\UpdateMessageRequest;
use App\Http\Resources\MessageResource;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    private MessageService $messageService;

    protected string $messageLabel = 'mensagem';

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function index(IndexMessageRequest $request)
    {
        $messages = $this->messageService->get(
            $request->query('messageable_type'),
            $request->query('messageable_id')
        );

        return MessageResource::collection($messages);
    }

    public function store(StoreMessageRequest $request)
    {
        $message = $this->messageService->create(
            $request->messageable_type,
            $request->messageable_id,
            Auth::id(),
            $request->description
        );

        return (new MessageResource($message))->additional([
            'message' => ucfirst($this->messageLabel) . ' adicionada com sucesso',
        ]);
    }

    public function show(int $messageId)
    {
        $message = $this->messageService->find($messageId);

        if (!$message) {
            return response()->json(['message' => ucfirst($this->messageLabel) . ' não encontrada'], 404);
        }

        return new MessageResource($message);
    }

    public function update(UpdateMessageRequest $request, int $messageId)
    {
        $message = $this->messageService->update(
            $messageId,
            Auth::id(),
            $request->description
        );

        return (new MessageResource($message))->additional([
            'message' => ucfirst($this->messageLabel) . ' atualizada com sucesso',
        ]);
    }

    public function destroy(int $messageId): JsonResponse
    {
        $this->messageService->delete($messageId, Auth::id());

        return response()->json(['message' => ucfirst($this->messageLabel) . ' excluída com sucesso']);
    }
}
