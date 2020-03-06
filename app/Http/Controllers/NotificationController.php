<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Process;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use iEducar\Modules\Notifications\Status;

class NotificationController extends Controller
{
    /**
     * @param Request $request
     * @param User $user
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

        $query->orderBy('created_at', 'desc');

        return view('notification.index', ['notifications' => $query->paginate()]);
    }

    public function markAsRead(Request $request, User $user)
    {
        $notifications = $request->get('notifications', []);

        Notification::where('user_id', $user->getKey())
            ->whereIn('id', $notifications)
            ->update(['read_at' => now()]);
    }

    public function getByLoggedUser(User $user)
    {
        return Notification::where('user_id', $user->getKey())
            ->limit(5)
            ->orderBy('read_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
