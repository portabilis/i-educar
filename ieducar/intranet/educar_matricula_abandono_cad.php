<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_matricula;
    public $ref_cod_reserva_vaga;
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
        $retorno = 'Novo';

        $this->cod_matricula=$_GET['ref_cod_matricula'];
        $this->ref_cod_aluno=$_GET['ref_cod_aluno'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        $obj_matricula = new clsPmieducarMatricula($this->cod_matricula, null, null, null, $this->pessoa_logada, null, null, 6);

        $det_matricula = $obj_matricula->detalhe();

        if (!$det_matricula) {
            $this->simpleRedirect('educar_matricula_lst.php');
        }

        if ($obj_matricula->edita()) {
            echo "<script>
                alert('Abandono realizado com sucesso');
                window.location='educar_matricula_det.php?cod_matricula={$this->cod_matricula}';
                </script>";
        }

        die();

        return;
    }

    public function Gerar()
    {
    }

    public function Novo()
    {
    }

    public function Excluir()
    {
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-matricula-abandono-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Abandono Matrícula';
        $this->processoAp = '578';
    }
};
