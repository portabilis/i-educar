<?php

use App\Models\LegacyCourse;
use App\Models\LegacyEducationLevel;
use App\Models\LegacyEducationType;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $offset;

    public $cod_curso;

    public $ref_usuario_cad;

    public $ref_cod_tipo_regime;

    public $ref_cod_nivel_ensino;

    public $ref_cod_tipo_ensino;

    public $nm_curso;

    public $sgl_curso;

    public $qtd_etapas;

    public $carga_horaria;

    public $ato_poder_publico;

    public $habilitacao;

    public $objetivo_curso;

    public $publico_alvo;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_usuario_exc;

    public $ref_cod_instituicao;

    public $padrao_ano_escolar;

    public function Gerar()
    {
        $this->titulo = 'Curso - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = [
            'Curso',
            'Nível Ensino',
            'Tipo Ensino',
        ];

        $this->addCabecalhos($lista_busca);
        // outros Filtros
        $this->campoTexto(nome: 'nm_curso', campo: 'Curso', valor: $this->nm_curso, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: false);

        $opcoes = LegacyEducationLevel::query()
            ->where(column: 'ativo', operator: 1)
            ->orderBy(column: 'nm_nivel', direction: 'ASC')
            ->pluck(column: 'nm_nivel', key: 'cod_nivel_ensino')
            ->prepend(value: 'Selecione', key: '');

        $this->campoLista(
            nome: 'ref_cod_nivel_ensino',
            campo: 'Nível Ensino',
            valor: $opcoes,
            default: $this->ref_cod_nivel_ensino,
            acao: null,
            duplo: null,
            descricao: null,
            complemento: null,
            desabilitado: null,
            obrigatorio: false
        );

        $query = LegacyEducationType::query()
            ->where(column: 'ativo', operator: 1)
            ->limit($this->limite)
            ->offset($this->offset)
            ->orderBy(column: 'nm_tipo', direction: 'ASC');
        $lista = $query->get();

        $opcoes = LegacyEducationType::query()
            ->where(column: 'ativo', operator: 1)
            ->orderBy(column: 'nm_tipo', direction: 'ASC')
            ->pluck(column: 'nm_tipo', key: 'cod_tipo_ensino')
            ->prepend(value: 'Selecione', key: '');

        $this->campoLista(
            nome: 'ref_cod_tipo_ensino',
            campo: 'Tipo Ensino',
            valor: $opcoes,
            default: $this->ref_cod_tipo_ensino,
            desabilitado: '',
            obrigatorio: false
        );

        // Paginador
        $this->limite = 20;

        $result = LegacyCourse::query()
            ->select([
                'cod_curso',
                'nm_curso',
                'nm_nivel',
                'nm_tipo',
            ])
            ->join('pmieducar.tipo_ensino', 'tipo_ensino.cod_tipo_ensino', 'curso.ref_cod_tipo_ensino')
            ->join('pmieducar.nivel_ensino', 'nivel_ensino.cod_nivel_ensino', 'curso.ref_cod_nivel_ensino')
            ->when(request('ref_cod_nivel_ensino'), function ($query) {
                return $query->where('curso.ref_cod_nivel_ensino', request('ref_cod_nivel_ensino'));
            })
            ->when(request('ref_cod_tipo_ensino'), function ($query) {
                return $query->where('curso.ref_cod_tipo_ensino', request('ref_cod_tipo_ensino'));
            })
            ->when(request('nm_curso'), function ($query) {
                return $query->where('nm_curso', 'ILIKE', '%' . request('nm_curso') . '%');
            })
            ->orderBy('nm_curso')
            ->paginate(
                perPage: $this->limite,
                pageName: 'pagina_' . $this->nome
            );

        $total = $result->total();
        $cursos = $result->items();

        // monta a lista
        foreach ($cursos as $curso) {
            $lista_busca = [
                '<a href="educar_curso_det.php?cod_curso=' . $curso->getKey() . '">' . $curso->nm_curso . '</a>',
                '<a href="educar_curso_det.php?cod_curso=' . $curso->getKey() . '">' . $curso->nm_nivel . '</a>',
                '<a href="educar_curso_det.php?cod_curso=' . $curso->getKey() . '">' . $curso->nm_tipo . '</a>',
            ];

            $this->addLinhas($lista_busca);
        }

        $this->addPaginador2(
            strUrl: 'educar_curso_lst.php',
            intTotalRegistros: $total,
            mixVariaveisMantidas: $_GET,
            nome: $this->nome,
            intResultadosPorPagina: $this->limite
        );

        $obj_permissoes = new clsPermissoes;
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 566, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->acao = 'go("educar_curso_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de cursos', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-curso-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Curso';
        $this->processoAp = '566';
    }
};
