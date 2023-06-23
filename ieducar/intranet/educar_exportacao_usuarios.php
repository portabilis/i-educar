<?php

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $ano;

    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 999869,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_index.php'
        );
        $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

        $this->breadcrumb(currentPage: 'Exportação de usuários', breadcrumbs: [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        return 'Nova exportação';
    }

    public function Gerar()
    {
        $this->inputsHelper()->dynamic(['instituicao']);
        $this->inputsHelper()->dynamic(helperNames: 'escola', inputOptions: ['required' => false]);

        $resourcesStatus = [1 => 'Ativo',
            0 => 'Inativo'];
        $optionsStatus = ['label' => 'Status', 'resources' => $resourcesStatus, 'value' => 1];
        $this->inputsHelper()->select(attrName: 'status', inputOptions: $optionsStatus);

        $opcoes = ['' => 'Selecione'];

        $objTemp = new clsPmieducarTipoUsuario();
        $objTemp->setOrderby('nm_tipo ASC');

        $lista = $objTemp->lista(int_ativo: 1);

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['cod_tipo_usuario']}"] = "{$registro['nm_tipo']}";
                $opcoes_["{$registro['cod_tipo_usuario']}"] = "{$registro['nivel']}";
            }
        }

        $tamanho = count($opcoes_);
        echo "<script>\nvar cod_tipo_usuario = new Array({$tamanho});\n";
        foreach ($opcoes_ as $key => $valor) {
            echo "cod_tipo_usuario[{$key}] = {$valor};\n";
        }
        echo '</script>';

        $this->campoLista(nome: 'ref_cod_tipo_usuario', campo: 'Tipo usuário', valor: $opcoes, default: $this->ref_cod_tipo_usuario, duplo: null, descricao: null, complemento: null, desabilitado: null, obrigatorio: false);

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: '/vendor/legacy/ExportarUsuarios/exportarUsuarios.js');

        $this->nome_url_sucesso = 'Exportar';
        $this->acao_enviar = ' ';
    }

    public function Formular()
    {
        $this->title = 'Nova exportação';
        $this->processoAp = 999869;
    }
};
