<?php

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

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Instituição';
        }

        $this->addCabecalhos($lista_busca);

        include 'include/pmieducar/educar_campo_lista.php';

        // outros Filtros
        $this->campoTexto(nome: 'nm_curso', campo: 'Curso', valor: $this->nm_curso, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: false);

        // outros de Foreign Keys
        $opcoes = ['' => 'Selecione'];

        $todos_niveis_ensino = "nivel_ensino = new Array();\n";
        $lista = LegacyEducationLevel::query()
            ->select(['cod_nivel_ensino', 'nm_nivel', 'ref_cod_instituicao'])
            ->where(column: 'ativo', operator: 1)
            ->orderBy(column: 'nm_nivel', direction: 'ASC')
            ->get();

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $todos_niveis_ensino .= "nivel_ensino[nivel_ensino.length] = new Array({$registro['cod_nivel_ensino']},'{$registro['nm_nivel']}', {$registro['ref_cod_instituicao']});\n";
            }
        }
        echo "<script>{$todos_niveis_ensino}</script>";

        if ($this->ref_cod_instituicao) {
            $opcoes = LegacyEducationLevel::query()
                ->where(column: 'ativo', operator: 1)
                ->orderBy(column: 'nm_nivel', direction: 'ASC')
                ->pluck(column: 'nm_nivel', key: 'cod_nivel_ensino');
        }

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

        $opcoes = ['' => 'Selecione'];

        $todos_tipos_ensino = "tipo_ensino = new Array();\n";

        $query = LegacyEducationType::query()
            ->where(column: 'ativo', operator: 1)
            ->limit($this->limite)
            ->offset($this->offset)
            ->orderBy(column: 'nm_tipo', direction: 'ASC');
        $lista = $query->get();

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $todos_tipos_ensino .= "tipo_ensino[tipo_ensino.length] = new Array({$registro['cod_tipo_ensino']},'{$registro['nm_tipo']}', {$registro['ref_cod_instituicao']});\n";
            }
        }
        echo "<script>{$todos_tipos_ensino}</script>";

        if ($this->ref_cod_instituicao) {
            $opcoes = LegacyEducationType::query()
                ->where(column: 'ativo', operator: 1)
                ->orderBy(column: 'nm_tipo', direction: 'ASC')
                ->pluck(column: 'nm_tipo', key: 'cod_tipo_ensino')
                ->prepend(value: 'Selecione', key: '');
        }

        $this->campoLista(
            nome: 'ref_cod_tipo_ensino',
            campo: 'Tipo Ensino',
            valor: $opcoes,
            default: $this->ref_cod_tipo_ensino,
            acao: '',
            duplo: false,
            descricao: '',
            complemento: '',
            desabilitado: '',
            obrigatorio: false
        );

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ?
            $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $obj_curso = new clsPmieducarCurso();
        $obj_curso->setOrderby('nm_curso ASC');
        $obj_curso->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $obj_curso->lista(
            int_cod_curso: null,
            int_ref_usuario_cad: null,
            int_ref_cod_tipo_regime: null,
            int_ref_cod_nivel_ensino: $this->ref_cod_nivel_ensino,
            int_ref_cod_tipo_ensino: $this->ref_cod_tipo_ensino,
            int_ref_cod_tipo_avaliacao: null,
            str_nm_curso: $this->nm_curso,
            str_sgl_curso: null,
            int_qtd_etapas: null,
            int_frequencia_minima: null,
            int_media: null,
            int_media_exame: null,
            int_falta_ch_globalizada: null,
            int_carga_horaria: null,
            str_ato_poder_publico: null,
            int_edicao_final: null,
            str_objetivo_curso: null,
            str_publico_alvo: null,
            date_data_cadastro_ini: null,
            date_data_cadastro_fim: null,
            date_data_exclusao_ini: null,
            date_data_exclusao_fim: null,
            int_ativo: 1,
            int_ref_usuario_exc: null,
            int_ref_cod_instituicao: $this->ref_cod_instituicao
        );

        $total = $obj_curso->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $det_ref_cod_nivel_ensino = LegacyEducationLevel::findOrFail($registro['ref_cod_nivel_ensino'])?->getAttributes();
                $registro['ref_cod_nivel_ensino'] = $det_ref_cod_nivel_ensino['nm_nivel'];

                $det_ref_cod_tipo_ensino = LegacyEducationType::find($registro['ref_cod_tipo_ensino'])?->getAttributes();
                $registro['ref_cod_tipo_ensino'] = $det_ref_cod_tipo_ensino['nm_tipo'];

                $obj_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

                $nomeCurso = empty($registro['descricao']) ? $registro['nm_curso'] : "{$registro['nm_curso']} ({$registro['descricao']})";

                $lista_busca = [
                    "<a href=\"educar_curso_det.php?cod_curso={$registro['cod_curso']}\">{$nomeCurso}</a>",
                    "<a href=\"educar_curso_det.php?cod_curso={$registro['cod_curso']}\">{$registro['ref_cod_nivel_ensino']}</a>",
                    "<a href=\"educar_curso_det.php?cod_curso={$registro['cod_curso']}\">{$registro['ref_cod_tipo_ensino']}</a>",
                ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_curso_det.php?cod_curso={$registro['cod_curso']}\">{$registro['ref_cod_instituicao']}</a>";
                }

                $this->addLinhas($lista_busca);
            }
        }

        $this->addPaginador2(strUrl: 'educar_curso_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        $obj_permissoes = new clsPermissoes();
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
