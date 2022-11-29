<?php

namespace App\Helpers;

use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository;

class UserCache
{
    public static function user($id)
    {
        $cache = app(Repository::class);

        return $cache->remember('user_' . $id, Carbon::now()->addHours(12), function () use ($id) {
            return User::query()->with([
                'person',
                'type',
                'employee'
            ])->find($id);
        });
    }
}
