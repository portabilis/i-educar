<?php

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Documentação padrão';

        $this->breadcrumb(currentPage: 'Documentação padrão', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->cod_instituicao = $_GET['cod_instituicao'];

        return $retorno;
    }

    public function Gerar()
    {
        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: ['/vendor/legacy/Cadastro/Assets/Javascripts/DocumentacaoPadrao.js']);

        $obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
        $obj_usuario_det = $obj_usuario->detalhe();
        $this->ref_cod_escola = $obj_usuario_det['ref_cod_escola'];

        $this->campoOculto(nome: 'cod_instituicao', valor: $this->cod_instituicao);
        $this->campoOculto(nome: 'pessoa_logada', valor: $this->pessoa_logada);
        $this->campoOculto(nome: 'ref_cod_escola', valor: $this->ref_cod_escola);

        $this->campoTexto(nome: 'titulo_documento', campo: 'Título', valor: $this->titulo_documento, tamanhovisivel: 30, tamanhomaximo: 50);

        $this->campoArquivo(nome: 'documento', campo: 'Documentação padrão', valor: $this->documento, tamanho: 40, descricao: '<span id=\'aviso_formato\'>São aceitos apenas arquivos no formato PDF com até 2MB.</span>');

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
