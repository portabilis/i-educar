<?php

use App\Support\View\NotFoundMessage;
use PHPUnit\Framework\TestCase;

class NotFoundMessageTest extends TestCase
{
    public function test_monta_mensagem_com_codigo()
    {
        $this->assertSame(
            'Aluno não encontrado. O código 999999 não existe ou foi excluído. Use a busca abaixo para localizá-lo.',
            NotFoundMessage::make(registro: 'aluno', codigo: '999999')
        );
    }

    public function test_concorda_com_registro_feminino()
    {
        $this->assertSame(
            'Matrícula não encontrada. O código 10 não existe ou foi excluído. Use a busca abaixo para localizá-la.',
            NotFoundMessage::make(registro: 'matrícula', feminino: true, codigo: 10)
        );
    }

    public function test_omite_codigo_ausente_ou_invalido()
    {
        $esperado = 'Curso não encontrado. O registro não existe ou foi excluído. Use a busca abaixo para localizá-lo.';

        foreach ([null, '', 0, '0', '-5', 'abc', '12abc'] as $codigo) {
            $this->assertSame($esperado, NotFoundMessage::make(registro: 'curso', codigo: $codigo));
        }
    }

    public function test_aceita_motivo_e_dica_personalizados()
    {
        $this->assertSame(
            'Turma não encontrada. A turma não existe. Confira a listagem.',
            NotFoundMessage::make(registro: 'turma', feminino: true, codigo: 1, motivo: 'A turma não existe.', dica: 'Confira a listagem.')
        );
    }

    public function test_escapa_html()
    {
        $mensagem = NotFoundMessage::make(registro: '<b>aluno</b>', codigo: '<script>alert(1)</script>');

        $this->assertStringNotContainsString('<', $mensagem);
        $this->assertStringContainsString('&lt;b&gt;aluno&lt;/b&gt;', $mensagem);
    }
}
