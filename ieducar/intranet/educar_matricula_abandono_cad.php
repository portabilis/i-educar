<?php

return new class extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_matricula;

    public $ref_ref_cod_escola;

    public $ref_ref_cod_serie;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_cod_aluno;

    public $aprovado;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ano;

    public $ref_cod_instituicao;

    public $ref_cod_curso;

    public $ref_cod_escola;

    public function Inicializar()
    {
        $this->cod_matricula = $_GET['ref_cod_matricula'];
        $this->ref_cod_aluno = $_GET['ref_cod_aluno'];

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        $obj_matricula = new clsPmieducarMatricula(
            cod_matricula: $this->cod_matricula,
            ref_ref_cod_escola: null,
            ref_ref_cod_serie: null,
            ref_usuario_exc: $this->pessoa_logada,
            ref_usuario_cad: null,
            ref_cod_aluno: null,
            aprovado: 6
        );

        $det_matricula = $obj_matricula->detalhe();

        if (!$det_matricula) {
            $this->redirectNotFound(
                url: 'educar_matricula_lst.php',
                registro: 'matrícula',
                feminino: true,
                codigo: $this->cod_matricula,
                dica: 'Acesse a matrícula pela ficha do aluno.',
            );
        }

        // Não testável: o caminho termina em `exit`, que encerraria a suíte de testes.
        // @codeCoverageIgnoreStart
        if ($obj_matricula->edita()) {
            echo "<script>
                alert('Situação deixou de frequentar cadastrada com sucesso');
                window.location='educar_matricula_det.php?cod_matricula={$this->cod_matricula}';
                </script>";
        }

        exit();
        // @codeCoverageIgnoreEnd
    }

    // Não testável: o `Inicializar()` termina em `exit` antes de qualquer um deles.
    // @codeCoverageIgnoreStart
    public function Gerar() {}

    public function Novo() {}

    public function Excluir() {}
    // @codeCoverageIgnoreEnd

    public function makeExtra()
    {
        // Não testável: a tela nunca chega a ser desenhada, o `Inicializar()` termina em `exit`.
        // @codeCoverageIgnoreStart
        return file_get_contents(filename: __DIR__ . '/scripts/extra/educar-matricula-abandono-cad.js');
        // @codeCoverageIgnoreEnd
    }

    public function Formular()
    {
        $this->title = 'Deixou de Frequentar Matrícula';
        $this->processoAp = '578';
    }
};
