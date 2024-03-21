<?php

use App\Models\EmployeeAllocation;
use App\Models\LegacyAcademicYearStage;
use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassGrade;
use App\Models\LegacySchoolClassTeacher;
use App\Models\LegacySchoolClassTeacherDiscipline;
use App\Models\LegacyStageType;
use App\Services\iDiarioService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

return new class extends clsCadastro
{
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

    public $copiar_alocacoes_e_vinculos_professores;

    public $copiar_alocacoes_demais_servidores;

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

        if (is_numeric(value: $this->ref_ano) && is_numeric(value: $this->ref_ref_cod_escola)) {
            $schoolAcademicYear = LegacySchoolAcademicYear::query()->where(
                column: [
                    'ref_cod_escola' => $this->ref_ref_cod_escola,
                    'ano' => $this->ref_ano,
                ]
            )->first();

            if ($schoolAcademicYear) {
                if ($obj_permissoes->permissao_excluir(int_processo_ap: 561, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
                    $this->fexcluir = true;
                }

                $this->copiar_alocacoes_demais_servidores = $schoolAcademicYear->copia_dados_professor;
                $this->copiar_alocacoes_e_vinculos_professores = $schoolAcademicYear->copia_dados_demais_servidores;

                $retorno = 'Editar';

                $this->etapas = LegacyAcademicYearStage::query()->where('ref_ano', $this->ref_ano)->where('ref_ref_cod_escola', $this->ref_ref_cod_escola)->orderBy('sequencial')->get();
                $this->ref_cod_modulo = $this->etapas->first()?->ref_cod_modulo;
            }
        }

        $this->url_cancelar = $_GET['referrer']
            ? $_GET['referrer'] . '?cod_escola=' . $this->ref_ref_cod_escola
            : 'educar_escola_lst.php';

        $this->breadcrumb(currentPage: 'Etapas do ano letivo', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
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

        $this->ref_cod_instituicao = LegacySchool::query()->where('cod_escola', $this->ref_ref_cod_escola)->value('ref_cod_instituicao');

        $registros = LegacyAcademicYearStage::query()->where('ref_ano', $this->ref_ano - 1)->where('ref_ref_cod_escola', $this->ref_ref_cod_escola)->orderBy('sequencial')->get();

        $cont = 0;

        if ($registros->isNotEmpty()) {
            $cor = '';
            $tabela = '<table border=0 style=\'\' cellpadding=2 width=\'100%\'>';
            $tabela .= "<tr bgcolor=$cor><td colspan='2'>Etapas do ano anterior (".($this->ref_ano - 1).')</td></tr><tr><td>';
            $tabela .= '<table cellpadding="2" cellspacing="2" border="0" align="left" width=\'300px\'>';
            $tabela .= '<tr bgcolor=\'#ccdce6\'><th width=\'100px\'>Etapa<a name=\'ano_letivo\'/></th><th width=\'200px\'>Período</th></tr>';

            $existeBissexto = false;

            foreach ($registros as $campo) {
                $cor = '#f5f9fd';
                $cont++;
                $tabela .= "<tr bgcolor='$cor'><td align='center'>{$cont}</td><td align='center'>".dataFromPgToBr(data_original: $campo['data_inicio']).' à '.dataFromPgToBr(data_original: $campo['data_fim']).'</td></tr>';

                $ano = date_parse_from_format(format: 'Y-m-d', datetime: $campo['data_inicio']);
                $ano = $ano['year'];

                $novaDataInicio = str_replace(search: $ano, replace: $this->ref_ano, subject: $campo['data_inicio']);
                $novaDataFim = str_replace(search: $ano, replace: $this->ref_ano, subject: $campo['data_fim']);

                if (
                    Portabilis_Date_Utils::checkDateBissexto(data: $novaDataInicio)
                    || Portabilis_Date_Utils::checkDateBissexto(data: $novaDataFim)
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

            $tabela .= '</table>';
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

        $lista = LegacyStageType::query()
            ->where('ativo', 1)
            ->where('ref_cod_instituicao', $this->ref_cod_instituicao)
            ->orderBy('nm_tipo')
            ->get()
            ->toArray();

        if (is_array(value: $lista) && count(value: $lista)) {
            $this->modulos = $lista;

            foreach ($lista as $registro) {
                $opcoesCampoModulo[$registro['cod_modulo']] = sprintf('%s - %d etapa(s)', $registro['nm_tipo'], $registro['num_etapas']);
            }
        }

        $this->campoLista(
            nome: 'ref_cod_modulo',
            campo: 'Etapa',
            valor: $opcoesCampoModulo,
            default: \Request::get('ref_cod_modulo', $this->ref_cod_modulo),
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

        if (is_numeric(value: $this->ref_ano) && is_numeric(value: $this->ref_ref_cod_escola) && !$_POST) {
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
                    $this->ano_letivo_modulo[$qtd_registros][] = dataFromPgToBr(data_original: $campo['data_inicio']);
                    $this->ano_letivo_modulo[$qtd_registros][] = dataFromPgToBr(data_original: $campo['data_fim']);
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

            $this->campoQuebra();

            $this->campoRotulo(
                nome: 'titulo-alocacoes-vinculos',
                campo: 'Alocações e vínculos',
                separador: null
            );

            $this->campoRotulo(
                nome: 'informativo1-alocacoes-vinculos',
                campo: '
                    Ao definir um novo ano letivo, o i-Educar copia automaticamente as turmas do ano anterior. <br>
                    Gostaria de copiar também as alocações e vínculos?
                ',
                separador: null
            );

            $checkedProfessores = ($this->copiar_alocacoes_e_vinculos_professores || $this->tipoacao == 'Novo') ? 'checked' : '';
            $checkedDemaisServidores = ($this->copiar_alocacoes_demais_servidores || $this->tipoacao == 'Novo') ? 'checked' : '';

            $this->campoRotulo(
                nome: 'copiar_alocacoes_e_vinculos_professores_',
                campo: '
                    <input type ="checkbox" '.$checkedProfessores.' id="copiar_alocacoes_e_vinculos_professores" name ="copiar_alocacoes_e_vinculos_professores">
                    <label for = "subscribeNews">Copiar alocações e vínculos dos professores</ label>
                ',
                separador: null
            );

            $this->campoRotulo(
                nome: 'copiar_alocacoes_demais_servidores_',
                campo: '
                    <input type="checkbox" '.$checkedDemaisServidores.' id="copiar_alocacoes_demais_servidores" name="copiar_alocacoes_demais_servidores">
                    <label for = "subscribeNews">Copiar alocações dos demais servidores</ label>
                ',
                separador: null
            );

            $this->campoRotulo(
                nome: 'informativo2-alocacoes-vinculos',
                campo: 'As alocações e vínculos podem depois ser editadas e excluídas, caso necessário',
                separador: null
            );
        }

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: [
            '/vendor/legacy/Portabilis/Assets/Javascripts/Validator.js',
            '/intranet/scripts/etapas.js',
        ]);

        $styles = ['/vendor/legacy/Cadastro/Assets/Stylesheets/EscolaAnosLetivos.css'];

        Portabilis_View_Helper_Application::loadStylesheet(viewInstance: $this,
            files: ['/vendor/legacy/Cadastro/Assets/Stylesheets/AnoLetivoModulo.css']);
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

        $this->copiar_alocacoes_e_vinculos_professores = !is_null(value: $this->copiar_alocacoes_e_vinculos_professores);
        $this->copiar_alocacoes_demais_servidores = !is_null(value: $this->copiar_alocacoes_demais_servidores);

        if ($this->ref_cod_modulo && $this->data_inicio && $this->data_fim) {
            $this->copiarTurmasUltimoAno(
                escolaId: $this->ref_ref_cod_escola,
                anoDestino: $this->ref_ano,
                copiaDadosProfessor: $this->copiar_alocacoes_e_vinculos_professores
            );

            if ($this->copiar_alocacoes_demais_servidores === true) {
                $this->copyEmployeeAllocations(refCodEscola: $this->ref_ref_cod_escola, anoDestino: $this->ref_ano);
            }

            Portabilis_Utils_Database::selectField(sql: "SELECT pmieducar.copiaAnosLetivos({$this->ref_ano}::smallint, {$this->ref_ref_cod_escola});");

            $schoolAcademicYear = new LegacySchoolAcademicYear();

            $schoolAcademicYear->ref_cod_escola = $this->ref_ref_cod_escola;
            $schoolAcademicYear->ano = $this->ref_ano;
            $schoolAcademicYear->ref_usuario_cad = $this->pessoa_logada;
            $schoolAcademicYear->andamento = 0;
            $schoolAcademicYear->ativo = 1;
            $schoolAcademicYear->turmas_por_ano = 1;
            $schoolAcademicYear->copia_dados_professor = $this->copiar_alocacoes_e_vinculos_professores;
            $schoolAcademicYear->copia_dados_demais_servidores = $this->copiar_alocacoes_demais_servidores;

            if ($schoolAcademicYear->save()) {
                foreach ($this->data_inicio as $key => $campo) {
                    $this->data_inicio[$key] = dataToBanco(data_original: $this->data_inicio[$key]);
                    $this->data_fim[$key] = dataToBanco(data_original: $this->data_fim[$key]);

                    if ($this->dias_letivos[$key] == '') {
                        $this->dias_letivos[$key] = '0';
                    }

                    $cadastrou1 = null;
                    $data = [
                        'ref_ano' => $this->ref_ano,
                        'ref_ref_cod_escola' => $this->ref_ref_cod_escola,
                        'sequencial' => $key + 1,
                        'ref_cod_modulo' => $this->ref_cod_modulo,
                        'data_inicio' => $this->data_inicio[$key],
                        'data_fim' => $this->data_fim[$key],
                        'dias_letivos' => $this->dias_letivos[$key],
                    ];
                    if ($this->validaAnoLetivoModulo($data)) {
                        $cadastrou1 = $schoolAcademicYear->academicYearStages()->create($data);
                        LegacySchoolAcademicYear::query()->where('ref_cod_escola', $this->ref_ref_cod_escola)->where('ano', $this->ref_ano)->where('ativo', 0)->update(['ativo' => 1]);
                    }

                    if (!$cadastrou1) {
                        $this->mensagem = 'Cadastro não realizado.<br />';

                        return false;
                    }
                }

                $this->mensagem = 'Cadastro efetuado com sucesso.<br />';

                $this->simpleRedirect(url: 'educar_escola_det.php?cod_escola=' . $this->ref_ref_cod_escola . '#ano_letivo');
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

            $excluiu = true;
            if (is_numeric($this->ref_ano) && is_numeric($this->ref_ref_cod_escola)) {
                $excluiu = LegacyAcademicYearStage::query()->where('ref_ref_cod_escola', $this->ref_ref_cod_escola)->where('ref_ano', $this->ref_ano)->delete() >= 0;
            }

            if ($excluiu) {
                $schoolAcademicYear = LegacySchoolAcademicYear::query()->where('ref_cod_escola', $this->ref_ref_cod_escola)->where('ano', $this->ref_ano)->first();

                foreach ($this->data_inicio as $key => $campo) {
                    $this->data_inicio[$key] = dataToBanco(data_original: $this->data_inicio[$key]);
                    $this->data_fim[$key] = dataToBanco(data_original: $this->data_fim[$key]);

                    if ($this->dias_letivos[$key] == '') {
                        $this->dias_letivos[$key] = '0';
                    }

                    $cadastrou1 = null;

                    $data = [
                        'ref_ano' => $this->ref_ano,
                        'ref_ref_cod_escola' => $this->ref_ref_cod_escola,
                        'sequencial' => $key + 1,
                        'ref_cod_modulo' => $this->ref_cod_modulo,
                        'data_inicio' => $this->data_inicio[$key],
                        'data_fim' => $this->data_fim[$key],
                        'dias_letivos' => $this->dias_letivos[$key],
                    ];

                    if ($this->validaAnoLetivoModulo($data)) {
                        $cadastrou1 = $schoolAcademicYear->academicYearStages()->create($data);
                        LegacySchoolAcademicYear::query()->where('ref_cod_escola', $this->ref_ref_cod_escola)->where('ano', $this->ref_ano)->where('ativo', 0)->update(['ativo' => 1]);
                    }

                    if (!$cadastrou1) {
                        $this->mensagem = 'Edição não realizada.<br />';

                        return false;
                    }
                }

                $this->mensagem = 'Edição efetuada com sucesso.<br />';
                $this->simpleRedirect(url: 'educar_escola_lst.php');
            }
        }

        echo '<script>alert(\'É necessário adicionar pelo menos uma etapa!\')</script>';
        $this->mensagem = 'Edição não realizada.<br />';

        return false;
    }

    private function validaAnoLetivoModulo(array $data): bool
    {
        return is_numeric($data['ref_ano'])
            && is_numeric($data['ref_ref_cod_escola'])
            && is_numeric($data['sequencial'])
            && is_numeric($data['ref_cod_modulo'])
            && is_string($data['data_inicio'])
            && is_string($data['data_fim'])
            && is_numeric($data['dias_letivos']);
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

        //Salvar com query raw, pois o model não tem primary key única e gera erro modificando todas as escolas em vez de uma
        LegacySchoolAcademicYear::query()->where(
            column: [
                'ref_cod_escola' => $this->ref_ref_cod_escola,
                'ano' => $this->ref_ano,
            ]
        )->update([
            'ref_usuario_cad' => $this->pessoa_logada,
            'andamento' => 2,
            'ativo' => 0,
        ]);

        $excluiu1 = LegacyAcademicYearStage::query()->where('ref_ref_cod_escola', $this->ref_ref_cod_escola)->where('ref_ano', $this->ref_ano)->delete() >= 0;

        if ($excluiu1) {
            $this->mensagem = 'Exclusão efetuada com sucesso.<br />';
            $this->simpleRedirect(url: 'educar_escola_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br />';

        return false;

    }

    public function copiarTurmasUltimoAno($escolaId, $anoDestino, $copiaDadosProfessor = true)
    {
        $lastSchoolAcademicYear = LegacySchoolAcademicYear::query()
            ->whereSchool(school: $escolaId)
            ->active()
            ->max(column: 'ano');

        $turmasEscola = (new clsPmieducarTurma())->lista(
            int_ref_ref_cod_escola: $escolaId,
            int_ativo: 1,
            ano: $lastSchoolAcademicYear
        );

        foreach ($turmasEscola as $turma) {
            $this->copiarTurma(
                turmaOrigem: $turma,
                anoOrigem: $lastSchoolAcademicYear,
                anoDestino: $anoDestino,
                copiaDadosProfessor: $copiaDadosProfessor
            );
        }

        if ($copiaDadosProfessor === true) {
            $this->copyEmployeeAllocations(refCodEscola: $this->ref_ref_cod_escola, anoDestino: $this->ref_ano, onlyTeacher: true);
        }
    }

    public function copiarTurma($turmaOrigem, $anoOrigem, $anoDestino, $copiaDadosProfessor)
    {
        $naoExiste = LegacySchoolClass::query()
            ->whereSchool($turmaOrigem['ref_ref_cod_escola'])
            ->where('nm_turma', $turmaOrigem['nm_turma'])
            ->where('ref_ref_cod_serie', $turmaOrigem['ref_ref_cod_serie'])
            ->whereYearEq($anoDestino)
            ->active()
            ->visible()
            ->doesntExist();

        if ($naoExiste) {
            $turma = LegacySchoolClass::query()->find($turmaOrigem['cod_turma']);
            if (!$turma) {
                return;
            }

            $turmaDestino = $turma->replicate([
                'data_cadastro',
                'updated_at',
                'data_exclusao',
                'parecer_1_etapa',
                'parecer_2_etapa',
                'parecer_3_etapa',
                'parecer_4_etapa',
            ])->fill([
                'ano' => $anoDestino,
                'ref_usuario_cad' => $this->pessoa_logada,
                'ref_usuario_exc' => $this->pessoa_logada,
            ]);
            $turmaDestino->save();
            $turmaDestinoId = $turmaDestino->getKey();
            $this->copiarComponenteCurricularTurma(turmaOrigemId: $turmaOrigem['cod_turma'], turmaDestinoId: $turmaDestinoId);
            $this->copiarModulosTurma(turmaOrigemId: $turmaOrigem['cod_turma'], turmaDestinoId: $turmaDestinoId, anoOrigem: $anoOrigem, anoDestino: $anoDestino);

            if ($turmaOrigem['multiseriada'] === 1) {
                $this->criarTurmaMultisseriada(turmaOrigem: $turmaOrigem, turmaDestinoId: $turmaDestinoId);
            }

            if ($copiaDadosProfessor === true) {
                $this->copySchoolClassTeacher(
                    originSchoolClassId: $turmaOrigem,
                    destinationSchoolClassId: $turmaDestinoId,
                    originYear: $anoOrigem,
                    destinationYear: $anoDestino
                );
            }
        }
    }

    private function copySchoolClassTeacher($originSchoolClassId, $destinationSchoolClassId, $originYear, $destinationYear)
    {
        $schoolClassTeachers = LegacySchoolClassTeacher::query()
            ->where(column: ['ano' => $originYear, 'turma_id' => $originSchoolClassId])
            ->get();

        /** @var LegacySchoolClassTeacher $schoolClassTeacher */
        foreach ($schoolClassTeachers as $schoolClassTeacher) {
            $exist = LegacySchoolClassTeacher::query()->where(
                column: [
                    'ano' => $destinationYear,
                    'turma_id' => $destinationSchoolClassId,
                    'servidor_id' => $schoolClassTeacher->servidor_id,
                ]
            )->exists();

            if ($exist === true) {
                continue;
            }

            $newSchoolClassTeacher = $schoolClassTeacher->replicate();
            $newSchoolClassTeacher->ano = $destinationYear;
            $newSchoolClassTeacher->turma_id = $destinationSchoolClassId;

            $newSchoolClassTeacher->save();

            $this->copySchoolClassTeacherDiscipline(schoolClassTeacher: $schoolClassTeacher, newSchoolClassTeacher: $newSchoolClassTeacher);
        }
    }

    private function copySchoolClassTeacherDiscipline(
        LegacySchoolClassTeacher $schoolClassTeacher,
        LegacySchoolClassTeacher $newSchoolClassTeacher
    ) {
        $schoolClassTeacherDisciplines = LegacySchoolClassTeacherDiscipline::query()
            ->where(column: 'professor_turma_id', operator: $schoolClassTeacher->getKey())
            ->get();

        /** @var LegacySchoolClassTeacherDiscipline $schoolClassTeacherDiscipline */
        foreach ($schoolClassTeacherDisciplines as $schoolClassTeacherDiscipline) {
            $exist = LegacySchoolClassTeacherDiscipline::query()->where(column: [
                'professor_turma_id' => $newSchoolClassTeacher->getKey(),
                'componente_curricular_id' => $schoolClassTeacherDiscipline->componente_curricular_id,
            ])->exists();

            if ($exist === true) {
                continue;
            }

            $newSchoolClassTeacherDisciplines = $schoolClassTeacherDiscipline->replicate();
            $newSchoolClassTeacherDisciplines->professor_turma_id = $newSchoolClassTeacher->getKey();

            $newSchoolClassTeacherDisciplines->save();
        }
    }

    public function copyEmployeeAllocations($refCodEscola, $anoDestino, $onlyTeacher = false)
    {
        $lastSchoolAcademicYear = LegacySchoolAcademicYear::query()
            ->whereSchool(school: $refCodEscola)
            ->active()
            ->max(column: 'ano');

        $employeeAllocations = EmployeeAllocation::query()
            ->whereHas(relation: 'employee', callback: fn ($q) => ($q->professor($onlyTeacher)))
            ->where(
                column: [
                    'ano' => $lastSchoolAcademicYear,
                    'ref_cod_escola' => $refCodEscola,
                ]
            )->get();

        /** @var EmployeeAllocation $employeeAllocation */
        foreach ($employeeAllocations as $employeeAllocation) {

            $exist = EmployeeAllocation::query()->where(
                column: [
                    'ano' => $anoDestino,
                    'ref_cod_escola' => $refCodEscola,
                    'ref_cod_servidor' => $employeeAllocation->ref_cod_servidor,
                    'ref_cod_servidor_funcao' => $employeeAllocation->ref_cod_servidor_funcao,
                ]
            )->exists();

            if ($exist === true) {
                continue;
            }

            $newEmployeeAllocation = $employeeAllocation->replicate();
            $newEmployeeAllocation->ano = $anoDestino;

            $newEmployeeAllocation->save();
        }
    }

    private function criarTurmaMultisseriada($turmaOrigem, $turmaDestinoId)
    {
        /** @var LegacySchoolClassGrade[] $turmasSeries */
        $turmasSeries = LegacySchoolClassGrade::query()
            ->where(column: 'escola_id', operator: $turmaOrigem['ref_ref_cod_escola'])
            ->where(column: 'turma_id', operator: $turmaOrigem['cod_turma'])
            ->get();

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
                'anoEscolar' => $componenteTurmaOrigem->get('anoEscolar'),
            ];

            $componenteTurmaDestino = $dataMapper->createNewEntityInstance(data: $data);
            $dataMapper->save(instance: $componenteTurmaDestino);
        }
    }

    public function copiarModulosTurma($turmaOrigemId, $turmaDestinoId, $anoOrigem, $anoDestino)
    {
        $modulosTurmaOrigem = new clsPmieducarTurmaModulo();
        $modulosTurmaOrigem = $modulosTurmaOrigem->lista(int_ref_cod_turma: $turmaOrigemId);

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

            $moduloDestino->dias_letivos = $moduloOrigem['dias_letivos'];

            if (Portabilis_Date_Utils::checkDateBissexto(data: $moduloDestino->data_inicio)) {
                $moduloDestino->data_inicio = str_replace(search: 29, replace: 28, subject: $moduloDestino->data_inicio);
            }

            if (Portabilis_Date_Utils::checkDateBissexto(data: $moduloDestino->data_fim)) {
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
                'etapas' => (int) $modulo['num_etapas'],
            ];
        }

        return json_encode(value: $retorno);
    }

    protected function validaDates(): void
    {
        foreach ($this->data_inicio as $key => $campo) {
            $data_inicio = Carbon::createFromFormat('d/m/Y', $this->data_inicio[$key]);
            $data_fim = Carbon::createFromFormat('d/m/Y', $this->data_fim[$key]);

            $etapaAntigo = Portabilis_Utils_Database::selectRow(
                sql: 'SELECT data_inicio,data_fim FROM pmieducar.ano_letivo_modulo WHERE ref_ano <> $1 AND ref_ref_cod_escola = $2 AND
                                                                   ($3::date BETWEEN data_inicio AND data_fim::date OR $4::date BETWEEN data_inicio AND data_fim OR
                                                                   ($3::date <= data_inicio AND $4::date >= data_fim)) limit 1',
                paramsOrOptions: [$this->ref_ano, $this->ref_ref_cod_escola, $data_inicio, $data_fim]
            );

            if (!empty($etapaAntigo) && isset($etapaAntigo['data_inicio'],$etapaAntigo['data_fim'])) {
                throw new RuntimeException(message: 'A data informada não pode fazer parte do período configurado para outros anos letivos.');
            }
        }
    }

    protected function validaModulos()
    {
        $ano = $this->ref_ano;
        $escolaId = $this->ref_ref_cod_escola;
        $etapasCount = count(value: $this->data_inicio);
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
            ->join(table: 'modules.falta_aluno as fa', first: 'fa.id', operator: '=', second: 'fcc.falta_aluno_id')
            ->join(table: 'pmieducar.matricula as m', first: 'm.cod_matricula', operator: '=', second: 'fa.matricula_id')
            ->whereIn(column: 'fcc.etapa', values: $etapas)
            ->where(column: 'm.ref_ref_cod_escola', operator: $escolaId)
            ->where(column: 'm.ano', operator: $ano)
            ->where(column: 'm.ativo', operator: 1)
            ->count();

        $counts[] = DB::table('modules.falta_geral as fg')
            ->join(table: 'modules.falta_aluno as fa', first: 'fa.id', operator: '=', second: 'fg.falta_aluno_id')
            ->join(table: 'pmieducar.matricula as m', first: 'm.cod_matricula', operator: '=', second: 'fa.matricula_id')
            ->whereIn(column: 'fg.etapa', values: $etapas)
            ->where(column: 'm.ref_ref_cod_escola', operator: $escolaId)
            ->where(column: 'm.ano', operator: $ano)
            ->where(column: 'm.ativo', operator: 1)
            ->count();

        $counts[] = DB::table('modules.nota_componente_curricular as ncc')
            ->join(table: 'modules.nota_aluno as na', first: 'na.id', operator: '=', second: 'ncc.nota_aluno_id')
            ->join(table: 'pmieducar.matricula as m', first: 'm.cod_matricula', operator: '=', second: 'na.matricula_id')
            ->whereIn(column: 'ncc.etapa', values: $etapas)
            ->where(column: 'm.ref_ref_cod_escola', operator: $escolaId)
            ->where(column: 'm.ano', operator: $ano)
            ->where(column: 'm.ativo', operator: 1)
            ->count();

        $sum = array_sum(array: $counts);

        if ($sum > 0) {
            throw new RuntimeException(message: 'Não foi possível remover uma das etapas pois existem notas ou faltas lançadas.');
        }

        // Caso não exista token e URL de integração com o i-Diário, não irá
        // validar se há lançamentos nas etapas removidas

        $checkReleases = config(key: 'legacy.config.url_novo_educacao')
            && config(key: 'legacy.config.token_novo_educacao');

        if (!$checkReleases) {
            return true;
        }

        $iDiarioService = app(abstract: iDiarioService::class);

        foreach ($etapas as $etapa) {
            if ($iDiarioService->getStepActivityByUnit($escolaId, $ano, $etapa)) {
                throw new RuntimeException(message: 'Não foi possível remover uma das etapas pois existem notas ou faltas lançadas no diário online.');
            }
        }

        return true;
    }

    public function makeExtra()
    {
        return str_replace(
            search: '#modulos',
            replace: $this->gerarJsonDosModulos(),
            subject: file_get_contents(filename: __DIR__ . '/scripts/extra/educar-ano-letivo-modulo-cad.js')
        );
    }

    public function Formular()
    {
        $this->title = 'Ano Letivo Etapa';
        $this->processoAp = 561;
    }
};
