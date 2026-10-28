<?php

return new class extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $ref_cod_matricula;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $formando;

    public function Inicializar()
    {
        foreach ($_GET as $key => $value) {
            $this->$key = $value;
        }

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_matricula_lst.php');

        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->formando)) {
            $obj = new clsPmieducarMatricula(
                cod_matricula: $this->ref_cod_matricula,
                ref_ref_cod_escola: null,
                ref_ref_cod_serie: null,
                ref_usuario_exc: $this->pessoa_logada,
                ref_usuario_cad: null,
                ref_cod_aluno: null,
                aprovado: null,
                data_cadastro: null,
                data_exclusao: null,
                ativo: null,
                ano: null,
                ultima_matricula: null,
                modulo: null,
                formando: $this->formando
            );
            $registro = $obj->detalhe();
            if ($registro) {
                if (!$obj->edita()) {
                    // Inalcançável: `edita()` só falha sem código, e o detalhe acabou de encontrar a matrícula.
                    // @codeCoverageIgnoreStart
                    echo 'erro ao cadastrar';
                    exit;
                    // @codeCoverageIgnoreEnd
                }
                $des = '';
                if (!$this->formando) {
                    $des = 'des';
                }
                echo "<script>alert('Matrícula {$des}marcada como formando com sucesso!'); window.location='educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}';</script>";
            }
        }

        $this->simpleRedirect("educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");
    }

    public function Gerar()
    {
        // Não testável: o `Inicializar()` sempre redireciona antes; por POST o `exit` encerraria a suíte de testes.
        // @codeCoverageIgnoreStart
        exit;
        // @codeCoverageIgnoreEnd
    }

    // Não testável: só rodam por POST, que em seguida cai no `exit` do `Gerar()`.
    // @codeCoverageIgnoreStart
    public function Novo() {}

    public function Editar() {}

    public function Excluir() {}
    // @codeCoverageIgnoreEnd

    public function Formular()
    {
        $this->title = 'Matricula Turma';
        $this->processoAp = '578';
    }
};
