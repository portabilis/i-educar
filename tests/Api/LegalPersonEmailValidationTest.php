<?php

namespace Tests\Api;

use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LegalPersonEmailValidationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_email_invalido_sem_arroba(): void
    {
        /** @var \App\User $user */
        $user = LegacyUserFactory::new()->admin()->createOne();
        $this->actingAs($user);

        $payload = [
            'tipoacao' => 'Novo',
            'fantasia' => 'Empresa Teste LTDA',
            'razao_social' => 'Empresa Teste LTDA ME',
            'email' => 'contatoempresa.com',
        ];

        $this->post('/intranet/empresas_cad.php', $payload)
            ->assertSuccessful()
            ->assertSee('Formato do e-mail inválido');
    }

    /**
     * Testa que URL é rejeitada como e-mail no cadastro
     */
    public function test_novo_rejeita_url_como_email(): void
    {
        /** @var \App\User $user */
        $user = LegacyUserFactory::new()->admin()->createOne();
        $this->actingAs($user);

        $payload = [
            'tipoacao' => 'Novo',
            'fantasia' => 'Empresa Teste LTDA',
            'razao_social' => 'Empresa Teste LTDA ME',
            'email' => 'https://www.google.com/search?q=contato.empresa.com',
        ];

        $this->post('/intranet/empresas_cad.php', $payload)
            ->assertSuccessful()
            ->assertSee('Formato do e-mail inválido');
    }


    /**
     * Testa que e-mail vazio é rejeitado
     */
    public function test_novo_rejeita_email_vazio(): void
    {
        /** @var \App\User $user */
        $user = LegacyUserFactory::new()->admin()->createOne();
        $this->actingAs($user);

        $payload = [
            'tipoacao' => 'Novo',
            'fantasia' => 'Empresa Teste LTDA',
            'razao_social' => 'Empresa Teste LTDA ME',
            'email' => '',
        ];

        $this->post('/intranet/empresas_cad.php', $payload)
            ->assertSuccessful()
            ->assertSee('Formato do e-mail inválido');
    }

}
