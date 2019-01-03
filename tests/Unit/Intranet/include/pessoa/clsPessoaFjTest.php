<?php

namespace Tests\Unit\Model;

use Tests\SuiteTestCase\TestCase;

class clsPessoaFjTest extends TestCase
{
    public function testGetById()
    {
        $pdo = $this->getConnection()->getConnection();
        $pdo->exec(
            'INSERT INTO cadastro.pessoa (nome, data_cad, tipo, situacao, origem_gravacao, operacao, idsis_cad) VALUES (\'Fulano\', now(), \'F\', \'P\', \'U\', \'I\', 17)'
        );
        $id = $pdo->lastInsertId('cadastro.seq_pessoa');

        $pessoaFj = new \clsPessoaFj();
        $pessoaFj->idpes = $id;
        $pessoa = $pessoaFj->detalhe();

        $this->getConnection()->getConnection()->exec(
            "DELETE FROM cadastro.pessoa WHERE idpes = $id;"
        );

        $this->assertNotNull($id);
        $this->assertEquals($pessoa['nome'], 'Fulano');
        $this->assertEquals($pessoa['idpes'], $id);
    }
}
