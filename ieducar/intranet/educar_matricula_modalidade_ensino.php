<?php

return new class extends clsCadastro {
    public $cod_matricula;
    public $ref_cod_aluno;
    public $matricula;
    public $modalidade_ensino;

    public function Formular()
    {
        $this->title = 'Modalidade de ensino';
        $this->processoAp = 578;
    }

    public function Inicializar()
    {
        $this->cod_matricula = $_GET['ref_cod_matricula'];

        $this->validaPermissao();
        $this->validaParametros();

        return 'Editar';
    }

    public function Gerar()
    {
        $this->campoOculto('cod_matricula', $this->cod_matricula);

        $this->nome_url_cancelar = 'Voltar';
        $this->url_cancelar = "educar_matricula_det.php?cod_matricula={$this->cod_matricula}";

        $this->breadcrumb('Modalidade de ensino', [
            url('/') => 'Início',
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->campoRotulo('nm_aluno', 'Aluno', $this->matricula['nome']);

        $this->campoLista(
            'modalidade_ensino',
            'Modalidade de ensino:',
            clsPmieducarMatricula::MODELOS_DE_ENSINO,
            (int) $this->matricula['modalidade_ensino'],
            '',
            false,
            '',
            '',
            false,
            false
        );
    }

    public function Editar()
    {
        $this->validaPermissao();

        try {
            $matricula = (new clsPmieducarMatricula($this->cod_matricula));
            $matricula->modalidade_ensino = (int) $this->modalidade_ensino;
            $matricula->edita();
        } catch (Exception) {
            $this->mensagem = 'Erro ao atualizar a modalidade de ensino.';

            return false;
        }

        $this->mensagem = 'Modalidade de ensino atualizado com sucesso.';

        $this->matricula = $matricula->detalhe();

        return true;
    }

    private function validaPermissao()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
    }

    private function validaParametros()
    {
        $matricula = (new clsPmieducarMatricula($this->cod_matricula))->detalhe();

        if (empty($matricula)) {
            $this->simpleRedirect("educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
        }

        $this->matricula = $matricula;
    }
};
