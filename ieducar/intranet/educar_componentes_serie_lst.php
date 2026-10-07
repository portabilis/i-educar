<?php

use App\Models\LegacyGrade;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $cod_serie;

    public $ref_cod_curso;

    public $nm_serie;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Componentes da série';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = [
            'Série',
            'Curso',
        ];

        $obj_permissoes = new clsPermissoes;
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Instituição';
        }

        $this->addCabecalhos($lista_busca);

        $this->inputsHelper()->dynamic(helperNames: ['instituicao', 'curso', 'serie'], inputOptions: ['required' => false]);

        // Paginador
        $this->limite = 10;
        $resultado = LegacyGrade::query()
            ->active()
            ->with([
                'course:cod_curso,nm_curso,descricao,ref_cod_instituicao',
                'course.institution:cod_instituicao,nm_instituicao',
            ])
            ->whereHas('academicYearDisciplines')
            ->when(is_numeric($this->ref_cod_serie), fn ($q) => $q->whereKey($this->ref_cod_serie))
            ->when(is_numeric($this->ref_cod_curso), fn ($q) => $q->whereCourse($this->ref_cod_curso))
            ->when(is_numeric($this->ref_cod_instituicao), fn ($q) => $q->whereInstitution($this->ref_cod_instituicao))
            ->orderBy('nm_serie')
            ->paginate(perPage: $this->limite, pageName: 'pagina_' . $this->nome);

        $lista = $resultado->getCollection();
        $total = $resultado->total();

        // monta a lista
        foreach ($lista as $registro) {
            $curso = $registro->course;
            $registro['nm_serie'] = empty($registro['descricao']) ? $registro['nm_serie'] : "{$registro['nm_serie']} ({$registro['descricao']})";
            $registro['ref_cod_instituicao'] = $curso->institution->nm_instituicao;
            $registro['ref_cod_curso'] = empty($curso->descricao) ? $curso->nm_curso : "{$curso->nm_curso} ({$curso->descricao})";

            $lista_busca = [
                "<a href=\"educar_componentes_serie_cad.php?serie_id={$registro['cod_serie']}\">{$registro['nm_serie']}</a>",
                "<a href=\"educar_componentes_serie_cad.php?serie_id={$registro['cod_serie']}\">{$registro['ref_cod_curso']}</a>",
            ];

            if ($nivel_usuario == 1) {
                $lista_busca[] = "<a href=\"educar_componentes_serie_cad.php?serie_id={$registro['cod_serie']}\">{$registro['ref_cod_instituicao']}</a>";
            }

            $this->addLinhas($lista_busca);
        }

        $this->addPaginador2(strUrl: 'educar_componentes_serie_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 9998859, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->acao = 'go("educar_componentes_serie_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Componentes da série', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $scripts = ['/vendor/legacy/Cadastro/Assets/Javascripts/ComponentesSerieFiltros.js'];

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: $scripts);
    }

    public function Formular()
    {
        $this->title = 'Componentes da série';
        $this->processoAp = '9998859';
    }
};
