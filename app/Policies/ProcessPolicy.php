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
    public function view(User $user, $process = 0)
    {
        if (empty($process)) {
            return true;
        }

        $root = Menu::query()
            ->whereNull('parent_id')
            ->where('process', $process)
            ->exists();

        if ($root) {
            return true;
        }

        return $user->processes()
            ->wherePivot('visualiza', 1)
            ->where('process', $process)
            ->exists();
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

        return $user->processes()
            ->wherePivot('cadastra', 1)
            ->where('process', $process)
            ->exists();
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

        return $user->processes()
            ->wherePivot('exclui', 1)
            ->where('process', $process)
            ->exists();
    }
}
