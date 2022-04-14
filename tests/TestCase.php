<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Utilizar este método pode permitir falta de integridade nos testes com o
     * banco de dados. Para suprir a falta de integridade, factories devem ser
     * criadas para serem utilizadas ao criar um registro com dependências.
     *
     * @deprecated
     *
     * @return void
     */
    protected function disableForeignKeys()
    {
        DB::statement('SET session_replication_role = replica;');
    }

    /**
     * Utilizar este método pode permitir falta de integridade nos testes com o
     * banco de dados. Para suprir a falta de integridade, factories devem ser
     * criadas para serem utilizadas ao criar um registro com dependências.
     *
     * @deprecated
     *
     * @return void
     */
    protected function enableForeignKeys()
    {
        DB::statement('SET session_replication_role = DEFAULT;');
    }

    /**
     * Retorna o header de autorização da API.
     *
     * @return string[]
     */
    protected function getAuthorizationHeader()
    {
        return [
            'Authorization' => 'Bearer ' . env('API_ACCESS_KEY')
        ];
    }

    /**
     * Inicializa as traits helpers.
     *
     * @return array
     */
    protected function setUpTraits()
    {
        $uses = parent::setUpTraits();

        if (isset($uses[LoginFirstUser::class])) {
            $this->loginWithFirstUser();
        }

        return $uses;
    }

    /**
     * Método necessário para executar testes legados.
     *
     * @deprecated
     *
     * @return string
     */
    public function getHtmlCodeFromFile($fileName)
    {
        return file_get_contents(__DIR__ . '/Unit/assets/' . $fileName);
    }
}
