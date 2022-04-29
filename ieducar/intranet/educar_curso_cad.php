<?php

use App\Models\LegacyCourseEducacensoStage;
use App\Models\LegacyEducacensoStages;

return new class extends clsCadastro {
    public $pessoa_logada;

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
    public $hora_falta;

    public $incluir;
    public $excluir_;
    public $habilitacao_curso;
    public $curso_sem_avaliacao = true;

    public $multi_seriado;
    public $modalidade_curso;
    public $importar_curso_pre_matricula;
    public $descricao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_curso = $this->getQueryString('cod_curso');

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            566,
            $this->pessoa_logada,
            3,
            'educar_curso_lst.php'
        );

        if (is_numeric($this->cod_curso)) {
            $obj = new clsPmieducarCurso($this->cod_curso);
            $registro = $obj->detalhe();

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(
                    566,
                    $this->pessoa_logada,
                    3
                );

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ?
        "educar_curso_det.php?cod_curso={$registro['cod_curso']}" : 'educar_curso_lst.php';

        $this->breadcrumb('Cursos', ['educar_index.php' => 'Escola']);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }

        if ($_POST['habilitacao_curso']) {
            $this->habilitacao_curso = unserialize(urldecode($_POST['habilitacao_curso']));
        }

        $qtd_habilitacao = (is_array($this->habilitacao_curso) && count($this->habilitacao_curso) == 0) ?
            1 : (is_array($this->habilitacao_curso) && count($this->habilitacao_curso) + 1);

        if (is_numeric($this->cod_curso) && $_POST['incluir'] != 'S' && empty($_POST['excluir_'])) {
            $obj = new clsPmieducarHabilitacaoCurso(null, $this->cod_curso);
            $registros = $obj->lista(null, $this->cod_curso);

            if ($registros) {
                foreach ($registros as $campo) {
                    $this->habilitacao_curso[$campo[$qtd_habilitacao]]['ref_cod_habilitacao_'] = $campo['ref_cod_habilitacao'];

                    $qtd_habilitacao++;
                }
            }
        }

        if ($_POST['habilitacao']) {
            $this->habilitacao_curso[$qtd_habilitacao]['ref_cod_habilitacao_'] = $_POST['habilitacao'];

            $qtd_habilitacao++;
            unset($this->habilitacao);
        }

        // primary keys
        $this->campoOculto('cod_curso', $this->cod_curso);

        $obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');

        // Nível ensino
        $opcoes = [ '' => 'Selecione' ];

        if ($this->ref_cod_instituicao) {
            $objTemp = new clsPmieducarNivelEnsino();
            $lista = $objTemp->lista(
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                1,
                $this->ref_cod_instituicao
            );

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['cod_nivel_ensino']] = $registro['nm_nivel'];
                }
            }
        }

        $script = 'javascript:showExpansivelIframe(520, 230, \'educar_nivel_ensino_cad_pop.php\');';
        if ($this->ref_cod_instituicao) {
            $script = "<img id='img_nivel_ensino' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        } else {
            $script = "<img id='img_nivel_ensino' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        }

        $this->campoLista(
            'ref_cod_nivel_ensino',
            'Nível Ensino',
            $opcoes,
            $this->ref_cod_nivel_ensino,
            '',
            false,
            '',
            $script
        );

        // Tipo ensino
        $opcoes = ['' => 'Selecione'];

        if ($this->ref_cod_instituicao) {
            $objTemp = new clsPmieducarTipoEnsino();
            $objTemp->setOrderby('nm_tipo');
            $lista = $objTemp->lista(
                null,
                null,
                null,
                null,
                null,
                null,
                1,
                $this->ref_cod_instituicao
            );

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['cod_tipo_ensino']] = $registro['nm_tipo'];
                }
            }
        }

        $script = 'javascript:showExpansivelIframe(520, 150, \'educar_tipo_ensino_cad_pop.php\');';
        if ($this->ref_cod_instituicao) {
            $script = "<img id='img_tipo_ensino' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        } else {
            $script = "<img id='img_tipo_ensino' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        }

        $this->campoLista(
            'ref_cod_tipo_ensino',
            'Tipo Ensino',
            $opcoes,
            $this->ref_cod_tipo_ensino,
            '',
            false,
            '',
            $script
        );

        // Tipo regime
        $opcoes = ['' => 'Selecione'];

        if ($this->ref_cod_instituicao) {
            $objTemp = new clsPmieducarTipoRegime();
            $objTemp->setOrderby('nm_tipo');

            $lista = $objTemp->lista(
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                1,
                $this->ref_cod_instituicao
            );

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['cod_tipo_regime']] = $registro['nm_tipo'];
                }
            }
        }

        $script = 'javascript:showExpansivelIframe(520, 120, \'educar_tipo_regime_cad_pop.php\');';

        if ($this->ref_cod_instituicao) {
            $script = "<img id='img_tipo_regime' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        } else {
            $script = "<img id='img_tipo_regime' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        }

        $this->campoLista(
            'ref_cod_tipo_regime',
            'Tipo Regime',
            $opcoes,
            $this->ref_cod_tipo_regime,
            '',
            false,
            '',
            $script,
            false,
            false
        );

        // Outros campos
        $this->campoTexto('nm_curso', 'Curso', $this->nm_curso, 30, 255, true);

        $this->campoTexto('sgl_curso', 'Sigla Curso', $this->sgl_curso, 15, 15, false);
        $this->campoTexto('descricao', 'Descrição', $this->descricao, 15, 50, false,false, '','Caso o campo seja preenchido, a descrição será apresentada nas listagens e filtros de busca');
        $this->campoNumero('qtd_etapas', 'Quantidade Etapas', $this->qtd_etapas, 2, 2, true);

        if (is_numeric($this->hora_falta)) {
            $this->campoMonetario(
                'hora_falta',
                'Hora Falta',
                number_format($this->hora_falta, 2, ',', ''),
                5,
                5,
                false,
                '',
                '',
                ''
            );
        } else {
            $this->campoMonetario(
                'hora_falta',
                'Hora Falta',
                $this->hora_falta,
                5,
                5,
                false,
                '',
                '',
                ''
            );
        }

        $this->campoMonetario(
            'carga_horaria',
            'Carga Horária',
            $this->carga_horaria,
            7,
            7,
            true
        );

        $this->campoTexto(
            'ato_poder_publico',
            'Ato Poder Público',
            $this->ato_poder_publico,
            30,
            255,
            false
        );

        $this->campoOculto('excluir_', '');
        $qtd_habilitacao = 1;
        $aux = [];

        $this->campoQuebra();
        if ($this->habilitacao_curso) {
            foreach ($this->habilitacao_curso as $campo) {
                if ($this->excluir_ == $campo['ref_cod_habilitacao_']) {
                    $this->habilitacao_curso[$campo['ref_cod_habilitacao']] = null;
                    $this->excluir_ = null;
                } else {
                    $obj_habilitacao = new clsPmieducarHabilitacao($campo['ref_cod_habilitacao_']);
                    $obj_habilitacao_det = $obj_habilitacao->detalhe();
                    $nm_habilitacao = $obj_habilitacao_det['nm_tipo'];

                    $this->campoTextoInv(
                        "ref_cod_habilitacao_{$campo['ref_cod_habilitacao_']}",
                        '',
                        $nm_habilitacao,
                        30,
                        255,
                        false,
                        false,
                        false,
                        '',
                        "<a href='#' onclick=\"getElementById('excluir_').value = '{$campo['ref_cod_habilitacao_']}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>"
                    );

                    $aux[$qtd_habilitacao]['ref_cod_habilitacao_'] = $campo['ref_cod_habilitacao_'];

                    $qtd_habilitacao++;
                }
            }

            unset($this->habilitacao_curso);
            $this->habilitacao_curso = $aux;
        }

        $this->campoOculto('habilitacao_curso', serialize($this->habilitacao_curso));

        // Habilitação
        $opcoes = ['' => 'Selecione'];

        if ($this->ref_cod_instituicao) {
            $objTemp = new clsPmieducarHabilitacao();
            $objTemp->setOrderby('nm_tipo');

            $lista = $objTemp->lista(
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                1,
                $this->ref_cod_instituicao
            );

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['cod_habilitacao']] = $registro['nm_tipo'];
                }
            }
        }

        $script = 'javascript:showExpansivelIframe(520, 225, \'educar_habilitacao_cad_pop.php\');';
        $script = "<img id='img_habilitacao' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";

        $this->campoLista(
            'habilitacao',
            'Habilitação',
            $opcoes,
            $this->habilitacao,
            '',
            false,
            '',
            "<a href='#' onclick=\"getElementById('incluir').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>{$script}",
            false,
            false
        );
        $this->campoOculto('incluir', '');
        $this->campoQuebra();

        // Padrão ano escolar
        $this->campoCheck('padrao_ano_escolar', 'Padrão Ano Escolar', $this->padrao_ano_escolar);

        $this->campoCheck('multi_seriado', 'Multisseriado', $this->multi_seriado);

        // Objetivo do curso
        $this->campoMemo(
            'objetivo_curso',
            'Objetivo Curso',
            $this->objetivo_curso,
            60,
            5,
            false
        );

        // Público alvo
        $this->campoMemo(
            'publico_alvo',
            'Público Alvo',
            $this->publico_alvo,
            60,
            5,
            false
        )	;

        $resources = [
            null => 'Selecione',
            1 => 'Ensino regular',
            2 => 'Educação especial',
            3 => 'Educação de Jovens e Adultos (EJA)',
            4 => 'Educação profissional'
        ];

        $options = ['label' => 'Modalidade do curso', 'resources' => $resources, 'value' => $this->modalidade_curso];
        $this->inputsHelper()->select('modalidade_curso', $options);

        $etapasEducacenso = LegacyEducacensoStages::getDescriptiveValues();
        $etapas = $this->cod_curso ? LegacyCourseEducacensoStage::getIdsByCourse($this->cod_curso) : [];

        $this->inputsHelper()->multipleSearchCustom('', [
            'label' => 'Etapas que o curso contém',
            'size' => 50,
            'required' => false,
            'options' => [
                'values' => $etapas,
                'all_values' => $etapasEducacenso
            ]
        ], [
            'objectName'  => 'etapacurso',
        ]);

        $this->campoCheck('importar_curso_pre_matricula', 'Importar os dados do curso para o recurso de pré-matrícula digital?', $this->importar_curso_pre_matricula);
    }

    public function Novo()
    {
        if ($this->habilitacao_curso && $this->incluir != 'S' && empty($this->excluir_)) {
            $this->carga_horaria = str_replace('.', '', $this->carga_horaria);
            $this->carga_horaria = str_replace(',', '.', $this->carga_horaria);
            $this->hora_falta = str_replace('.', '', $this->hora_falta);
            $this->hora_falta = str_replace(',', '.', $this->hora_falta);

            $this->padrao_ano_escolar = is_null($this->padrao_ano_escolar) ? 0 : 1;
            $this->multi_seriado = is_null($this->multi_seriado) ? 0 : 1;
            $this->importar_curso_pre_matricula = is_null($this->importar_curso_pre_matricula) ? 0 : 1;

            $obj = new clsPmieducarCurso(
                null,
                $this->pessoa_logada,
                $this->ref_cod_tipo_regime,
                $this->ref_cod_nivel_ensino,
                $this->ref_cod_tipo_ensino,
                null,
                $this->nm_curso,
                $this->sgl_curso,
                $this->qtd_etapas,
                null,
                null,
                null,
                null,
                $this->carga_horaria,
                $this->ato_poder_publico,
                null,
                $this->objetivo_curso,
                $this->publico_alvo,
                null,
                null,
                1,
                null,
                $this->ref_cod_instituicao,
                $this->padrao_ano_escolar,
                $this->hora_falta,
                null,
                $this->multi_seriado,
                $this->importar_curso_pre_matricula,
                $this->descricao
            );
            $obj->modalidade_curso = $this->modalidade_curso;

            $this->cod_curso = $cadastrou = $obj->cadastra();
            if ($cadastrou) {
                $this->gravaEtapacurso($cadastrou);
                $this->habilitacao_curso = unserialize(urldecode($this->habilitacao_curso));

                if ($this->habilitacao_curso) {
                    foreach ($this->habilitacao_curso as $campo) {
                        $obj = new clsPmieducarHabilitacaoCurso(
                            $campo['ref_cod_habilitacao_'],
                            $cadastrou
                        );

                        $cadastrou2 = $obj->cadastra();

                        if (!$cadastrou2) {
                            $this->mensagem = 'Cadastro não realizado.<br>';

                            return false;
                        }
                    }
                }

                $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
                $this->simpleRedirect('educar_curso_lst.php');
            }

            $this->mensagem = 'Cadastro não realizado.<br>';

            return false;
        }

        return true;
    }

    public function Editar()
    {
        if ($this->habilitacao_curso && $this->incluir != 'S' && empty($this->excluir_)) {
            $this->carga_horaria = str_replace('.', '', $this->carga_horaria);
            $this->carga_horaria = str_replace(',', '.', $this->carga_horaria);
            $this->hora_falta = str_replace('.', '', $this->hora_falta);
            $this->hora_falta = str_replace(',', '.', $this->hora_falta);

            $this->padrao_ano_escolar = is_null($this->padrao_ano_escolar) ? 0 : 1;
            $this->multi_seriado = is_null($this->multi_seriado) ? 0 : 1;
            $this->importar_curso_pre_matricula = is_null($this->importar_curso_pre_matricula) ? 0 : 1;

            $obj = new clsPmieducarCurso(
                $this->cod_curso,
                null,
                $this->ref_cod_tipo_regime,
                $this->ref_cod_nivel_ensino,
                $this->ref_cod_tipo_ensino,
                null,
                $this->nm_curso,
                $this->sgl_curso,
                $this->qtd_etapas,
                null,
                null,
                null,
                null,
                $this->carga_horaria,
                $this->ato_poder_publico,
                null,
                $this->objetivo_curso,
                $this->publico_alvo,
                null,
                null,
                1,
                $this->pessoa_logada,
                $this->ref_cod_instituicao,
                $this->padrao_ano_escolar,
                $this->hora_falta,
                null,
                $this->multi_seriado,
                $this->importar_curso_pre_matricula,
                $this->descricao
            );
            $obj->modalidade_curso = $this->modalidade_curso;

            $detalheAntigo = $obj->detalhe();
            $alterouPadraoAnoEscolar = $detalheAntigo['padrao_ano_escolar'] != $this->padrao_ano_escolar;
            $editou = $obj->edita();
            if ($editou) {
                $this->gravaEtapacurso($this->cod_curso);
                $this->habilitacao_curso = unserialize(urldecode($this->habilitacao_curso));
                $obj  = new clsPmieducarHabilitacaoCurso(null, $this->cod_curso);
                $excluiu = $obj->excluirTodos();

                if ($excluiu) {
                    if ($this->habilitacao_curso) {
                        foreach ($this->habilitacao_curso as $campo) {
                            $obj = new clsPmieducarHabilitacaoCurso(
                                $campo['ref_cod_habilitacao_'],
                                $this->cod_curso
                            );

                            $cadastrou2 = $obj->cadastra();

                            if (!$cadastrou2) {
                                $this->mensagem = 'Edição não realizada.<br>';

                                return false;
                            }
                        }
                    }
                }

                if ($alterouPadraoAnoEscolar) {
                    $this->updateClassStepsForCourse($this->cod_curso, $this->padrao_ano_escolar, date('Y'));
                }

                $this->mensagem .= 'Edição efetuada com sucesso.<br>';
                $this->simpleRedirect('educar_curso_lst.php');
            }

            $this->mensagem = 'Edição não realizada.<br>';

            return false;
        }

        return true;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarCurso(
            $this->cod_curso,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            0,
            $this->pessoa_logada
        );

        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_curso_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function gravaEtapacurso($cod_curso)
    {
        $etapas = [];

        foreach ($this->getRequest()->etapacurso as $etapaId) {
            if (empty($etapaId)) {
                continue;
            }

            $etapas[] = $etapaId;

            LegacyCourseEducacensoStage::query()->updateOrCreate([
                'etapa_id' => $etapaId,
                'curso_id' => $cod_curso,
            ]);
        }

        LegacyCourseEducacensoStage::query()
            ->where('curso_id', $cod_curso)
            ->whereNotIn('etapa_id', $etapas)
            ->delete();
    }

    public function updateClassStepsForCourse($courseCode, $standerdSchoolYear, $currentYear)
    {
        $classStepsObject = new clsPmieducarTurmaModulo();

        $classStepsObject->removeStepsOfClassesForCourseAndYear($courseCode, $currentYear);

        if ($standerdSchoolYear == 0) {
            $classStepsObject->copySchoolStepsIntoClassesForCourseAndYear($courseCode, $currentYear);
        }
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-curso-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Curso';
        $this->processoAp = '566';
    }
};
