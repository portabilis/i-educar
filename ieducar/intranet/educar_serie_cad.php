<?php

return new class extends clsCadastro {
    public $pessoa_logada;

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

    public $regras_ano_letivo;
    public $regras_avaliacao_id;
    public $regras_avaliacao_diferenciada_id;
    public $anos_letivos;

    public $ref_cod_instituicao;

    public $disciplina_serie;
    public $ref_cod_disciplina;
    public $incluir_disciplina;
    public $excluir_disciplina;

    public $idade_inicial;
    public $idade_ideal;
    public $idade_final;

    public $alerta_faixa_etaria;
    public $bloquear_matricula_faixa_etaria;
    public $exigir_inep;
    public $importar_serie_pre_matricula;
    public $descricao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_serie=$_GET['cod_serie'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            583,
            $this->pessoa_logada,
            3,
            'educar_serie_lst.php'
        );

        $this->regras_ano_letivo = [];

        if (is_numeric($this->cod_serie)) {
            $obj = new clsPmieducarSerie($this->cod_serie);
            $registro = $obj->detalhe();

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
                $obj_curso_det = $obj_curso->detalhe();
                $this->ref_cod_instituicao = $obj_curso_det['ref_cod_instituicao'];

                if ($obj->possuiTurmasVinculadas()) {
                    $this->script_excluir = 'excluirSerieComTurmas();';
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(
                    583,
                    $this->pessoa_logada,
                    3
                );

                $retorno = 'Editar';
            }

            $serieAnoMapper = new RegraAvaliacao_Model_SerieAnoDataMapper();
            $regrasSerieAno = [];

            if (!is_null($this->ref_cod_instituicao)) {
                $regrasSerieAno = $serieAnoMapper->findAll(
                    [
                        'regraAvaliacao',
                        'regraAvaliacaoDiferenciada',
                        'serie',
                        'anoLetivo',
                    ],
                    [
                        'serie' => $this->cod_serie
                    ],
                    ['ano_letivo' => 'DESC'],
                    false
                );
            }

            foreach ($regrasSerieAno as $key => $regra) {
                $this->regras_ano_letivo[$key][] = $regra->regraAvaliacao;
                $this->regras_ano_letivo[$key][] = $regra->regraAvaliacaoDiferenciada;
                $this->regras_ano_letivo[$key][] = $regra->anoLetivo;
            }
        }

        $this->url_cancelar = ($retorno == 'Editar')
            ? "educar_serie_det.php?cod_serie={$registro['cod_serie']}"
            : 'educar_serie_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' série', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        $this->alerta_faixa_etaria = dbBool($this->alerta_faixa_etaria);
        $this->bloquear_matricula_faixa_etaria = dbBool($this->bloquear_matricula_faixa_etaria);
        $this->exigir_inep = dbBool($this->exigir_inep);
        $this->importar_serie_pre_matricula = dbBool($this->importar_serie_pre_matricula);

        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }

        // primary keys
        $this->campoOculto('cod_serie', $this->cod_serie);

        $obrigatorio = true;
        $get_curso = true;
        include('include/pmieducar/educar_campo_lista.php');

        $this->campoTexto('nm_serie', 'Série', $this->nm_serie, 30, 255, true);
        $this->campoTexto('descricao', 'Descrição', $this->descricao, 30, 50, false,false, '','Caso o campo seja preenchido, a descrição será apresentada nas listagens e filtros de busca');

        $opcoes = ['' => 'Selecione'];

        if ($this->ref_cod_curso) {
            $objTemp = new clsPmieducarCurso();
            $lista = $objTemp->lista(
                $this->ref_cod_curso,
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
                null,
                null,
                1
            );

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes_["{$registro['cod_curso']}"] = "{$registro['qtd_etapas']}";
                }
            }

            for ($i=1; $i <= $opcoes_["{$registro['cod_curso']}"]; $i++) {
                $opcoes[$i] = "Etapa {$i}";
            }
        }

        $this->campoLista('etapa_curso', 'Etapa Curso', $opcoes, $this->etapa_curso);

        // Regra de avaliação
        $mapper = new RegraAvaliacao_Model_RegraDataMapper();
        $regras = [];

        // @TODO entender como funciona a tabela para poder popular os campos de regra
        // baseado na instituição escolhida
        $regras = $mapper->findAll([], []);
        $regras = CoreExt_Entity::entityFilterAttr($regras, 'id', 'nome');

        $regras = ['' => 'Selecione'] + $regras;

        $this->campoTabelaInicio('regras', 'Regras de avaliação', ['Regra de avaliação (padrão)','Regra de avaliação (alternativa)<br><font size=-1; color=gray>O campo deve ser preenchido se existirem escolas avaliadas de forma alternativa (ex.: escola rural, indígena, etc)</font>', 'Ano escolar'], $this->regras_ano_letivo);
        $this->campoLista('regras_avaliacao_id', 'Regra de avaliação (padrão)', $regras, $this->regras_avaliacao_id);
        $this->campoLista('regras_avaliacao_diferenciada_id', 'Regra de avaliação (alternativa)', $regras, $this->regras_avaliacao_difer>enciada_id, '', false, 'Será utilizada quando campo <b>Utilizar regra de avaliação diferenciada</b> estiver marcado no cadastro da escola', '', false, false);
        $this->campoNumero('anos_letivos', 'Ano letivo', $this->anos_letivos, 4, 4, true);
        $this->campoTabelaFim();

        $opcoes = ['' => 'Selecione', 1 => 'não', 2 => 'sim'];

        $this->campoLista('concluinte', 'Concluinte', $opcoes, $this->concluinte);

        $this->campoMonetario('carga_horaria', 'Carga Horária', $this->carga_horaria, 7, 7, true);

        $this->campoNumero('dias_letivos', 'Dias letivos', $this->dias_letivos, 10, 10, true);

        $this->campoNumero('idade_ideal', 'Idade padrão', $this->idade_ideal, 2, 2, false);

        $this->campoNumero(
            'idade_inicial',
            'Faixa etária',
            $this->idade_inicial,
            2,
            2,
            false,
            '',
            '',
            false,
            false,
            true
        );

        $this->campoNumero('idade_final', '&nbsp;até', $this->idade_final, 2, 2, false);

        $this->campoMemo('observacao_historico', 'Observação histórico', $this->observacao_historico, 60, 5, false);

        $this->campoCheck('alerta_faixa_etaria', 'Exibir alerta ao tentar matricular alunos fora da faixa etária da série/ano', $this->alerta_faixa_etaria);
        $this->campoCheck('bloquear_matricula_faixa_etaria', 'Bloquear matrículas de alunos fora da faixa etária da série/ano', $this->bloquear_matricula_faixa_etaria);

        $this->campoCheck('exigir_inep', 'Exigir INEP para a matrícula?', $this->exigir_inep);
        $this->campoCheck('importar_serie_pre_matricula', 'Importar os dados da série para o recurso de pré-matrícula online?', $this->importar_serie_pre_matricula);
    }

    public function Novo()
    {
        $this->carga_horaria = str_replace('.', '', $this->carga_horaria);
        $this->carga_horaria = str_replace(',', '.', $this->carga_horaria);

        $obj = new clsPmieducarSerie(
            null,
            null,
            $this->pessoa_logada,
            $this->ref_cod_curso,
            $this->nm_serie,
            $this->etapa_curso,
            $this->concluinte,
            $this->carga_horaria,
            null,
            null,
            1,
            $this->idade_inicial,
            $this->idade_final,
            $this->regra_avaliacao_id,
            $this->observacao_historico,
            $this->dias_letivos,
            $this->regra_avaliacao_diferenciada_id,
            !is_null($this->alerta_faixa_etaria),
            !is_null($this->bloquear_matricula_faixa_etaria),
            $this->idade_ideal,
            !is_null($this->exigir_inep),
            !is_null($this->importar_serie_pre_matricula)
        );

        $this->cod_serie = $cadastrou = $obj->cadastra();

        if ($cadastrou) {
            $this->persisteRegraSerieAno();

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_serie_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $this->carga_horaria = str_replace('.', '', $this->carga_horaria);
        $this->carga_horaria = str_replace(',', '.', $this->carga_horaria);

        $obj = new clsPmieducarSerie(
            $this->cod_serie,
            $this->pessoa_logada,
            null,
            $this->ref_cod_curso,
            $this->nm_serie,
            $this->etapa_curso,
            $this->concluinte,
            $this->carga_horaria,
            null,
            null,
            1,
            $this->idade_inicial,
            $this->idade_final,
            $this->regra_avaliacao_id,
            $this->observacao_historico,
            $this->dias_letivos,
            $this->regra_avaliacao_diferenciada_id,
            !is_null($this->alerta_faixa_etaria),
            !is_null($this->bloquear_matricula_faixa_etaria),
            $this->idade_ideal,
            !is_null($this->exigir_inep),
            !is_null($this->importar_serie_pre_matricula),
            $this->descricao
        );

        $editou = $obj->edita();

        if ($editou) {
            $this->persisteRegraSerieAno();

            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_serie_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarSerie(
            $this->cod_serie,
            $this->pessoa_logada,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            0
        );

        if ($obj->possuiTurmasVinculadas()) {
            $this->mensagem = 'Não foi possível excluir a série, pois a mesma possui turmas vinculadas.';

            return false;
        }

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_serie_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    protected function persisteRegraSerieAno()
    {
        $serieAnoMapper = new RegraAvaliacao_Model_SerieAnoDataMapper();
        $anosParaManter = [];

        foreach ($this->regras_avaliacao_id as $key => $regraAvaliacao) {
            $anosParaManter[] = $this->anos_letivos[$key];
            $dados = [
                'regraAvaliacao' => $regraAvaliacao,
                'regraAvaliacaoDiferenciada' => $this->regras_avaliacao_diferenciada_id[$key],
                'serie' => $this->cod_serie,
                'anoLetivo' => $this->anos_letivos[$key],
            ];

            $entity = $serieAnoMapper->createNewEntityInstance($dados);
            $serieAnoMapper->save($entity);
        }
        $this->deletaRegraSerieAnoNaoEnviada($anosParaManter);
    }

    protected function deletaRegraSerieAnoNaoEnviada(array $anosParaManter)
    {
        $anosParaManter = implode(',', $anosParaManter);
        $serieAnoMapper = new RegraAvaliacao_Model_SerieAnoDataMapper();
        $regrasSerieAnoDeletar = $serieAnoMapper->findAll([
            'regraAvaliacao',
            'regraAvaliacaoDiferenciada',
            'serie',
            'anoLetivo',
        ], [
            'serie' => $this->cod_serie,
            ' not ano_letivo = any(\'{'.$anosParaManter.'}\') '
        ], [], false);

        foreach ($regrasSerieAnoDeletar as $regra) {
            $serieAnoMapper->delete($regra);
        }
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-serie-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Série';
        $this->processoAp = '583';
    }
};
