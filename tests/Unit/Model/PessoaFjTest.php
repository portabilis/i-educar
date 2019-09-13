<?php

namespace Tests\Unit\Model;

use Tests\TestCase;

class PessoaFjTest extends TestCase
{
    public function testGetById()
    {
        $pdo = $this->getConnection()->getPdo();
        $pdo->exec(
            'INSERT INTO cadastro.pessoa (nome, data_cad, tipo, situacao, origem_gravacao, operacao) VALUES (\'Fulano\', now(), \'F\', \'P\', \'U\', \'I\')'
        );
        $id = $pdo->lastInsertId('cadastro.seq_pessoa');

        $pessoaFj = new \clsPessoaFj();
        $pessoaFj->idpes = $id;
        $pessoa = $pessoaFj->detalhe();

        $pdo->exec(
            "DELETE FROM cadastro.pessoa WHERE idpes = $id;"
        );

        $this->assertNotNull($id);
        $this->assertEquals($pessoa['nome'], 'Fulano');
        $this->assertEquals($pessoa['idpes'], $id);
    }
}
