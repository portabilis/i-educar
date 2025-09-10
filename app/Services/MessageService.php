<?php

namespace App\Services;

use App\Models\Message;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageService
{
    public function create(string $messageableType, int $messageableId, int $userId, string $description): Message
    {
        return DB::transaction(function () use ($messageableType, $messageableId, $userId, $description) {
            $message = Message::create([
                'messageable_type' => $messageableType,
                'messageable_id' => $messageableId,
                'user_id' => $userId,
                'description' => $description,
            ]);

            $message->load('user');

            return $message;
        });
    }

    public function update(int $messageId, int $userId, string $description): Message
    {
        return DB::transaction(function () use ($messageId, $userId, $description) {
            $message = Message::findOrFail($messageId);

            if (!$this->canEditMessage($message, $userId)) {
                throw new AuthorizationException('Você não tem permissão para editar esta mensagem');
            }

            $message->update(['description' => $description]);

            $message->load('user');

            return $message;
        });
    }

    public function delete(int $messageId, int $userId): bool
    {
        return DB::transaction(function () use ($messageId, $userId) {
            $message = Message::findOrFail($messageId);

            if (!$this->canDeleteMessage($message, $userId)) {
                throw new AuthorizationException('Você não tem permissão para excluir esta mensagem');
            }

            return $message->delete();
        });
    }

    public function find(int $messageId): ?Message
    {
        return Message::with('user')->find($messageId);
    }

    public function get(string $messageableType, int $messageableId): \Illuminate\Database\Eloquent\Collection
    {
        return Message::with('user')
            ->where('messageable_type', $messageableType)
            ->where('messageable_id', $messageableId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function canEditMessage(Message $message, int $userId): bool
    {
        if ($message->user_id === $userId) {
            return true;
        }

        return $this->isPoliOrInstitutionalUser($userId);
    }

    private function canDeleteMessage(Message $message, int $userId): bool
    {
        return $this->canEditMessage($message, $userId);
    }

    private function isPoliOrInstitutionalUser(int $userId): bool
    {
        if ($userId === Auth::id()) {
            return Auth::user()->isAdmin() || Auth::user()->isInstitutional();
        }

        $user = User::find($userId);

        return $user && ($user->isAdmin() || $user->isInstitutional());
    }
}
