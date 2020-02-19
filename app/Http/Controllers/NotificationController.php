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

        return view('notification.index', ['notifications' => Notification::paginate()]);
    }
}
