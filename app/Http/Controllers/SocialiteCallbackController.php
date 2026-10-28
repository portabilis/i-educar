<?php

namespace App\Http\Controllers;

use App\Events\UserSocialiteNotFound;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as UserSocialite;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class SocialiteCallbackController
{
    public function __invoke(Request $request)
    {
        if ($request->has('error') || !$request->has('code')) {
            return $this->loginWithError('Autorização de acesso não concedida.');
        }

        try {
            $socialite = Socialite::driver('passport')->user();
        } catch (InvalidStateException) {
            return $this->loginWithError('Sessão de login expirada. Tente entrar novamente.');
        }

        $user = $this->findUserByEmail($socialite);

        if (empty($user)) {
            return $this->loginWithError('Usuário não encontrado.');
        }

        if ($user->isInactive()) {
            return $this->loginWithError($user->employee->motivo ?: __('auth.inactive'));
        }

        Auth::login($user);

        return redirect()->intended();
    }

    private function loginWithError(string $message): RedirectResponse
    {
        return redirect('/login')->withErrors([
            'login' => $message,
        ]);
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

    /**
     * Mais de um usuário pode ter o mesmo e-mail (um cadastro antigo
     * desativado e o atual, por exemplo). O usuário ativo tem prioridade:
     * pegar o primeiro qualquer barrava o login com "conta desativada"
     * mesmo existindo um usuário ativo com o e-mail.
     *
     * Sem ativo, devolve o inativo em vez de nada: o login segue barrado
     * com a mensagem de conta desativada, e o usuário não é tratado como
     * inexistente (o que dispararia UserSocialiteNotFound, e quem escuta
     * esse evento pode criar um usuário novo para o e-mail).
     */
    private function findUser($email): ?User
    {
        $active = User::query()
            ->where('ativo', 1)
            ->whereHas('employee', function ($query) use ($email) {
                $query
                    ->where('email', $email)
                    ->where('ativo', 1);
            })
            ->orderBy('cod_usuario')
            ->first();

        return $active ?? User::query()
            ->whereHas('employee', function ($query) use ($email) {
                $query->where('email', $email);
            })
            ->orderBy('cod_usuario')
            ->first();
    }
}
