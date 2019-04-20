<?php

namespace App\Policies;

use App\Menu;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProcessPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param int  $process
     *
     * @return bool
     */
    public function view(User $user, $process)
    {
        if (empty($process)) {
            return true;
        }

        $root = Menu::query()
            ->whereNull('parent_id')
            ->pluck('process')
            ->toArray();

        if (in_array($process, $root)) {
            return true;
        }

        $allow = $user->processes()
            ->wherePivot('visualiza', 1)
            ->where('process', $process)
            ->first();

        if ($allow) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param int  $process
     *
     * @return bool
     */
    public function modify(User $user, $process)
    {
        if (empty($process)) {
            return true;
        }

        $allow = $user->processes()
            ->wherePivot('cadastra', 1)
            ->where('process', $process)
            ->first();

        if ($allow) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param int  $process
     *
     * @return bool
     */
    public function remove(User $user, $process)
    {
        if (empty($process)) {
            return true;
        }

        $allow = $user->processes()
            ->wherePivot('exclui', 1)
            ->where('process', $process)
            ->first();

        if ($allow) {
            return true;
        }

        return false;
    }
}
