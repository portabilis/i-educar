<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Process;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $this->breadcrumb('Notificações', []);

        $this->menu(Process::SCHEDULE);

        $query = Notification::query();

        if ($request->get('type')) {
            $query->where('type_id', $request->get('type'));
        }

        if ($request->get('status')) {
            if ($request->get('status') == '1') {
                $query->whereNotNull('read_at');
            }

            if ($request->get('status') == '2') {
                $query->whereNull('read_at');
            }
        }

        return view('notification.index', ['notifications' => $query->paginate()]);
    }
}
