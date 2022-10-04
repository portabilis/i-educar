<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Process;
use App\Services\NotificationUrlPresigner;
use App\User;
use clsPmieducarServidor;
use iEducar\Modules\Notifications\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * @param Request $request
     * @param User    $user
     *
     * @return View
     */
    public function index(Request $request, User $user)
    {
        $this->breadcrumb('Notificações', []);

        $this->menu(Process::SCHEDULE);


        $query = Notification::query();

        $query->where('user_id', $user->getKey());

        if ($request->get('type')) {
            $query->where('type_id', $request->get('type'));
        }

        if ($request->get('status')) {
            if ($request->get('status') == STATUS::READ) {
                $query->whereNotNull('read_at');
            }

            if ($request->get('status') == STATUS::UNREAD) {
                $query->whereNull('read_at');
            }
        }

        if ($request->get('text')) {
            $query->where('text', 'ilike', '%' . $request->get('text') . '%');
        }

        $query->orderBy('created_at', 'desc');

        $obj_servidor = new clsPmieducarServidor(
            Auth::id(),
            null,
            null,
            null,
            null,
            null,
            1,      //  Ativo
            1,      //  Fixado na instituição de ID 1
        );
        $isProfessor = $obj_servidor->isProfessor();

        return view('notification.index', ['notifications' => $query->paginate(), 'auth_id' => Auth::id(), 'isProfessor' => $isProfessor]);
    }

    public function markAsRead(Request $request, User $user)
    {
        $notifications = $request->get('notifications', []);

        Notification::where('user_id', $user->getKey())
            ->whereIn('id', $notifications)
            ->update(['read_at' => now()]);
    }

    public function markAllRead(User $user)
    {
        Notification::where('user_id', $user->getKey())
            ->update(['read_at' => now()]);
    }

    public function getByLoggedUser(User $user, NotificationUrlPresigner $presigner)
    {
        $limit = $this->getLimit($user);

        return Notification::query()
            ->where('user_id', $user->getKey())
            ->limit($limit)
            ->orderBy('read_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) use ($presigner) {
                $notification->link = $presigner->getNotificationUrl($notification);

                return $notification;
            });
    }

    public function getNotReadCount(User $user)
    {
        $notifications = Notification::where('user_id', $user->getKey())
            ->whereNull('read_at');

        return $notifications->count();
    }

    private function getLimit(User $user)
    {
        $notRead = $this->getNotReadCount($user);

        if ($notRead > 5) {
            return 25;
        }

        return 5;
    }
}
