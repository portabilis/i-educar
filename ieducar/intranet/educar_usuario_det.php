<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/pmieducar/clsPmieducarEscolaUsuario.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} i-Educar - Usu&aacute;rio");
        $this->processoAp = '555';
    }
}

class indice extends clsDetalhe
{
    public $cod_usuario;
    public $ref_cod_escola;
    public $ref_cod_instituicao;
    public $ref_funcionario_cad;
    public $ref_funcionario_exc;
    public $ref_cod_tipo_usuario;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Usu&aacute;rio - Detalhe';

        $cod_pessoa = $this->cod_usuario = $_GET['ref_pessoa'];

        $obj_pessoa = new clsPessoa_($cod_pessoa);
        $det_pessoa = $obj_pessoa->detalhe();

        $this->addDetalhe(['Nome', $det_pessoa['nome']]);

        $obj_fisica = new clsFisica($cod_pessoa);
        $det_fisica = $obj_fisica->detalhe();
        $this->addDetalhe(['CPF', int2CPF($det_fisica['cpf'])]);

        $obj_funcionario = new clsFuncionario($cod_pessoa);
        $det_funcionario = $obj_funcionario->detalhe();

        $this->addDetalhe(['E-mail usuário', $det_funcionario['email']]);

        if (!empty($det_funcionario['matricula_interna'])) {
            $this->addDetalhe(['Matr&iacute;cula interna', $det_funcionario['matricula_interna']]);
        }

        $obj_fisica = new clsFisica($cod_pessoa);
        $det_fisica = $obj_fisica->detalhe();

        $sexo = ($det_fisica['sexo'] == 'M') ? 'Masculino' : 'Feminino';
        $this->addDetalhe(['Sexo', $sexo]);
        $this->addDetalhe(['Matrícula', $det_funcionario['matricula']]);

        $ativo_f = ($det_funcionario['ativo'] == '1') ? 'Ativo' : 'Inativo';
        $this->addDetalhe(['Status', $ativo_f]);

        $tmp_obj = new clsPmieducarUsuario($this->cod_usuario);
        $registro = $tmp_obj->detalhe();

        $obj_ref_cod_tipo_usuario = new clsPmieducarTipoUsuario($registro['ref_cod_tipo_usuario']);
        $det_ref_cod_tipo_usuario = $obj_ref_cod_tipo_usuario->detalhe();
        $registro['ref_cod_tipo_usuario'] = $det_ref_cod_tipo_usuario['nm_tipo'];

        $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

        $escolasUsuario = new clsPmieducarEscolaUsuario();
        $escolasUsuario = $escolasUsuario->lista($cod_pessoa);

        foreach ($escolasUsuario as $escola) {
            $escolaDetalhe = new clsPmieducarEscola($escola['ref_cod_escola']);
            $escolaDetalhe = $escolaDetalhe->detalhe();
            $nomesEscola[] = $escolaDetalhe['nome'];
        }
        $nomesEscola = implode('<br>', $nomesEscola);
        $registro['ref_cod_escola'] = $nomesEscola;

        if ($registro['ref_cod_tipo_usuario']) {
            $this->addDetalhe([ 'Tipo Usu&aacute;rio', "{$registro['ref_cod_tipo_usuario']}"]);
        }

        if ($registro['ref_cod_instituicao']) {
            $this->addDetalhe([ 'Institui&ccedil;&atilde;o', "{$registro['ref_cod_instituicao']}"]);
        }

        if ($registro['ref_cod_escola']) {
            $this->addDetalhe([ 'Escolas', $registro['ref_cod_escola']]);
        }

        $objPermissao = new clsPermissoes();
        if ($objPermissao->permissao_cadastra(555, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_usuario_cad.php';
            $this->url_editar = "educar_usuario_cad.php?ref_pessoa={$cod_pessoa}";
        }

        $this->url_cancelar = 'educar_usuario_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do usuário', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
