<?php

use iEducar\Support\View\SelectOptions;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $offset;

    public $id;

    public $ano;

    public $servidor_id;

    public $funcao_exercida;

    public $tipo_vinculo;

    public $ref_cod_instituicao;

    public $ref_cod_escola;

    public $ref_cod_curso;

    public $ref_cod_serie;

    public $ref_cod_turma;

    public function Gerar()
    {
        $this->servidor_id = $_GET['ref_cod_servidor'];
        $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];

        $this->titulo = 'Servidor Vínculo Turma - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos(coluna: [
            'Ano',
            'Escola',
            'Curso',
            'Série',
            'Turma',
            'Função exercida',
            'Tipo de vínculo',
        ]);

        $this->campoOculto(nome: 'ref_cod_servidor', valor: $this->servidor_id);

        $this->inputsHelper()->dynamic(helperNames: ['ano', 'instituicao', 'escola', 'curso', 'serie', 'turma'], inputOptions: ['required' => false]);

        $resources_funcao = SelectOptions::funcoesExercidaServidor();
        $options = ['label' => 'Função exercida', 'resources' => $resources_funcao, 'value' => $this->funcao_exercida];
        $this->inputsHelper()->select(attrName: 'funcao_exercida', inputOptions: $options);

        $resources_tipo = SelectOptions::tiposVinculoServidor();
        $options = ['label' => 'Tipo do vínculo', 'resources' => $resources_tipo, 'value' => $this->tipo_vinculo];
        $this->inputsHelper()->select(attrName: 'tipo_vinculo', inputOptions: $options);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET['pagina_' . $this->nome]) ?
        $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

        $codUsuario = App_Model_IedFinder::usuarioNivelBibliotecaEscolar(codUsuario: $this->pessoa_logada)
            ? $this->pessoa_logada
            : null;

        $result = $this->listarVinculos(
            servidorId: $this->servidor_id,
            instituicaoId: $this->ref_cod_instituicao,
            ano: $this->ano,
            escolaId: $this->ref_cod_escola,
            cursoId: $this->ref_cod_curso,
            serieId: $this->ref_cod_serie,
            turmaId: $this->ref_cod_turma,
            funcaoExercida: $this->funcao_exercida,
            tipoVinculo: $this->tipo_vinculo,
            codUsuario: $codUsuario,
            limite: $this->limite,
            offset: $this->offset
        );

        $lista = $result['lista'];
        $total = $result['total'];

        // UrlHelper
        $url = CoreExt_View_Helper_UrlHelper::getInstance();
        $path = 'educar_servidor_vinculo_turma_det.php';

        // Monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $options = [
                    'query' => [
                        'id' => $registro['id'],
                    ]];

                $this->addLinhas(linha: [
                    $url->l(text: $registro['ano'], path: $path, options: $options),
                    $url->l(text: $registro['nm_escola'], path: $path, options: $options),
                    $url->l(text: $registro['nm_curso'], path: $path, options: $options),
                    $url->l(text: $registro['nm_serie'], path: $path, options: $options),
                    $url->l(text: $registro['nm_turma'], path: $path, options: $options),
                    $url->l(text: $resources_funcao[$registro['funcao_exercida']], path: $path, options: $options),
                    $url->l(text: $resources_tipo[$registro['tipo_vinculo']], path: $path, options: $options),
                ]);
            }
        }

        $this->addPaginador2(strUrl: 'educar_servidor_vinculo_turma_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);
        $obj_permissoes = new clsPermissoes;

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->array_botao[] = [
                'name' => 'Novo',
                'css-extra' => 'btn-green',
            ];
            $this->array_botao_url[] = sprintf(
                'educar_servidor_vinculo_turma_cad.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
                $this->servidor_id,
                $this->ref_cod_instituicao
            );
        }

        $this->array_botao[] = 'Voltar';
        $this->array_botao_url[] = sprintf(
            'educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d',
            $this->servidor_id,
            $this->ref_cod_instituicao
        );

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Registro de vínculos do professor', breadcrumbs: [
            url(path: 'intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores - Servidor Vínculo Turma';
        $this->processoAp = 635;
    }

    private function listarVinculos(
        $servidorId = null,
        $instituicaoId = null,
        $ano = null,
        $escolaId = null,
        $cursoId = null,
        $serieId = null,
        $turmaId = null,
        $funcaoExercida = null,
        $tipoVinculo = null,
        $codUsuario = null,
        $limite = 20,
        $offset = 0
    ) {
        $filtros = '
            JOIN pmieducar.turma t ON pt.turma_id = t.cod_turma
            LEFT JOIN pmieducar.turma_serie ts ON ts.turma_id = t.cod_turma
            JOIN pmieducar.serie s ON s.cod_serie = coalesce(ts.serie_id, t.ref_ref_cod_serie)
            JOIN pmieducar.curso c ON s.ref_cod_curso = c.cod_curso
            JOIN pmieducar.escola e ON t.ref_ref_cod_escola = e.cod_escola
            JOIN cadastro.pessoa p ON e.ref_idpes = p.idpes
        WHERE true';
        $params = [];

        if (is_numeric($servidorId)) {
            $filtros .= ' AND pt.servidor_id = ?';
            $params[] = $servidorId;
        }
        if (is_numeric($instituicaoId)) {
            $filtros .= ' AND pt.instituicao_id = ?';
            $params[] = $instituicaoId;
        }
        if (is_numeric($ano)) {
            $filtros .= ' AND pt.ano = ?';
            $params[] = $ano;
        }
        if (is_numeric($escolaId)) {
            $filtros .= ' AND t.ref_ref_cod_escola = ?';
            $params[] = $escolaId;
        } elseif ($codUsuario) {
            $filtros .= ' AND EXISTS (SELECT 1 FROM pmieducar.escola_usuario WHERE escola_usuario.ref_cod_escola = t.ref_ref_cod_escola AND escola_usuario.ref_cod_usuario = ?)';
            $params[] = $codUsuario;
        }
        if (is_numeric($cursoId)) {
            $filtros .= ' AND t.ref_cod_curso = ?';
            $params[] = $cursoId;
        }
        if (is_numeric($serieId)) {
            $filtros .= ' AND t.ref_ref_cod_serie = ?';
            $params[] = $serieId;
        }
        if (is_numeric($turmaId)) {
            $filtros .= ' AND t.cod_turma = ?';
            $params[] = $turmaId;
        }
        if (is_numeric($funcaoExercida)) {
            $filtros .= ' AND pt.funcao_exercida = ?';
            $params[] = $funcaoExercida;
        }
        if (is_numeric($tipoVinculo)) {
            $filtros .= ' AND pt.tipo_vinculo = ?';
            $params[] = $tipoVinculo;
        }

        $total = DB::selectOne("SELECT COUNT(0) as total FROM modules.professor_turma pt {$filtros}", $params)->total ?? 0;

        $sql = "SELECT pt.id, pt.ano, pt.instituicao_id, pt.servidor_id, pt.turma_id, pt.funcao_exercida, pt.tipo_vinculo, pt.permite_lancar_faltas_componente, pt.turno_id, pt.data_inicial, pt.data_fim, pt.leciona_itinerario_tecnico_profissional, pt.area_itinerario,
            t.nm_turma, t.cod_turma as ref_cod_turma, t.ref_ref_cod_serie as ref_cod_serie,
            textcat_all(s.nm_serie) AS nm_serie, t.ref_cod_curso, textcat_all(DISTINCT c.nm_curso) AS nm_curso,
            t.ref_ref_cod_escola as ref_cod_escola, p.nome as nm_escola
            FROM modules.professor_turma pt
            {$filtros}
            GROUP BY pt.id, t.cod_turma, p.nome
            ORDER BY nm_escola, nm_curso, nm_serie, nm_turma ASC
            LIMIT ? OFFSET ?";

        $params[] = $limite;
        $params[] = $offset;

        $registros = DB::select($sql, $params);

        if (empty($registros)) {
            return ['lista' => false, 'total' => 0];
        }

        $lista = array_map(fn ($r) => array_merge((array) $r, ['_total' => $total]), $registros);

        return ['lista' => $lista, 'total' => $total];
    }
};
