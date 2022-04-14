<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset;

    public function Gerar()
    {
        $obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
        $obj_usuario_det = $obj_usuario->detalhe();
        $this->ref_cod_instituicao = $obj_usuario_det['ref_cod_instituicao'];

        $obj_permissoes = new clsPermissoes();

        $nivelUsuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivelUsuario == 4) {
            $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);

            $obj_instituicao = new clsPmieducarInstituicao();
            $lst_instituicao = $obj_instituicao->lista($this->ref_cod_instituicao);

            if (is_array($lst_instituicao)) {
                $det_instituicao      = array_shift($lst_instituicao);
                $this->nm_instituicao = $det_instituicao['nm_instituicao'];
                $this->campoRotulo('nm_instituicao', 'Institução', $this->nm_instituicao);
            }
        }

        $this->largura = '100%';

        $this->breadcrumb('Documentação padrão', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->inputsHelper()->dynamic(['instituicao']);

        $opcoes_relatorio = [];
        $opcoes_relatorio[''] = 'Selecione';
        $this->campoLista('relatorio', 'Relatório', $opcoes_relatorio);
    }

    public function Formular()
    {
        $this->title = 'Documentação padrão';
        $this->processoAp = '578';
    }

    public function makeCss()
    {
        return file_get_contents(__DIR__ . '/styles/extra/DocumentacaoPadrao.css');
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/DocumentacaoPadrao.js');
    }
};
