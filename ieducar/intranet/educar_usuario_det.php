<?php

return new class extends clsDetalhe
{
    public $cod_usuario;

    public $ref_cod_escola;

    public $ref_cod_instituicao;

    public $ref_funcionario_cad;

    public $ref_cod_tipo_usuario;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Usuário - Detalhe';

        $cod_pessoa = $this->cod_usuario = $_GET['ref_pessoa'];

        $obj_pessoa = new clsPessoa_(int_idpes: $cod_pessoa);
        $det_pessoa = $obj_pessoa->detalhe();

        $this->addDetalhe(detalhe: ['Nome', $det_pessoa['nome']]);

        $obj_fisica = new clsFisica(idpes: $cod_pessoa);
        $det_fisica = $obj_fisica->detalhe();
        $this->addDetalhe(detalhe: ['CPF', int2CPF(int: $det_fisica['cpf'])]);

        $obj_funcionario = new clsFuncionario(int_idpes: $cod_pessoa);
        $det_funcionario = $obj_funcionario->detalhe();

        $this->addDetalhe(detalhe: ['E-mail usuário', $det_funcionario['email']]);

        if (!empty($det_funcionario['matricula_interna'])) {
            $this->addDetalhe(detalhe: ['Matrícula interna', $det_funcionario['matricula_interna']]);
        }

        $obj_fisica = new clsFisica(idpes: $cod_pessoa);
        $det_fisica = $obj_fisica->detalhe();

        $sexo = ($det_fisica['sexo'] == 'M') ? 'Masculino' : 'Feminino';
        $this->addDetalhe(detalhe: ['Sexo', $sexo]);
        $this->addDetalhe(detalhe: ['Matrícula', $det_funcionario['matricula']]);

        $tmp_obj = new clsPmieducarUsuario(cod_usuario: $this->cod_usuario);
        $registro = $tmp_obj->detalhe();

        $ativo_f = ($registro['ativo'] == '1') ? 'Ativo' : 'Inativo';
        $this->addDetalhe(detalhe: ['Status', $ativo_f]);

        $obj_ref_cod_tipo_usuario = new clsPmieducarTipoUsuario(cod_tipo_usuario: $registro['ref_cod_tipo_usuario']);
        $det_ref_cod_tipo_usuario = $obj_ref_cod_tipo_usuario->detalhe();
        $registro['ref_cod_tipo_usuario'] = $det_ref_cod_tipo_usuario['nm_tipo'];

        $obj_ref_cod_instituicao = new clsPmieducarInstituicao(cod_instituicao: $registro['ref_cod_instituicao']);
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

        $escolasUsuario = new clsPmieducarEscolaUsuario();
        $escolasUsuario = $escolasUsuario->lista(ref_cod_usuario: $cod_pessoa);

        $nomesEscola = [];
        foreach ($escolasUsuario as $escola) {
            $escolaDetalhe = new clsPmieducarEscola(cod_escola: $escola['ref_cod_escola']);
            $escolaDetalhe = $escolaDetalhe->detalhe();
            $nomesEscola[] = $escolaDetalhe['nome'];
        }
        $nomesEscola = implode(separator: '<br>', array: $nomesEscola);
        $registro['ref_cod_escola'] = $nomesEscola;

        if ($registro['ref_cod_tipo_usuario']) {
            $this->addDetalhe(detalhe: ['Tipo Usuário', "{$registro['ref_cod_tipo_usuario']}"]);
        }

        if ($registro['ref_cod_instituicao']) {
            $this->addDetalhe(detalhe: ['Instituição', "{$registro['ref_cod_instituicao']}"]);
        }

        if ($registro['ref_cod_escola']) {
            $this->addDetalhe(detalhe: ['Escolas', $registro['ref_cod_escola']]);
        }

        $objPermissao = new clsPermissoes();
        if ($objPermissao->permissao_cadastra(int_processo_ap: 555, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->url_novo = 'educar_usuario_cad.php';
            $this->url_editar = "educar_usuario_cad.php?ref_pessoa={$cod_pessoa}";
        }

        $this->url_cancelar = 'educar_usuario_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe do usuário', breadcrumbs: [
            url(path: 'intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Usuário';
        $this->processoAp = '555';
    }
};
