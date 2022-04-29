<?php

return new class extends clsCadastro {
    public $pessoa_logada;

    public $ano;
    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            999869,
            $this->pessoa_logada,
            7,
            'educar_index.php'
        );
        $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

        $this->breadcrumb('Exportação de usuários', [
        url('intranet/educar_configuracoes_index.php') => 'Configurações',
    ]);

        return 'Nova exportação';
    }

    public function Gerar()
    {
        $this->inputsHelper()->dynamic(['instituicao']);
        $this->inputsHelper()->dynamic('escola', ['required' =>  false]);

        $resourcesStatus = [1  => 'Ativo',
                       0  => 'Inativo'];
        $optionsStatus   = ['label' => 'Status', 'resources' => $resourcesStatus, 'value' => 1];
        $this->inputsHelper()->select('status', $optionsStatus);

        $opcoes = [ '' => 'Selecione' ];

        $objTemp = new clsPmieducarTipoUsuario();
        $objTemp->setOrderby('nm_tipo ASC');

        $lista = $objTemp->lista(null, null, null, null, null, null, null, null, 1);

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['cod_tipo_usuario']}"] = "{$registro['nm_tipo']}";
                $opcoes_["{$registro['cod_tipo_usuario']}"] = "{$registro['nivel']}";
            }
        }

        $tamanho = sizeof($opcoes_);
        echo "<script>\nvar cod_tipo_usuario = new Array({$tamanho});\n";
        foreach ($opcoes_ as $key => $valor) {
            echo "cod_tipo_usuario[{$key}] = {$valor};\n";
        }
        echo '</script>';

        $this->campoLista('ref_cod_tipo_usuario', 'Tipo usuário', $opcoes, $this->ref_cod_tipo_usuario, '', null, null, null, null, false);

        Portabilis_View_Helper_Application::loadJavascript($this, '/modules/ExportarUsuarios/exportarUsuarios.js');

        $this->nome_url_sucesso = 'Exportar';
        $this->acao_enviar      = ' ';
    }

    public function Formular()
    {
        $this->title = 'Nova exportação';
        $this->processoAp = 999869;
    }
};
