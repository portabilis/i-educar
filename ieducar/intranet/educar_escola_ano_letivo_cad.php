<?php

use App\Models\LegacySchoolAcademicYear;

return new class() extends clsCadastro
{
    public $pessoa_logada;

    public $ref_cod_escola;

    public $ano;

    public $ref_usuario_cad;

    public $ref_usuario_exc;

    public $andamento;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ano = $_GET['ano'];
        $this->ref_cod_escola = $_GET['cod_escola'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 561,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_escola_lst.php'
        );

        $this->nome_url_sucesso = 'Continuar';
        $this->url_cancelar = 'educar_escola_det.php?cod_escola=' . $this->ref_cod_escola;

        $this->breadcrumb(currentPage: 'Definição do ano letivo', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // Primary keys
        $this->campoOculto(nome: 'ref_cod_escola', valor: $this->ref_cod_escola);
        $this->campoOculto(nome: 'ano', valor: $this->ano);

        $ano_array = collect();
        if (is_numeric($this->ref_cod_escola)) {
            $ano_array = LegacySchoolAcademicYear::query()->where('andamento', LegacySchoolAcademicYear::FINALIZED)->whereSchool($this->ref_cod_escola)->active()->pluck('ano', 'ano');
        }

        $ano_atual = date('Y') - 5;

        // Foreign keys
        $opcoes = ['' => 'Selecione'];
        $lim = 10;

        for ($i = 0; $i < $lim; $i++) {
            $ano = $ano_atual + $i;

            if (!$ano_array->contains($ano)) {
                $opcoes[$ano] = $ano;
            } else {
                $lim++;
            }
        }

        $this->campoLista(nome: 'ano', campo: 'Ano', valor: $opcoes, default: $this->ano);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 561,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_escola_lst.php'
        );

        $url = sprintf(
            'educar_ano_letivo_modulo_cad.php?ref_cod_escola=%s&ano=%s',
            $this->ref_cod_escola,
            $this->ano
        );

        $this->simpleRedirect($url);
    }

    public function Formular()
    {
        $this->title = 'Escola Ano Letivo';
        $this->processoAp = 561;
    }
};
