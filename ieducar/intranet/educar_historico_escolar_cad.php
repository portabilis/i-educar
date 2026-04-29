<?php

use App\Models\Country;
use App\Models\LegacyInstitution;
use App\Models\LegacySchoolHistory;
use App\Models\LegacySchoolHistoryDiscipline;
use App\Models\RegistrationStatus;
use App\Models\State;
use App\Services\SchoolHistoryService;
use iEducar\Modules\Enrollments\Model\EnrollmentStatusFilter;
use Illuminate\Support\Facades\DB;

return new class extends clsCadastro
{
    public $ref_cod_aluno;

    public $sequencial;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ano;

    public $carga_horaria;

    public $dias_letivos;

    public $ref_cod_escola;

    public $escola;

    public $escola_cidade;

    public $escola_uf;

    public $observacao;

    public $aprovado;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $posicao;

    public $ref_cod_instituicao;

    public $nm_curso;

    public $nm_serie;

    public $origem;

    public $extra_curricular;

    public $ref_cod_matricula;

    public $faltas_globalizadas;

    public $cb_faltas_globalizadas;

    public $frequencia;

    public $historico_disciplinas;

    public $nm_disciplina;

    public $nota;

    public $faltas;

    public $ordenamento;

    public $carga_horaria_disciplina;

    public $disciplinaDependencia;

    public $excluir_disciplina;

    public $ultimo_sequencial;

    public $aceleracao;

    public $dependencia;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->sequencial = $_GET['sequencial'];
        $this->ref_cod_aluno = $_GET['ref_cod_aluno'];

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        if (is_numeric($this->ref_cod_aluno) && is_numeric($this->sequencial)) {
            $registro = LegacySchoolHistory::forStudentSequential($this->ref_cod_aluno, $this->sequencial)->first();

            if ($registro) {
                foreach ($registro->getAttributes() as $campo => $val) {
                    $this->$campo = $val;
                }

                if (!$this->origem) {
                    $this->simpleRedirect("educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
                }

                if ($obj_permissoes->permissao_excluir(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
                    $this->fexcluir = true;
                }

                if (!isset($_GET['copia'])) {
                    $retorno = 'Editar';
                } else {
                    $this->fexcluir = false;
                }
            }
        }

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_historico_escolar_det.php?ref_cod_aluno={$registro['ref_cod_aluno']}&sequencial={$registro['sequencial']}" : "educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}";
        $this->nome_url_cancelar = 'Cancelar';
        $this->dependencia = dbBool($this->dependencia);

        $this->breadcrumb(currentPage: 'Atualização de históricos escolares', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $nomeEscola = $this->escola;
        $codigoEscola = $this->ref_cod_escola;

        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = (!$this->$campo) ? $val : $this->$campo;
            }
        }

        // primary keys
        $this->campoOculto(nome: 'ref_cod_aluno', valor: $this->ref_cod_aluno);
        $this->campoOculto(nome: 'sequencial', valor: $this->sequencial);
        $this->campoOculto(nome: 'codigoEscola', valor: $codigoEscola);
        $this->campoOculto(nome: 'nomeEscola', valor: $nomeEscola);
        $this->campoOculto(nome: 'numeroSequencial', valor: $_GET['sequencial']);

        $obj_aluno = new clsPmieducarAluno;
        $lst_aluno = $obj_aluno->lista(int_cod_aluno: $this->ref_cod_aluno, int_ativo: 1);

        if (is_array($lst_aluno)) {
            $det_aluno = array_shift($lst_aluno);
            $this->nm_aluno = $det_aluno['nome_aluno'];
            $this->campoRotulo(nome: 'nm_aluno', campo: 'Aluno', valor: $this->nm_aluno);
        }

        $obj_nivelUser = new clsPermissoes;
        $user_nivel = $obj_nivelUser->nivel_acesso($this->pessoa_logada);

        if ($user_nivel != App_Model_NivelTipoUsuario::POLI_INSTITUCIONAL) {
            $obj_permissoes = new clsPermissoes;
            $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
            $habilitaCargaHoraria = $this->habilitaCargaHoraria($this->ref_cod_instituicao);
        }
        $this->inputsHelper()->dynamic(helperNames: ['instituicao', 'escolaSemFiltroPorUsuario'], inputOptions: ['required' => false]);
        $labelHintEscolaForaDoMunicipio = 'Transferência para uma escola externa (outro município, particular, etc)';
        $this->inputsHelper()->checkbox(attrName: 'escola_em_outro_municipio', inputOptions: ['label' => 'Escola em outro município ou fora da rede?', '<br>label_hint' => $labelHintEscolaForaDoMunicipio]);

        $escola_options = [
            'required' => false,
            'label' => 'Nome da escola',
            'value' => $this->escola,
            'max_length' => 255,
            'size' => 80,
        ];
        $this->inputsHelper()->text(attrNames: 'escola', inputOptions: $escola_options);

        $countryId = null;

        if ($this->escola_uf) {
            $state = State::findByAbbreviation($this->escola_uf);

            $countryId = $state->country_id;
        }

        $lista_pais_origem = Country::query()->orderBy('name')->pluck(column: 'name', key: 'id')->prepend(value: 'Selecione um país', key: '');

        $this->campoLista(nome: 'idpais', campo: 'País da Escola', valor: $lista_pais_origem, default: $countryId ?? 45, obrigatorio: false);

        $lista_estado = ['' => 'Selecione um estado'] + State::getListKeyAbbreviation()->toArray();

        $this->campoLista(nome: 'escola_uf', campo: 'Estado da Escola', valor: $lista_estado, default: $this->escola_uf, obrigatorio: false);

        $options = ['label' => 'Cidade da Escola', 'required' => false];

        $helperOptions = [
            'objectName' => 'escola_cidade',
            'hiddenInputOptions' => ['options' => ['value' => mb_strtoupper($this->escola_cidade)]],
            'apiResource' => 'municipio-name-search',
            'placeholder' => 'Informe o nome da cidade',
            'checkIfExists' => false,
        ];

        $this->inputsHelper()->simpleSearchMunicipio(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

        $this->campoTexto(nome: 'nm_curso', campo: 'Curso', valor: $this->nm_curso, tamanhovisivel: 30, tamanhomaximo: 255, descricao: _cl('historico.cadastro.curso_detalhe'));

        $opcoesGradeCurso = $this->getOpcoesGradeCurso();
        $this->campoLista(nome: 'historico_grade_curso_id', campo: 'Grade curso', valor: $opcoesGradeCurso, default: $this->historico_grade_curso_id);

        $this->campoTexto(nome: 'nm_serie', campo: _cl('historico.cadastro.serie'), valor: $this->nm_serie, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoCheck(nome: 'dependencia', campo: 'Histórico de dependência', valor: $this->dependencia);
        $this->campoNumero(nome: 'ano', campo: 'Ano', valor: $this->ano, tamanhovisivel: 4, tamanhomaximo: 4, obrigatorio: true);

        if ($this->validaControlePosicaoHistorico()) {
            $this->campoNumero(nome: 'posicao', campo: 'Posição', valor: $this->posicao, tamanhovisivel: 1, tamanhomaximo: 1, obrigatorio: false, descricao: 'Informe a coluna equivalente a série/ano/etapa a qual o histórico pertence. Ex.: 1º ano informe 1, 2º ano informe 2');
        }

        $this->campoNumero(nome: 'carga_horaria', campo: 'Carga Horária', valor: $this->carga_horaria, tamanhovisivel: 8, tamanhomaximo: 8);
        $this->campoCheck(nome: 'cb_faltas_globalizadas', campo: 'Faltas Globalizadas', valor: is_numeric($this->faltas_globalizadas) ? 'on' : '');
        $this->campoNumero(nome: 'faltas_globalizadas', campo: 'Faltas Globalizadas', valor: $this->faltas_globalizadas, tamanhovisivel: 4, tamanhomaximo: 4);
        $this->campoNumero(nome: 'dias_letivos', campo: 'Dias Letivos', valor: $this->dias_letivos, tamanhovisivel: 3, tamanhomaximo: 3);
        $this->campoMonetario(nome: 'frequencia', campo: 'Frequência', valor: $this->frequencia, tamanhovisivel: 8, tamanhomaximo: 6);
        $this->campoCheck(nome: 'extra_curricular', campo: 'Extra-Curricular', valor: $this->extra_curricular);
        $this->campoCheck(nome: 'aceleracao', campo: 'Aceleração', valor: $this->aceleracao);

        $obs_options = [
            'required' => false,
            'label' => 'Observação',
            'value' => $this->observacao,
        ];
        $this->inputsHelper()->textArea(attrName: 'observacao', inputOptions: $obs_options);

        $opcoes = collect(EnrollmentStatusFilter::getDescriptiveValues())
            ->prepend('Selecione', '')
            ->except([
                EnrollmentStatusFilter::EXCEPT_TRANSFERRED_OR_ABANDONMENT,  // Exceto Transferidos/Deixou de Frequentar'
                EnrollmentStatusFilter::ALL, // Todas
                RegistrationStatus::DECEASED,  // Falecido
            ]);

        $this->campoLista(nome: 'aprovado', campo: 'Situação', valor: $opcoes, default: $this->aprovado);
        $this->campoTexto(nome: 'registro', campo: 'Registro (arquivo)', valor: $this->registro, tamanhovisivel: 30, tamanhomaximo: 50);
        $this->campoTexto(nome: 'livro', campo: 'Livro', valor: $this->livro, tamanhovisivel: 30, tamanhomaximo: 50);
        $this->campoTexto(nome: 'folha', campo: 'Folha', valor: $this->folha, tamanhovisivel: 30, tamanhomaximo: 50);

        // ---------------------INCLUI DISCIPLINAS---------------------//
        $this->campoQuebra();

        if (is_numeric($this->ref_cod_aluno) && is_numeric($this->sequencial) && !$_POST) {
            $historicoEscolar = LegacySchoolHistory::forStudentSequential($this->ref_cod_aluno, $this->sequencial)->first();

            $registros = $historicoEscolar?->disciplines()->orderBy('nm_disciplina')->get();
            $qtd_disciplinas = 0;

            if ($registros) {
                foreach ($registros as $campo) {
                    $this->historico_disciplinas[$qtd_disciplinas][] = $campo->nm_disciplina;
                    $this->historico_disciplinas[$qtd_disciplinas][] = $campo->tipo_base;
                    $this->historico_disciplinas[$qtd_disciplinas][] = $campo->nota;
                    $this->historico_disciplinas[$qtd_disciplinas][] = $campo->faltas;
                    $this->historico_disciplinas[$qtd_disciplinas][] = $campo->carga_horaria_disciplina;
                    $this->historico_disciplinas[$qtd_disciplinas][] = $campo->ordenamento;
                    $this->historico_disciplinas[$qtd_disciplinas][] = dbBool($campo->dependencia) ? 1 : 0;
                    $this->historico_disciplinas[$qtd_disciplinas][] = $campo->sequencial;
                    $qtd_disciplinas++;
                }
            }
        }

        // Tipo Base
        $tipoBase = ComponenteCurricular_Model_TipoBase::getInstance()->getEnums();

        $this->campoTabelaInicio(nome: 'notas', titulo: 'Notas', arr_campos: ['Disciplina', 'Base curricular', 'Nota', 'Faltas', 'C.H', 'Ordem', 'Dependência'], arr_valores: $this->historico_disciplinas);
        $this->campoTexto(nome: 'nm_disciplina', campo: 'Disciplina', valor: $this->nm_disciplina, tamanhovisivel: 30, tamanhomaximo: 255, evento: 'onfocus');
        $this->campoLista(nome: 'tipo_base', campo: 'Base curricular', valor: $tipoBase, default: $this->tipo_base, obrigatorio: false);
        $this->campoTexto(nome: 'nota', campo: 'Nota', valor: $this->nota, tamanhovisivel: 10, tamanhomaximo: 255);
        $this->campoNumero(nome: 'faltas', campo: 'Faltas', valor: $this->faltas, tamanhovisivel: 3, tamanhomaximo: 3);
        $this->campoNumero(nome: 'carga_horaria_disciplina', campo: 'carga_horaria_disciplina', valor: $this->carga_horaria_disciplina, tamanhovisivel: 3, tamanhomaximo: 3, descricao: null, descricao2: null, script: null, evento: null, duplo: null, disabled: $habilitaCargaHoraria);
        $this->campoNumero(nome: 'ordenamento', campo: 'ordenamento', valor: $this->ordenamento, tamanhovisivel: 3, tamanhomaximo: 3);
        $options = ['label' => 'Dependência', 'value' => $this->disciplinaDependencia];
        $this->inputsHelper()->checkbox(attrName: 'disciplinaDependencia', inputOptions: $options);

        $this->campoTabelaFim();

        $this->campoQuebra();

        // ---------------------FIM INCLUI DISCIPLINAS---------------------//

        // carrega estilo para feedback messages, para exibir msg validação frequencia.

        $style = '/vendor/legacy/Portabilis/Assets/Stylesheets/Frontend.css';
        Portabilis_View_Helper_Application::loadStylesheet(viewInstance: $this, files: $style);

        Portabilis_View_Helper_Application::loadJavascript(
            viewInstance: $this,
            files: [
                '/vendor/legacy/Portabilis/Assets/Javascripts/Utils.js',
                '/vendor/legacy/Portabilis/Assets/Javascripts/Frontend/Inputs/SimpleSearch.js',
                '/vendor/legacy/Portabilis/Assets/Javascripts/Validator.js',
                '/vendor/legacy/Cadastro/Assets/Javascripts/HistoricoEscolar.js',
            ]
        );
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        $service = app(SchoolHistoryService::class);
        $instituicao = LegacyInstitution::active()->first();
        $schoolName = is_numeric($this->ref_cod_escola) ? $service->getSchoolName($this->ref_cod_escola) : null;

        DB::transaction(function () use ($instituicao, $schoolName, $service) {
            $faltasGlobalizadas = is_numeric($this->faltas_globalizadas) ? $this->faltas_globalizadas : null;
            $attributes = $this->buildHistoricoAttributes($instituicao, $schoolName, $faltasGlobalizadas);

            $historicoEscolar = LegacySchoolHistory::create(array_merge($attributes, [
                'ref_cod_aluno' => $this->ref_cod_aluno,
                'sequencial' => $service->getNextSequencial($this->ref_cod_aluno),
                'ref_usuario_cad' => $this->pessoa_logada,
            ]));

            $this->createDisciplines($historicoEscolar);
        });

        $this->mensagem = 'Cadastro efetuado com sucesso.<br>';
        $this->simpleRedirect("educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        $service = app(SchoolHistoryService::class);
        $instituicao = LegacyInstitution::active()->first();
        $schoolName = is_numeric($this->ref_cod_escola) ? $service->getSchoolName($this->ref_cod_escola) : null;
        $faltasGlobalizadas = $this->cb_faltas_globalizadas === 'on' && is_numeric($this->faltas_globalizadas) ? $this->faltas_globalizadas : null;

        DB::transaction(function () use ($instituicao, $faltasGlobalizadas, $schoolName) {
            $attributes = $this->buildHistoricoAttributes($instituicao, $schoolName, $faltasGlobalizadas);

            $historicoEscolar = LegacySchoolHistory::forStudentSequential($this->ref_cod_aluno, $this->sequencial)->firstOrFail();

            $historicoEscolar->update(array_merge($attributes, [
                'ref_usuario_exc' => $this->pessoa_logada,
                'data_exclusao' => now(),
            ]));

            if ($this->nm_disciplina) {
                $historicoEscolar->disciplines()->delete();
                $this->createDisciplines($historicoEscolar);
            }
        });

        $this->mensagem = 'Edição efetuada com sucesso.<br>';
        $this->simpleRedirect("educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_excluir(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        $historicoEscolar = LegacySchoolHistory::forStudentSequential($this->ref_cod_aluno, $this->sequencial)->firstOrFail();

        DB::transaction(function () use ($historicoEscolar) {
            $historicoEscolar->update([
                'ref_usuario_exc' => $this->pessoa_logada,
                'ativo' => 0,
                'data_exclusao' => now(),
            ]);
            $historicoEscolar->disciplines()->delete();
        });

        $this->mensagem = 'Exclusão efetuada com sucesso.<br>';
        $this->simpleRedirect("educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
    }

    private function buildHistoricoAttributes($instituicao, ?string $schoolName, $faltasGlobalizadas): array
    {
        return [
            'nm_serie' => $this->nm_serie,
            'ano' => $this->ano,
            'carga_horaria' => is_numeric($this->carga_horaria) ? $this->carga_horaria : null,
            'dias_letivos' => is_numeric($this->dias_letivos) ? $this->dias_letivos : null,
            'escola' => $schoolName ?? $this->escola,
            'escola_cidade' => $this->escola_cidade ?: $instituicao?->cidade,
            'escola_uf' => $this->escola_uf ?: $instituicao?->ref_sigla_uf,
            'observacao' => $this->observacao,
            'aprovado' => $this->aprovado,
            'ativo' => 1,
            'faltas_globalizadas' => $faltasGlobalizadas,
            'ref_cod_instituicao' => $this->ref_cod_instituicao ?: $instituicao?->cod_instituicao,
            'origem' => 1,
            'extra_curricular' => is_null($this->extra_curricular) ? 0 : 1,
            'frequencia' => str_contains($this->frequencia ?? '', ',')
                ? str_replace(',', '.', str_replace('.', '', $this->frequencia))
                : $this->frequencia,
            'registro' => $this->registro,
            'livro' => $this->livro,
            'folha' => $this->folha,
            'nm_curso' => $this->nm_curso,
            'historico_grade_curso_id' => is_numeric($this->historico_grade_curso_id) ? $this->historico_grade_curso_id : null,
            'aceleracao' => is_null($this->aceleracao) ? 0 : 1,
            'ref_cod_escola' => is_numeric($this->ref_cod_escola) ? $this->ref_cod_escola : null,
            'dependencia' => !is_null($this->dependencia),
            'posicao' => is_numeric($this->posicao) ? $this->posicao : null,
        ];
    }

    private function createDisciplines(LegacySchoolHistory $historicoEscolar): void
    {
        if (!$this->nm_disciplina) {
            return;
        }

        foreach ($this->nm_disciplina as $key => $disciplina) {
            $faltas = $this->faltas[$key] ?? null;
            $ordenamento = $this->ordenamento[$key] ?? null;
            $cargaHoraria = $this->carga_horaria_disciplina[$key] ?? null;

            LegacySchoolHistoryDiscipline::create([
                'historico_escolar_id' => $historicoEscolar->id,
                'ref_ref_cod_aluno' => $this->ref_cod_aluno,
                'ref_sequencial' => $historicoEscolar->sequencial,
                'sequencial' => $key + 1,
                'nm_disciplina' => $disciplina,
                'nota' => $this->nota[$key] ?? '',
                'faltas' => is_numeric($faltas) ? $faltas : null,
                'ordenamento' => is_numeric($ordenamento) ? $ordenamento : null,
                'carga_horaria_disciplina' => is_numeric($cargaHoraria) ? (int) $cargaHoraria : null,
                'dependencia' => ($this->disciplinaDependencia[$key] ?? '') == 'on',
                'tipo_base' => $this->tipo_base[$key] ?? 1,
            ]);
        }
    }

    public function habilitaCargaHoraria($instituicao)
    {
        $obj_instituicao = new clsPmieducarInstituicao($instituicao);
        $detalhe_instituicao = $obj_instituicao->detalhe();

        return dbBool($detalhe_instituicao['permitir_carga_horaria']);
    }

    public function getOpcoesGradeCurso()
    {
        $db = new clsBanco;
        $sql = 'select * from pmieducar.historico_grade_curso where ativo = 1';
        $db->Consulta($sql);

        $opcoes = ['' => 'Selecione'];
        while ($db->ProximoRegistro()) {
            $record = $db->Tupla();
            $opcoes[$record['id']] = $record['descricao_etapa'];
        }

        return $opcoes;
    }

    public function validaControlePosicaoHistorico()
    {
        $obj = new clsPmieducarInstituicao;
        // Busca instituicao ativa
        $lst = $obj->lista(int_ativo: 1);

        return dbBool($lst[0]['controlar_posicao_historicos']);
    }

    public function Formular()
    {
        $this->title = 'Histórico Escolar';
        $this->processoAp = '578';
    }
};
