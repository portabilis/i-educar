<?php

namespace Tests;

use Database\Factories\LegacyUserFactory;

trait LoginFirstUser
{
    /**
     * Faz login com o primeiro usuário encontrado no banco de dados.
     *
     * @return void
     */
    public function loginWithFirstUser()
    {
        $user = LegacyUserFactory::new()->admin()->create();

        $this->actingAs($user);
    }
}
