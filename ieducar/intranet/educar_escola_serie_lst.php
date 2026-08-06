<?php

use App\Models\LegacyGrade;
use App\Models\LegacySchoolGrade;

return new class extends clsListagem
{
    public $limite;

    public $ref_cod_serie;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $hora_inicial;

    public $hora_final;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $hora_inicio_intervalo;

    public $hora_fim_intervalo;

    public $ref_cod_curso;

    public $ref_ref_cod_serie;

    public function Gerar()
    {
        $this->titulo = 'Escola Série - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = ['Série', 'Curso'];
        $lista_busca[] = 'Escola';
        $lista_busca[] = 'Instituição';
        $lista_busca[] = 'Escola';
        $this->addCabecalhos($lista_busca);

        $obrigatorio = false;
        $get_escola = true;
        $get_curso = true;
        $get_serie = false;
        $get_escola_serie = true;
        $get_select_name_full = true;

        include 'include/pmieducar/educar_campo_lista.php';

        if ($this->ref_cod_escola_) {
            $this->ref_cod_escola = $this->ref_cod_escola_;
        }

        if ($this->ref_cod_serie_) {
            $this->ref_cod_serie = $this->ref_cod_serie_;
        }

        $opcoes_serie = ['' => 'Selecione uma série'];

        // Editar
        if ($this->ref_cod_curso) {
            $series = LegacyGrade::where('ativo', 1)->where('ref_cod_curso', $this->ref_cod_curso)->orderBy('nm_serie')->get(['nm_serie', 'cod_serie']);

            foreach ($series as $serie) {
                $opcoes_serie[$serie['cod_serie']] = $serie['nm_serie'];
            }
        }

        $this->campoLista(
            nome: 'ref_cod_serie',
            campo: 'Série',
            valor: $opcoes_serie,
            default: $this->ref_cod_serie,
            obrigatorio: false
        );

        $this->limite = 20;

        $usuarioBiblioteca = App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada);

        $paginador = LegacySchoolGrade::query()
            ->joinGradeCourse()
            ->active()
            ->when(is_numeric($this->ref_cod_escola), fn ($q) => $q->whereSchool($this->ref_cod_escola))
            ->when(!is_numeric($this->ref_cod_escola) && $usuarioBiblioteca, fn ($q) => $q->whereUser($this->pessoa_logada))
            ->when(is_numeric($this->ref_cod_serie), fn ($q) => $q->whereGrade($this->ref_cod_serie))
            ->when(is_numeric($this->ref_cod_instituicao), fn ($q) => $q->whereInstitution($this->ref_cod_instituicao))
            ->when(is_numeric($this->ref_cod_curso), fn ($q) => $q->whereCourse($this->ref_cod_curso))
            ->with(['grade.course', 'school.organization', 'school.institution'])
            ->orderBy('nm_serie')
            ->orderBy('escola_serie.ref_cod_serie')
            ->paginate(perPage: $this->limite, pageName: 'pagina_' . $this->nome);

        $total = $paginador->total();

        foreach ($paginador->getCollection() as $registro) {
            $serie = $registro->grade;
            $nm_serie = empty($serie->descricao) ? $serie->nm_serie : "{$serie->nm_serie} ({$serie->descricao})";

            $curso = $serie->course;
            $nm_curso = empty($curso->descricao) ? $curso->nm_curso : "{$curso->nm_curso} ({$curso->descricao})";

            $nm_escola = $registro->school->organization->fantasia;
            $nm_instituicao = $registro->school->institution->nm_instituicao;

            $link = "educar_escola_serie_det.php?ref_cod_escola={$registro['ref_cod_escola']}&ref_cod_serie={$registro['ref_cod_serie']}";

            $this->addLinhas([
                "<a href=\"{$link}\">{$nm_serie}</a>",
                "<a href=\"{$link}\">{$nm_curso}</a>",
                "<a href=\"{$link}\">{$nm_escola}</a>",
                "<a href=\"{$link}\">{$nm_instituicao}</a>",
                "<a href=\"{$link}\">{$nm_escola}</a>",
            ]);
        }

        $this->addPaginador2(
            strUrl: 'educar_escola_serie_lst.php',
            intTotalRegistros: $total,
            mixVariaveisMantidas: $_GET,
            nome: $this->nome,
            intResultadosPorPagina: $this->limite
        );

        $obj_permissao = new clsPermissoes;
        if ($obj_permissao->permissao_cadastra(int_processo_ap: 585, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->acao = 'go("educar_escola_serie_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Séries da escola', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function makeExtra()
    {
        return file_get_contents(public_path('/vendor/legacy/Cadastro/Assets/Javascripts/EscolaSerie.js'));
    }

    public function Formular()
    {
        $this->title = 'Séries da escola';

        $this->processoAp = 585;
    }
};
