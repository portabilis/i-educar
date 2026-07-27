<?php

namespace App\Http\Controllers;

use App\Events\UserSocialiteNotFound;
use App\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\User as UserSocialite;

class SocialiteCallbackController
{
    public function __invoke()
    {
        $socialite = Socialite::driver('passport')->user();

        $user = $this->findUserByEmail($socialite);

        if (empty($user)) {
            return redirect('/login')->withErrors([
                'login' => 'Usuário não encontrado.',
            ]);
        }

        if ($user->isInactive()) {
            return redirect('/login')->withErrors([
                'login' => $user->employee->motivo ?: __('auth.inactive'),
            ]);
        }

        Auth::login($user);

        return redirect()->intended();
    }

    public function findUserByEmail(UserSocialite $socialite): ?User
    {
        $user = $this->findUser($socialite->getEmail());

        if ($user) {
            return $user;
        }

        event(new UserSocialiteNotFound($socialite));

        return $this->findUser($socialite->getEmail());
    }

    private function findUser($email): ?User
    {
        return User::query()->whereHas('employee', function ($query) use ($email) {
            $query->where('email', $email);
        })->first();
    }
}
