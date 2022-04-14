<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Documentação padrão';

        $this->breadcrumb('Documentação padrão', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->cod_instituicao=$_GET['cod_instituicao'];

        return $retorno;
    }

    public function Gerar()
    {
        Portabilis_View_Helper_Application::loadJavascript($this, ['/modules/Cadastro/Assets/Javascripts/DocumentacaoPadrao.js']);

        $obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
        $obj_usuario_det = $obj_usuario->detalhe();
        $this->ref_cod_escola = $obj_usuario_det['ref_cod_escola'];

        $this->campoOculto('cod_instituicao', $this->cod_instituicao);
        $this->campoOculto('pessoa_logada', $this->pessoa_logada);
        $this->campoOculto('ref_cod_escola', $this->ref_cod_escola);

        $this->campoTexto('titulo_documento', 'Título', $this->titulo_documento, 30, 50, false);

        $this->campoArquivo('documento', 'Documentação padrão', $this->documento, 40, '<span id=\'aviso_formato\'>São aceitos apenas arquivos no formato PDF com até 2MB.</span>');

        $this->array_botao[] = 'Salvar';
        $this->array_botao_url_script[] = 'go(\'educar_instituicao_lst.php\')';

        $this->array_botao[] = 'Voltar';
        $this->array_botao_url_script[] = 'go(\'educar_instituicao_lst.php\')';
    }

    public function Formular()
    {
        $this->title = 'Documentação padrão';
        $this->processoAp = '578';
    }
};
