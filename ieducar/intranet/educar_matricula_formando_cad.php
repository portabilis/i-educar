<?php

return new class extends clsCadastro {
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
        //  print_r($_POST);die;
        $retorno = 'Novo';

        //$this->ref_cod_turma=$_GET["ref_cod_turma"];

        foreach ($_GET as $key =>$value) {
            $this->$key = $value;
        }

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, 'educar_matricula_lst.php');

        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->formando)) {
            $obj = new clsPmieducarMatricula($this->ref_cod_matricula, null, null, null, $this->pessoa_logada, null, null, null, null, null, null, null, null, null, $this->formando);
            $registro  = $obj->detalhe();
            if ($registro) {
                if (!$obj->edita()) {
                    echo 'erro ao cadastrar';
                    die;
                }
                $des = '';
                if (!$this->formando) {
                    $des = 'des';
                }
                echo "<script>alert('Matrícula {$des}marcada como formando com sucesso!'); window.location='educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}';</script>";
            }
        }

        $this->simpleRedirect('educar_matricula_lst.php');
    }

    public function Gerar()
    {
        die;
    }

    public function Novo()
    {
    }

    public function Editar()
    {
    }

    public function Excluir()
    {
    }

    public function Formular()
    {
        $this->title = 'Matricula Turma';
        $this->processoAp = '578';
    }
};
