<?php

namespace App\Http\Middleware;

use App\Models\Announcement;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AnnouncementMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($user = $request->user()) {
            $announcement = Announcement::query()
                ->whereHas('userTypes', fn ($q) => $q->whereKey($user->ref_cod_tipo_usuario)
                )->latest()->first();

            if ($announcement?->show_confirmation && !$this->userConfirmedAnnouncement($announcement, $user)) {
                Session::flash('error', 'Confirme a ciência do aviso antes de prosseguir!');

                return redirect()->route('announcement.user.show');
            }
        }

        return $next($request);
    }

    private function userConfirmedAnnouncement(Announcement $announcement, $user): bool
    {
        return $announcement->users()
            ->whereKey($user->getKey())
            ->wherePivotNotNull('confirmed_at')
            ->exists();
    }
}
