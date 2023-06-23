<?php

use App\Models\Country;
use App\Models\LegacyInstitution;
use App\Models\State;

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

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        if (is_numeric($this->ref_cod_aluno) && is_numeric($this->sequencial)) {
            $obj = new clsPmieducarHistoricoEscolar(ref_cod_aluno: $this->ref_cod_aluno, sequencial: $this->sequencial);
            $registro = $obj->detalhe();

            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
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
        if (isset($_GET['ref_cod_aluno'], $_GET['sequencial'])) {
            $objCodNomeEscola = new clsPmieducarHistoricoEscolar(ref_cod_aluno: $_GET['ref_cod_aluno'], sequencial: $_GET['sequencial']);
            $registro = $objCodNomeEscola->detalhe();

            if ($registro) {
                $nomeEscola = $registro['escola'];
                $codigoEscola = $registro['ref_cod_escola'];
            }
        }

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

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista(int_cod_aluno: $this->ref_cod_aluno, int_ativo: 1);

        if (is_array($lst_aluno)) {
            $det_aluno = array_shift($lst_aluno);
            $this->nm_aluno = $det_aluno['nome_aluno'];
            $this->campoRotulo(nome: 'nm_aluno', campo: 'Aluno', valor: $this->nm_aluno);
        }

        $obj_nivelUser = new clsPermissoes();
        $user_nivel = $obj_nivelUser->nivel_acesso($this->pessoa_logada);

        if ($user_nivel != App_Model_NivelTipoUsuario::POLI_INSTITUCIONAL) {
            $obj_permissoes = new clsPermissoes();
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
            $this->campoNumero(nome: 'posicao', campo: 'Posição', valor: $this->posicao, tamanhovisivel: 1, tamanhomaximo: 1, obrigatorio: true, descricao: 'Informe a coluna equivalente a série/ano/etapa a qual o histórico pertence. Ex.: 1º ano informe 1, 2º ano informe 2');
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

        $opcoes = [
            '' => 'Selecione',
            1 => 'Aprovado',
            2 => 'Reprovado',
            3 => 'Cursando',
            4 => 'Transferido',
            5 => 'Reclassificado',
            6 => 'Abandono',
            12 => 'Aprovado com dependência',
            13 => 'Aprovado pelo conselho',
            14 => 'Reprovado por faltas',
        ];

        $this->campoLista(nome: 'aprovado', campo: 'Situação', valor: $opcoes, default: $this->aprovado);
        $this->campoTexto(nome: 'registro', campo: 'Registro (arquivo)', valor: $this->registro, tamanhovisivel: 30, tamanhomaximo: 50);
        $this->campoTexto(nome: 'livro', campo: 'Livro', valor: $this->livro, tamanhovisivel: 30, tamanhomaximo: 50);
        $this->campoTexto(nome: 'folha', campo: 'Folha', valor: $this->folha, tamanhovisivel: 30, tamanhomaximo: 50);

        //---------------------INCLUI DISCIPLINAS---------------------//
        $this->campoQuebra();

        if (is_numeric($this->ref_cod_aluno) && is_numeric($this->sequencial) && !$_POST) {
            $obj = new clsPmieducarHistoricoDisciplinas();
            $obj->setOrderby('nm_disciplina ASC');
            $registros = $obj->lista(int_ref_ref_cod_aluno: $this->ref_cod_aluno, int_ref_sequencial: $this->sequencial);
            $qtd_disciplinas = 0;

            if ($registros) {
                foreach ($registros as $campo) {
                    $this->historico_disciplinas[$qtd_disciplinas][] = $campo['nm_disciplina'];
                    $this->historico_disciplinas[$qtd_disciplinas][] = $campo['tipo_base'];
                    $this->historico_disciplinas[$qtd_disciplinas][] = $campo['nota'];
                    $this->historico_disciplinas[$qtd_disciplinas][] = $campo['faltas'];
                    $this->historico_disciplinas[$qtd_disciplinas][] = $campo['carga_horaria_disciplina'];
                    $this->historico_disciplinas[$qtd_disciplinas][] = $campo['ordenamento'];
                    $this->historico_disciplinas[$qtd_disciplinas][] = dbBool($campo['dependencia']) ? 1 : 0;
                    $this->historico_disciplinas[$qtd_disciplinas][] = $campo['sequencial'];
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

        //---------------------FIM INCLUI DISCIPLINAS---------------------//

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
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        $this->carga_horaria = is_numeric($this->carga_horaria) ? intval($this->carga_horaria) : $this->carga_horaria;
        $this->frequencia = $this->fixupFrequencia($this->frequencia);
        $this->extra_curricular = is_null($this->extra_curricular) ? 0 : 1;

        $instituicao = $instituicao = LegacyInstitution::active()->first();

        $obj = new clsPmieducarHistoricoEscolar(
            ref_cod_aluno: $this->ref_cod_aluno,
            ref_usuario_cad: $this->pessoa_logada,
            nm_serie: $this->nm_serie,
            ano: $this->ano,
            carga_horaria: $this->carga_horaria,
            dias_letivos: $this->dias_letivos,
            escola: mb_strtoupper($this->escola),
            escola_cidade: mb_strtoupper($this->escola_cidade ?: $instituicao?->cidade),
            escola_uf: $this->escola_uf ?: $instituicao?->ref_sigla_uf,
            observacao: $this->observacao,
            aprovado: $this->aprovado,
            ativo: 1,
            faltas_globalizadas: $this->faltas_globalizadas,
            ref_cod_instituicao: $this->ref_cod_instituicao ?: $instituicao?->cod_instituicao,
            origem: 1,
            extra_curricular: $this->extra_curricular,
            frequencia: $this->frequencia,
            registro: $this->registro,
            livro: $this->livro,
            folha: $this->folha,
            nm_curso: $this->nm_curso,
            historico_grade_curso_id: $this->historico_grade_curso_id,
            aceleracao: $this->aceleracao,
            ref_cod_escola: $this->ref_cod_escola,
            dependencia: !is_null($this->dependencia),
            posicao: $this->posicao
        );
        $cadastrou = $obj->cadastra();

        if ($cadastrou) {

            //--------------CADASTRA DISCIPLINAS--------------//
            if ($this->nm_disciplina) {
                $sequencial = 1;

                foreach ($this->nm_disciplina as $key => $disciplina) {
                    $obj_historico = new clsPmieducarHistoricoEscolar();
                    $this->sequencial = $obj_historico->getMaxSequencial($this->ref_cod_aluno);

                    $obj = new clsPmieducarHistoricoDisciplinas(sequencial: $sequencial, ref_ref_cod_aluno: $this->ref_cod_aluno, ref_sequencial: $this->sequencial, nm_disciplina: $disciplina, nota: $this->nota[$key], faltas: $this->faltas[$key], ordenamento: $this->ordenamento[$key], carga_horaria_disciplina: $this->carga_horaria_disciplina[$key], dependencia: $this->disciplinaDependencia[$key] == 'on' ? true : false, tipo_base: $this->tipo_base[$key]);
                    $cadastrou1 = $obj->cadastra();

                    if (!$cadastrou1) {
                        $this->mensagem = 'Cadastro não realizado.<br>';

                        return false;
                    }

                    $sequencial++;
                }

                $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
                $this->simpleRedirect("educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
            }
            //--------------FIM CADASTRA DISCIPLINAS--------------//
        }
        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        $this->carga_horaria = is_numeric($this->carga_horaria) ? (int) $this->carga_horaria : $this->carga_horaria;
        $this->frequencia = $this->fixupFrequencia($this->frequencia);

        $faltasGlobalizadas = $this->faltas_globalizadas;

        if ($this->cb_faltas_globalizadas !== 'on') {
            $faltasGlobalizadas = 'NULL';
        }

        $this->aceleracao = is_null($this->aceleracao) ? 0 : 1;
        $this->extra_curricular = is_null($this->extra_curricular) ? 0 : 1;

        $instituicao = LegacyInstitution::active()->first();

        $obj = new clsPmieducarHistoricoEscolar(
            ref_cod_aluno: $this->ref_cod_aluno,
            sequencial: $this->sequencial,
            ref_usuario_exc: $this->pessoa_logada,
            nm_serie: $this->nm_serie,
            ano: $this->ano,
            carga_horaria: $this->carga_horaria,
            dias_letivos: $this->dias_letivos,
            escola: mb_strtoupper($this->escola),
            escola_cidade: mb_strtoupper($this->escola_cidade ?: $instituicao?->cidade),
            escola_uf: $this->escola_uf ?: $instituicao?->ref_sigla_uf,
            observacao: $this->observacao,
            aprovado: $this->aprovado,
            ativo: 1,
            faltas_globalizadas: $faltasGlobalizadas,
            ref_cod_instituicao: $this->ref_cod_instituicao ?: $instituicao?->cod_instituicao,
            origem: 1,
            extra_curricular: $this->extra_curricular,
            frequencia: $this->frequencia,
            registro: $this->registro,
            livro: $this->livro,
            folha: $this->folha,
            nm_curso: $this->nm_curso,
            historico_grade_curso_id: $this->historico_grade_curso_id,
            aceleracao: $this->aceleracao,
            ref_cod_escola: $this->ref_cod_escola,
            dependencia: !is_null($this->dependencia),
            posicao: $this->posicao
        );

        $editou = $obj->edita();

        if ($editou) {

            //--------------EDITA DISCIPLINAS--------------//
            if ($this->nm_disciplina) {
                $obj = new clsPmieducarHistoricoDisciplinas();
                $excluiu = $obj->excluirTodos(ref_cod_aluno: $this->ref_cod_aluno, ref_sequencial: $this->sequencial);
                if ($excluiu) {
                    $sequencial = 1;
                    foreach ($this->nm_disciplina as $key => $disciplina) {
                        //$campo['nm_disciplina_'] = urldecode($campo['nm_disciplina_']);

                        $obj = new clsPmieducarHistoricoDisciplinas(sequencial: $sequencial, ref_ref_cod_aluno: $this->ref_cod_aluno, ref_sequencial: $this->sequencial, nm_disciplina: $disciplina, nota: $this->nota[$key], faltas: $this->faltas[$key], ordenamento: $this->ordenamento[$key], carga_horaria_disciplina: $this->carga_horaria_disciplina[$key], dependencia: $this->disciplinaDependencia[$key] == 'on' ? true : false, tipo_base: $this->tipo_base[$key]);
                        $cadastrou1 = $obj->cadastra();
                        if (!$cadastrou1) {
                            $this->mensagem = 'Cadastro não realizado.<br>';

                            return false;
                        }
                        $sequencial++;
                    }
                }
                $this->mensagem .= 'Edição efetuada com sucesso.<br>';
                $this->simpleRedirect("educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
            }
            //--------------FIM EDITA DISCIPLINAS--------------//
        }
        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        $obj = new clsPmieducarHistoricoEscolar(ref_cod_aluno: $this->ref_cod_aluno, sequencial: $this->sequencial, ref_usuario_exc: $this->pessoa_logada, ref_usuario_cad: null, nm_serie: null, ano: null, carga_horaria: null, dias_letivos: null, escola: null, escola_cidade: null, escola_uf: null, observacao: null, aprovado: null, data_cadastro: null, data_exclusao: null, ativo: 0);

        $excluiu = $obj->excluir();
        if ($excluiu) {
            $obj = new clsPmieducarHistoricoDisciplinas();
            $excluiu = $obj->excluirTodos(ref_cod_aluno: $this->ref_cod_aluno, ref_sequencial: $this->sequencial);
            if ($excluiu) {
                $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
                $this->simpleRedirect("educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
            }
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    protected function fixupFrequencia($frequencia)
    {
        if (strpos(haystack: $frequencia, needle: ',')) {
            $frequencia = str_replace(search: '.', replace: '', subject: $frequencia);
            $frequencia = str_replace(search: ',', replace: '.', subject: $frequencia);
        }

        return $frequencia;
    }

    public function habilitaCargaHoraria($instituicao)
    {
        $obj_instituicao = new clsPmieducarInstituicao($instituicao);
        $detalhe_instituicao = $obj_instituicao->detalhe();

        return dbBool($detalhe_instituicao['permitir_carga_horaria']);
    }

    public function getOpcoesGradeCurso()
    {
        $db = new clsBanco();
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
        //Busca instituicao ativa
        $lst = $obj->lista(int_ativo: 1);

        return dbBool($lst[0]['controlar_posicao_historicos']);
    }

    public function Formular()
    {
        $this->title = 'Histórico Escolar';
        $this->processoAp = '578';
    }
};
