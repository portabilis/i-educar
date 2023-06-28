<?php

namespace App\Helpers;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class UserCache
{
    public static function user($id)
    {
        return Cache::remember('user_' . $id, Carbon::now()->addHours(12), function () use ($id) {
            return User::query()->with([
                'person',
                'type',
                'employee',
            ])->find($id);
        });
    }
}
