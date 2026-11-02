<?php

use App\Models\LegacyGrade;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $cod_serie;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_cod_curso;

    public $nm_serie;

    public $etapa_curso;

    public $concluinte;

    public $carga_horaria;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $intervalo;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Série - Listagem';

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

        $get_curso = true;
        include 'include/pmieducar/educar_campo_lista.php';

        // outros Filtros
        $this->campoTexto('nm_serie', 'Série', $this->nm_serie, 30, 255);

        // Paginador
        $this->limite = 20;
        $resultado = LegacyGrade::query()
            ->active()
            ->with([
                'course:cod_curso,nm_curso,descricao,ref_cod_instituicao',
                'course.institution:cod_instituicao,nm_instituicao',
            ])
            ->when(is_numeric($this->ref_cod_curso), fn ($q) => $q->whereCourse($this->ref_cod_curso))
            ->when(is_string($this->nm_serie), fn ($q) => $q->whereNameLike($this->nm_serie))
            ->when(is_numeric($this->ref_cod_instituicao), fn ($q) => $q->whereInstitution($this->ref_cod_instituicao))
            ->orderBy('nm_serie')
            ->paginate(perPage: $this->limite, pageName: 'pagina_' . $this->nome);

        $lista = $resultado->getCollection();
        $total = $resultado->total();

        // monta a lista
        foreach ($lista as $registro) {
            $curso = $registro->course;
            $nomeCurso = empty($curso->descricao) ? $curso->nm_curso : "{$curso->nm_curso} ({$curso->descricao})";
            $registro['ref_cod_instituicao'] = $curso->institution->nm_instituicao;
            $registro['ref_cod_curso'] = $nomeCurso;
            $nomeSerie = empty($registro['descricao']) ? $registro['nm_serie'] : "{$registro['nm_serie']} ({$registro['descricao']})";

            $lista_busca = [
                "<a href=\"educar_serie_det.php?cod_serie={$registro['cod_serie']}\">{$nomeSerie}</a>",
                "<a href=\"educar_serie_det.php?cod_serie={$registro['cod_serie']}\">{$registro['ref_cod_curso']}</a>",
            ];

            if ($nivel_usuario == 1) {
                $lista_busca[] = "<a href=\"educar_serie_det.php?cod_serie={$registro['cod_serie']}\">{$registro['ref_cod_instituicao']}</a>";
            }

            $this->addLinhas($lista_busca);
        }

        $this->addPaginador2('educar_serie_lst.php', $total, $_GET, $this->nome, $this->limite);

        if ($obj_permissoes->permissao_cadastra(583, $this->pessoa_logada, 3)) {
            $this->acao = 'go("educar_serie_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de séries', [url('intranet/educar_index.php') => 'Escola']);
    }

    public function Formular()
    {
        $this->title = 'Série';
        $this->processoAp = '583';
    }
};
