<?php

namespace Tests;

use App\User;

trait LoginFirstUser
{
    /**
     * Faz login com o primeiro usuário encontrado no banco de dados.
     *
     * @return void
     */
    public function loginWithFirstUser()
    {
        $this->actingAs(User::query()->first());
    }
}
