<?php

use App\Models\LegacyEmployee;
use App\Models\LegacyPerson;
use App\Models\LegacyUserSchool;
use App\Models\LegacyUserType;

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

        $pessoa = LegacyPerson::with('individual')->whereKey($cod_pessoa)->first(['idpes', 'nome']);
        $funcionario = LegacyEmployee::whereKey($cod_pessoa)->first(['email', 'matricula', 'matricula_interna']);

        $this->addDetalhe(detalhe: ['Nome', $pessoa?->nome]);
        $this->addDetalhe(detalhe: ['CPF', $pessoa?->individual?->cpf]);
        $this->addDetalhe(detalhe: ['E-mail usuário', $funcionario?->email]);

        if (!empty($funcionario?->matricula_interna)) {
            $this->addDetalhe(detalhe: ['Matrícula interna', $funcionario->matricula_interna]);
        }

        $sexo = ($pessoa?->individual?->sexo == 'M') ? 'Masculino' : 'Feminino';
        $this->addDetalhe(detalhe: ['Sexo', $sexo]);
        $this->addDetalhe(detalhe: ['Matrícula', $funcionario?->matricula]);

        $tmp_obj = new clsPmieducarUsuario(cod_usuario: $this->cod_usuario);
        $registro = $tmp_obj->detalhe();

        $ativo_f = ($registro['ativo'] == '1') ? 'Ativo' : 'Inativo';
        $this->addDetalhe(detalhe: ['Status', $ativo_f]);

        $registro['ref_cod_tipo_usuario'] = LegacyUserType::query()
            ->whereKey($registro['ref_cod_tipo_usuario'])
            ->value('nm_tipo');

        $obj_ref_cod_instituicao = new clsPmieducarInstituicao(cod_instituicao: $registro['ref_cod_instituicao']);
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

        $escolasUsuario = LegacyUserSchool::query()->where('ref_cod_usuario', $cod_pessoa)->pluck('ref_cod_escola');

        $nomesEscola = [];
        foreach ($escolasUsuario as $codEscola) {
            $escolaDetalhe = new clsPmieducarEscola(cod_escola: $codEscola);
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

        $objPermissao = new clsPermissoes;
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
