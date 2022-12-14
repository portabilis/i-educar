<?php

use App\Models\LegacySchoolClassGrade;
use App\Services\iDiarioService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

return new class extends clsCadastro {
    public $pessoa_logada;

    public $ref_cod_instituicao;

    public $ref_ano;

    public $ref_ref_cod_escola;

    public $sequencial;

    public $ref_cod_modulo;

    public $data_inicio;

    public $data_fim;

    public $ano_letivo_modulo;

    public $modulos = [];

    public $etapas = [];

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_modulo = $_GET['ref_cod_modulo'];
        $this->ref_ref_cod_escola = $_GET['ref_cod_escola'];
        $this->ref_ano = $_GET['ano'];

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 561,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_escola_lst.php'
        );

        if (is_numeric($this->ref_ano) && is_numeric($this->ref_ref_cod_escola)) {
            $obj = new clsPmieducarEscolaAnoLetivo(ref_cod_escola: $this->ref_ref_cod_escola, ano: $this->ref_ano);
            $registro = $obj->detalhe();

            if ($registro) {
                if ($obj_permissoes->permissao_excluir(int_processo_ap: 561, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';

                $etapasObj = new clsPmieducarAnoLetivoModulo();
                $etapasObj->setOrderBy('sequencial ASC');
                $this->etapas = $etapasObj->lista(int_ref_ano: $this->ref_ano, int_ref_ref_cod_escola: $this->ref_ref_cod_escola);
                $this->ref_cod_modulo = $this->etapas[0]['ref_cod_modulo'];
            }
        }

        $this->url_cancelar = $_GET['referrer']
            ? $_GET['referrer'] . '?cod_escola=' . $this->ref_ref_cod_escola
            : 'educar_escola_lst.php';

        $this->breadcrumb(currentPage: 'Etapas do ano letivo', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = $this->$campo ? $this->$campo : $val;
            }
        }

        // Primary keys
        $this->campoOculto(nome: 'ref_ano', valor: $this->ref_ano);
        $this->campoOculto(nome: 'ref_ref_cod_escola', valor: $this->ref_ref_cod_escola);

        $obj_escola = new clsPmieducarEscola($this->ref_ref_cod_escola);
        $det_escola = $obj_escola->detalhe();
        $ref_cod_instituicao = $det_escola['ref_cod_instituicao'];
        $this->ref_cod_instituicao = $ref_cod_instituicao;

        $obj = new clsPmieducarAnoLetivoModulo();
        $obj->setOrderBy('sequencial ASC');
        $registros = $obj->lista(int_ref_ano: $this->ref_ano - 1, int_ref_ref_cod_escola: $this->ref_ref_cod_escola);
        $cont = 0;

        if ($registros) {
            $cor = '';
            $tabela = '<table border=0 style=\'\' cellpadding=2 width=\'100%\'>';
            $tabela .= "<tr bgcolor=$cor><td colspan='2'>Etapas do ano anterior (".($this->ref_ano - 1).')</td></tr><tr><td>';
            $tabela .= '<table cellpadding="2" cellspacing="2" border="0" align="left" width=\'300px\'>';
            $tabela .= '<tr bgcolor=\'#ccdce6\'><th width=\'100px\'>Etapa<a name=\'ano_letivo\'/></th><th width=\'200px\'>Período</th></tr>';

            $existeBissexto = false;

            foreach ($registros as $campo) {
                $cor = '#f5f9fd';
                $cont++;
                $tabela .= "<tr bgcolor='$cor'><td align='center'>{$cont}</td><td align='center'>".dataFromPgToBr($campo['data_inicio']).' à '.dataFromPgToBr($campo['data_fim']).'</td></tr>';

                $ano = date_parse_from_format(format: 'Y-m-d', datetime: $campo['data_inicio']);
                $ano = $ano['year'];

                $novaDataInicio = str_replace(search: $ano, replace: $this->ref_ano, subject: $campo['data_inicio']);
                $novaDataFim = str_replace(search: $ano, replace: $this->ref_ano, subject: $campo['data_fim']);

                if (
                    Portabilis_Date_Utils::checkDateBissexto($novaDataInicio)
                    || Portabilis_Date_Utils::checkDateBissexto($novaDataFim)
                ) {
                    $existeBissexto = true;
                }
            }

            if ($existeBissexto) {
                $tabela .= "<tr bgcolor='#FCF8E3' style='color: #8A6D3B; font-weight:normal;'>
                    <td align='center'><b>Observação:</b></td>
                    <td align='center'>A data 29/02/$this->ref_ano não poderá ser migrada pois $this->ref_ano não é um ano bissexto, portanto será substituída por 28/02/$this->ref_ano.</td>
                    </tr>";
            }

            $tabela .='</table>';
            $tabela .= "<tr><td colspan='2'><b> Adicione as etapas abaixo para {$this->ref_ano} semelhante ao exemplo do ano anterior: </b></td></tr><tr><td>";
            $tabela .= '</table>';
        }

        $ref_ano_ = $this->ref_ano;

        $this->campoTexto(
            nome: 'ref_ano_',
            campo: 'Ano',
            valor: $ref_ano_,
            tamanhovisivel: 4,
            tamanhomaximo: 4,
            evento: '',
            disabled: true
        );

        $opcoesCampoModulo = [];

        $objTemp = new clsPmieducarModulo();
        $objTemp->setOrderby('nm_tipo ASC');

        $lista = $objTemp->lista(
            int_ativo: 1,
            int_ref_cod_instituicao: $ref_cod_instituicao
        );

        if (is_array($lista) && count($lista)) {
            $this->modulos = $lista;

            foreach ($lista as $registro) {
                $opcoesCampoModulo[$registro['cod_modulo']] = sprintf('%s - %d etapa(s)', $registro['nm_tipo'], $registro['num_etapas']);
            }
        }

        $this->campoLista(
            nome: 'ref_cod_modulo',
            campo: 'Etapa',
            valor: $opcoesCampoModulo,
            default: \Request::get('ref_cod_modulo',$this->ref_cod_modulo),
            acao: null,
            duplo: null,
            descricao: null,
            complemento: null,
            desabilitado: null,
        );

        if ($tabela) {
            $this->campoQuebra();
            $this->campoRotulo(nome: 'modulosAnoAnterior', campo: '-', valor: $tabela);
        }

        $this->campoQuebra();

        if (is_numeric($this->ref_ano) && is_numeric($this->ref_ref_cod_escola) && !$_POST) {
            $qtd_registros = 0;


            if (Request::has('data_inicio')) {
                foreach (Request::get('data_inicio') as $key => $campo) {
                    $this->ano_letivo_modulo[$qtd_registros][] = \Request::get('data_inicio')[$key] ?? null;
                    $this->ano_letivo_modulo[$qtd_registros][] = \Request::get('data_fim')[$key] ?? null;
                    $this->ano_letivo_modulo[$qtd_registros][] = \Request::get('dias_letivos')[$key] ?? null;
                    $qtd_registros++;
                }
            } else {
                foreach ($this->etapas as $campo) {
                    $this->ano_letivo_modulo[$qtd_registros][] = dataFromPgToBr($campo['data_inicio']);
                    $this->ano_letivo_modulo[$qtd_registros][] = dataFromPgToBr($campo['data_fim']);
                    $this->ano_letivo_modulo[$qtd_registros][] = $campo['dias_letivos'];
                    $qtd_registros++;
                }
            }

            $this->campoTabelaInicio(
                nome: 'modulos_ano_letivo',
                titulo: 'Etapas do ano letivo',
                arr_campos: ['Data inicial', 'Data final', 'Dias Letivos'],
                arr_valores: $this->ano_letivo_modulo
            );

            $this->campoData(nome: 'data_inicio', campo: 'Hora', valor: $this->data_inicio, obrigatorio: true);
            $this->campoData(nome: 'data_fim', campo: 'Hora', valor: $this->data_fim, obrigatorio: true);
            $this->campoNumero(nome: 'dias_letivos', campo: 'Dias Letivos', valor: $this->dias_letivos, tamanhovisivel: 6, tamanhomaximo: 3, obrigatorio: false);

            $this->campoTabelaFim();
        }

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: [
            '/vendor/legacy/Portabilis/Assets/Javascripts/Validator.js',
            '/intranet/scripts/etapas.js'
        ]);
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

        try {
            $this->validaDates();
        } catch (Exception $e) {
            $_POST = [];
            $this->Inicializar();
            $this->mensagem = $e->getMessage();
            return false;
        }

        if ($this->ref_cod_modulo && $this->data_inicio && $this->data_fim) {
            $this->copiarTurmasUltimoAno(escolaId: $this->ref_ref_cod_escola, anoDestino: $this->ref_ano);
            Portabilis_Utils_Database::selectField("SELECT pmieducar.copiaAnosLetivos({$this->ref_ano}::smallint, {$this->ref_ref_cod_escola});");

            $obj = new clsPmieducarEscolaAnoLetivo(
                ref_cod_escola: $this->ref_ref_cod_escola,
                ano: $this->ref_ano,
                ref_usuario_cad: $this->pessoa_logada,
                andamento: 0,
                ativo: 1,
                turmas_por_ano: 1
            );

            $cadastrou = $obj->cadastra();

            if ($cadastrou) {
                foreach ($this->data_inicio as $key => $campo) {
                    $this->data_inicio[$key] = dataToBanco($this->data_inicio[$key]);
                    $this->data_fim[$key] = dataToBanco($this->data_fim[$key]);

                    if ($this->dias_letivos[$key] == '') {
                        $this->dias_letivos[$key] = '0';
                    }

                    $obj = new clsPmieducarAnoLetivoModulo(
                        ref_ano: $this->ref_ano,
                        ref_ref_cod_escola: $this->ref_ref_cod_escola,
                        sequencial: $key + 1,
                        ref_cod_modulo: $this->ref_cod_modulo,
                        data_inicio: $this->data_inicio[$key],
                        data_fim: $this->data_fim[$key],
                        dias_letivos: $this->dias_letivos[$key]
                    );

                    $cadastrou1 = $obj->cadastra();

                    if (!$cadastrou1) {
                        $this->mensagem = 'Cadastro não realizado.<br />';

                        return false;
                    }
                }

                $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';

                $this->simpleRedirect('educar_escola_det.php?cod_escola=' . $this->ref_ref_cod_escola . '#ano_letivo');
            }

            $this->mensagem = 'Cadastro não realizado. <br />';

            return false;
        }

        $this->mensagem = 'Cadastro não realizado.<br />';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 561,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_escola_lst.php'
        );

        try {
            $this->validaDates();
        } catch (Exception $e) {
            $_POST = [];
            $this->Inicializar();
            $this->mensagem = $e->getMessage();
            return false;
        }

        if ($this->ref_cod_modulo && $this->data_inicio && $this->data_fim) {
            try {
                $this->validaModulos();
            } catch (Exception $e) {
                $_POST = [];

                $this->Inicializar();

                $this->mensagem = $e->getMessage();

                return false;
            }

            $obj = new clsPmieducarAnoLetivoModulo(ref_ano: $this->ref_ano, ref_ref_cod_escola: $this->ref_ref_cod_escola);
            $excluiu = $obj->excluirTodos();

            if ($excluiu) {
                foreach ($this->data_inicio as $key => $campo) {
                    $this->data_inicio[$key] = dataToBanco($this->data_inicio[$key]);
                    $this->data_fim[$key] = dataToBanco($this->data_fim[$key]);

                    if ($this->dias_letivos[$key] == '') {
                        $this->dias_letivos[$key] = '0';
                    }

                    $obj = new clsPmieducarAnoLetivoModulo(
                        ref_ano: $this->ref_ano,
                        ref_ref_cod_escola: $this->ref_ref_cod_escola,
                        sequencial: $key + 1,
                        ref_cod_modulo: $this->ref_cod_modulo,
                        data_inicio: $this->data_inicio[$key],
                        data_fim: $this->data_fim[$key],
                        dias_letivos: $this->dias_letivos[$key]
                    );

                    $cadastrou1 = $obj->cadastra();

                    if (!$cadastrou1) {
                        $this->mensagem = 'Edição não realizada.<br />';

                        return false;
                    }
                }

                $this->mensagem .= 'Edição efetuada com sucesso.<br />';
                $this->simpleRedirect('educar_escola_lst.php');
            }
        }

        echo '<script>alert(\'É necessário adicionar pelo menos uma etapa!\')</script>';
        $this->mensagem = 'Edição não realizada.<br />';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_excluir(
            int_processo_ap: 561,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_escola_lst.php'
        );

        $obj = new clsPmieducarEscolaAnoLetivo(
            ref_cod_escola: $this->ref_ref_cod_escola,
            ano: $this->ref_ano,
            ref_usuario_cad: null,
            ref_usuario_exc: $this->pessoa_logada,
            andamento: null,
            data_cadastro: null,
            data_exclusao: null,
            ativo: 0
        );

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $obj = new clsPmieducarAnoLetivoModulo(ref_ano: $this->ref_ano, ref_ref_cod_escola: $this->ref_ref_cod_escola);
            $excluiu1 = $obj->excluirTodos();

            if ($excluiu1) {
                $this->mensagem .= 'Exclusão efetuada com sucesso.<br />';
                $this->simpleRedirect('educar_escola_lst.php');
            }

            $this->mensagem = 'Exclusão não realizada.<br />';

            return false;
        }

        $this->mensagem = 'Exclusão não realizada.<br />';

        return false;
    }

    public function copiarTurmasUltimoAno($escolaId, $anoDestino)
    {
        $sql = 'select ano from pmieducar.escola_ano_letivo where ref_cod_escola = $1 ' .
            'and ativo = 1 and ano in (select max(ano) from pmieducar.escola_ano_letivo where ' .
            'ref_cod_escola = $1 and ativo = 1)';

        $ultimoAnoLetivo = Portabilis_Utils_Database::selectRow(sql: $sql, paramsOrOptions: $escolaId);
        $turmasEscola = new clsPmieducarTurma();
        $turmasEscola = $turmasEscola->lista(
            int_cod_turma: null,
            int_ref_usuario_exc: null,
            int_ref_usuario_cad: null,
            int_ref_ref_cod_serie: null,
            int_ref_ref_cod_escola: $escolaId,
            int_ref_cod_infra_predio_comodo: null,
            str_nm_turma: null,
            str_sgl_turma: null,
            int_max_aluno: null,
            int_multiseriada: null,
            date_data_cadastro_ini: null,
            date_data_cadastro_fim: null,
            date_data_exclusao_ini: null,
            date_data_exclusao_fim: null,
            int_ativo: 1,
            int_ref_cod_turma_tipo: null,
            time_hora_inicial_ini: null,
            time_hora_inicial_fim: null,
            time_hora_final_ini: null,
            time_hora_final_fim: null,
            time_hora_inicio_intervalo_ini: null,
            time_hora_inicio_intervalo_fim: null,
            time_hora_fim_intervalo_ini: null,
            time_hora_fim_intervalo_fim: null,
            int_ref_cod_curso: null,
            int_ref_cod_instituicao: null,
            int_ref_cod_regente: null,
            int_ref_cod_instituicao_regente: null,
            int_ref_ref_cod_escola_mult: null,
            int_ref_ref_cod_serie_mult: null,
            int_qtd_min_alunos_matriculados: null,
            bool_verifica_serie_multiseriada: false,
            bool_tem_alunos_aguardando_nota: null,
            visivel: true,
            turma_turno_id: null,
            tipo_boletim: null,
            ano: $ultimoAnoLetivo['ano']
        );

        foreach ($turmasEscola as $turma) {
            $this->copiarTurma(turmaOrigem: $turma, anoOrigem: $ultimoAnoLetivo['ano'], anoDestino: $anoDestino);
        }
    }

    public function copiarTurma($turmaOrigem, $anoOrigem, $anoDestino)
    {
        $sql = 'select 1 from turma where ativo = 1 and visivel = true
            and ref_ref_cod_escola = $1 and nm_turma = $2 and ref_ref_cod_serie = $3 and ano = $4 limit 1';

        $params = [
            $turmaOrigem['ref_ref_cod_escola'],
            $turmaOrigem['nm_turma'],
            $turmaOrigem['ref_ref_cod_serie'],
            $anoDestino
        ];

        $existe = Portabilis_Utils_Database::selectField(sql: $sql, paramsOrOptions: $params);

        if ($existe != 1) {
            $fields = [
                'ref_usuario_exc',
                'ref_usuario_cad',
                'ref_ref_cod_serie',
                'ref_ref_cod_escola',
                'ref_cod_infra_predio_comodo',
                'nm_turma',
                'sgl_turma',
                'max_aluno',
                'multiseriada',
                'data_cadastro',
                'data_exclusao',
                'ativo',
                'ref_cod_turma_tipo',
                'hora_inicial',
                'hora_final',
                'hora_inicio_intervalo',
                'hora_fim_intervalo',
                'ref_cod_regente',
                'ref_cod_instituicao_regente',
                'ref_cod_instituicao',
                'ref_cod_curso',
                'ref_ref_cod_serie_mult',
                'ref_ref_cod_escola_mult',
                'visivel',
                'turma_turno_id',
                'tipo_boletim',
                'tipo_boletim_diferenciado',
                'ano',
                'dias_semana',
                'atividades_complementares',
                'atividades_aee',
                'turma_unificada',
                'tipo_atendimento',
                'etapa_educacenso',
                'cod_curso_profissional',
                'tipo_mediacao_didatico_pedagogico',
                'nao_informar_educacenso',
                'local_funcionamento_diferenciado'
            ];

            $turmaDestino = new clsPmieducarTurma();

            foreach ($fields as $fieldName) {
                $turmaDestino->$fieldName = $turmaOrigem[$fieldName];
            }

            $turmaDestino->ano = $anoDestino;
            $turmaDestino->ref_usuario_cad = $this->pessoa_logada;
            $turmaDestino->ref_usuario_exc = $this->pessoa_logada;
            $turmaDestino->visivel = dbBool($turmaOrigem['visivel']);
            $turmaDestinoId = $turmaDestino->cadastra();

            $this->copiarComponenteCurricularTurma(turmaOrigemId: $turmaOrigem['cod_turma'], turmaDestinoId: $turmaDestinoId);
            $this->copiarModulosTurma(turmaOrigemId: $turmaOrigem['cod_turma'], turmaDestinoId: $turmaDestinoId, anoOrigem: $anoOrigem, anoDestino: $anoDestino);

            if ($turmaOrigem['multiseriada'] === 1) {
                $this->criarTurmaMultisseriada(turmaOrigem: $turmaOrigem, turmaDestinoId: $turmaDestinoId);
            }
        }
    }

    private function criarTurmaMultisseriada($turmaOrigem, $turmaDestinoId)
    {
        /** @var LegacySchoolClassGrade[] $turmasSeries */
        $turmasSeries = LegacySchoolClassGrade::query()
            ->where(column: 'escola_id', operator: $turmaOrigem['ref_ref_cod_escola'])
            ->where(column: 'turma_id', operator: $turmaOrigem['cod_turma'])
            ->get()
        ;

        foreach ($turmasSeries as $turmaSerie) {
            $newTurmaSerie = new LegacySchoolClassGrade();

            $newTurmaSerie->escola_id = $turmaOrigem['ref_ref_cod_escola'];
            $newTurmaSerie->serie_id = $turmaSerie->serie_id;
            $newTurmaSerie->turma_id = $turmaDestinoId;
            $newTurmaSerie->boletim_id = $turmaSerie->boletim_id;
            $newTurmaSerie->boletim_diferenciado_id = $turmaSerie->boletim_diferenciado_id;

            $newTurmaSerie->save();
        }
    }

    public function copiarComponenteCurricularTurma($turmaOrigemId, $turmaDestinoId)
    {
        $dataMapper = new ComponenteCurricular_Model_TurmaDataMapper();
        $componentesTurmaOrigem = $dataMapper->findAll(columns: [], where: ['turma' => $turmaOrigemId]);

        foreach ($componentesTurmaOrigem as $componenteTurmaOrigem) {
            $data = [
                'componenteCurricular' => $componenteTurmaOrigem->get('componenteCurricular'),
                'escola' => $componenteTurmaOrigem->get('escola'),
                'cargaHoraria' => $componenteTurmaOrigem->get('cargaHoraria'),
                'turma' => $turmaDestinoId,
                // está sendo mantido o mesmo ano_escolar_id, uma vez que não foi
                // foi encontrado de onde o valor deste campo é obtido.
                'anoEscolar' => $componenteTurmaOrigem->get('anoEscolar')
            ];

            $componenteTurmaDestino = $dataMapper->createNewEntityInstance($data);
            $dataMapper->save($componenteTurmaDestino);
        }
    }

    public function copiarModulosTurma($turmaOrigemId, $turmaDestinoId, $anoOrigem, $anoDestino)
    {
        $modulosTurmaOrigem = new clsPmieducarTurmaModulo();
        $modulosTurmaOrigem = $modulosTurmaOrigem->lista($turmaOrigemId);

        foreach ($modulosTurmaOrigem as $moduloOrigem) {
            $moduloDestino = new clsPmieducarTurmaModulo();

            $moduloDestino->ref_cod_modulo = $moduloOrigem['ref_cod_modulo'];
            $moduloDestino->sequencial = $moduloOrigem['sequencial'];
            $moduloDestino->ref_cod_turma = $turmaDestinoId;

            $moduloDestino->data_inicio = str_replace(
                search: $anoOrigem,
                replace: $anoDestino,
                subject: $moduloOrigem['data_inicio']
            );

            $moduloDestino->data_fim = str_replace(
                search: $anoOrigem,
                replace: $anoDestino,
                subject: $moduloOrigem['data_fim']
            );

            if (Portabilis_Date_Utils::checkDateBissexto($moduloDestino->data_inicio)) {
                $moduloDestino->data_inicio = str_replace(search: 29, replace: 28, subject: $moduloDestino->data_inicio);
            }

            if (Portabilis_Date_Utils::checkDateBissexto($moduloDestino->data_fim)) {
                $moduloDestino->data_fim = str_replace(search: 29, replace: 28, subject: $moduloDestino->data_fim);
            }

            $moduloDestino->cadastra();
        }
    }

    public function gerarJsonDosModulos()
    {
        $retorno = [];

        foreach ($this->modulos as $modulo) {
            $retorno[$modulo['cod_modulo']] = [
                'label' => $modulo['nm_tipo'],
                'etapas' => (int)$modulo['num_etapas']
            ];
        }

        return json_encode($retorno);
    }

    protected function validaDates(): void
    {
        foreach ($this->data_inicio as $key => $campo) {
            $data_inicio = Carbon::createFromFormat('d/m/Y',$this->data_inicio[$key]);
            $data_fim = Carbon::createFromFormat('d/m/Y',$this->data_fim[$key]);

            $etapaAntigo = Portabilis_Utils_Database::selectRow(
                sql: 'SELECT data_inicio,data_fim FROM pmieducar.ano_letivo_modulo WHERE ref_ano <> $1 AND ref_ref_cod_escola = $2 AND
                                                                   ($3::date BETWEEN data_inicio AND data_fim::date OR $4::date BETWEEN data_inicio AND data_fim OR
                                                                   ($3::date <= data_inicio AND $4::date >= data_fim)) limit 1',
                paramsOrOptions: [$this->ref_ano,$this->ref_ref_cod_escola,$data_inicio,$data_fim]
            );

            if (!empty($etapaAntigo) && isset($etapaAntigo['data_inicio'],$etapaAntigo['data_fim'])) {
                throw new RuntimeException("A data informada não pode fazer parte do período configurado para outros anos letivos.");
            }
        }
    }

    protected function validaModulos()
    {
        $ano = $this->ref_ano;
        $escolaId = $this->ref_ref_cod_escola;
        $etapasCount = count($this->data_inicio);
        $etapasCountAntigo = (int) Portabilis_Utils_Database::selectField(
            sql: 'SELECT COUNT(*) AS count FROM pmieducar.ano_letivo_modulo WHERE ref_ano = $1 AND ref_ref_cod_escola = $2',
            paramsOrOptions: [$ano, $escolaId]
        );

        if ($etapasCount >= $etapasCountAntigo) {
            return true;
        }

        $etapasTmp = $etapasCount;
        $etapas = [];

        while ($etapasTmp < $etapasCountAntigo) {
            $etapasTmp += 1;
            $etapas[] = $etapasTmp;
        }

        $counts = [];

        $counts[] = DB::table('modules.falta_componente_curricular as fcc')
            ->join('modules.falta_aluno as fa', 'fa.id', '=', 'fcc.falta_aluno_id')
            ->join('pmieducar.matricula as m', 'm.cod_matricula', '=', 'fa.matricula_id')
            ->whereIn('fcc.etapa', $etapas)
            ->where('m.ref_ref_cod_escola', $escolaId)
            ->where('m.ano', $ano)
            ->where('m.ativo', 1)
            ->count();

        $counts[] = DB::table('modules.falta_geral as fg')
            ->join('modules.falta_aluno as fa', 'fa.id', '=', 'fg.falta_aluno_id')
            ->join('pmieducar.matricula as m', 'm.cod_matricula', '=', 'fa.matricula_id')
            ->whereIn('fg.etapa', $etapas)
            ->where('m.ref_ref_cod_escola', $escolaId)
            ->where('m.ano', $ano)
            ->where('m.ativo', 1)
            ->count();

        $counts[] = DB::table('modules.nota_componente_curricular as ncc')
            ->join('modules.nota_aluno as na', 'na.id', '=', 'ncc.nota_aluno_id')
            ->join('pmieducar.matricula as m', 'm.cod_matricula', '=', 'na.matricula_id')
            ->whereIn('ncc.etapa', $etapas)
            ->where('m.ref_ref_cod_escola', $escolaId)
            ->where('m.ano', $ano)
            ->where('m.ativo', 1)
            ->count();

        $sum = array_sum($counts);

        if ($sum > 0) {
            throw new RuntimeException('Não foi possível remover uma das etapas pois existem notas ou faltas lançadas.');
        }

        // Caso não exista token e URL de integração com o i-Diário, não irá
        // validar se há lançamentos nas etapas removidas

        $checkReleases = config('legacy.config.url_novo_educacao')
            && config('legacy.config.token_novo_educacao');

        if (!$checkReleases) {
            return true;
        }

        $iDiarioService = app(iDiarioService::class);

        foreach ($etapas as $etapa) {
            if ($iDiarioService->getStepActivityByUnit($escolaId, $ano, $etapa)) {
                throw new RuntimeException('Não foi possível remover uma das etapas pois existem notas ou faltas lançadas no diário online.');
            }
        }

        return true;
    }

    public function makeExtra()
    {
        return str_replace(
            search: '#modulos',
            replace: $this->gerarJsonDosModulos(),
            subject: file_get_contents(__DIR__ . '/scripts/extra/educar-ano-letivo-modulo-cad.js')
        );
    }

    public function Formular()
    {
        $this->title = 'Ano Letivo Etapa';
        $this->processoAp = 561;
    }
};
