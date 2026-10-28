<?php

use App\Models\LegacyCourse;
use App\Models\LegacyGrade;
use App\Models\LegacySchoolClass;
use App\Services\GradeService;
use Illuminate\Support\Facades\DB;

return new class extends clsCadastro
{
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

    public $regra_avaliacao_id;

    public $observacao_historico;

    public $dias_letivos;

    public $regra_avaliacao_diferenciada_id;

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

    public $etapa_educacenso;

    private function possuiTurmasVinculadas(): bool
    {
        return LegacySchoolClass::query()
            ->where('ref_ref_cod_serie', $this->cod_serie)
            ->active()
            ->exists();
    }

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_serie = $_GET['cod_serie'];

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(
            583,
            $this->pessoa_logada,
            3,
            'educar_serie_lst.php'
        );

        $this->regras_ano_letivo = [];

        if (is_numeric($this->cod_serie)) {
            $serie = LegacyGrade::query()->find($this->cod_serie);

            if ($serie) {
                $registro = $serie->getAttributes();

                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->carga_horaria = formatDecimalBr($this->carga_horaria) ?? $this->carga_horaria;

                $this->ref_cod_instituicao = LegacyCourse::query()->whereKey($registro['ref_cod_curso'])->value('ref_cod_instituicao');

                if ($this->possuiTurmasVinculadas()) {
                    $this->script_excluir = 'excluirSerieComTurmas();';
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(
                    583,
                    $this->pessoa_logada,
                    3
                );

                $retorno = 'Editar';
            }

            $serieAnoMapper = new RegraAvaliacao_Model_SerieAnoDataMapper;
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
                        'serie' => $this->cod_serie,
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
            ? "educar_serie_det.php?cod_serie={$this->cod_serie}"
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
                $this->$campo = ($this->$campo) ?: $val;
            }
        }

        // primary keys
        $this->campoOculto('cod_serie', $this->cod_serie);

        $obrigatorio = true;
        $get_curso = true;
        include 'include/pmieducar/educar_campo_lista.php';

        $this->campoTexto('nm_serie', 'Série', $this->nm_serie, 30, 255, true);
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
        $opcoes = ['' => 'Selecione'];

        if (is_numeric($this->ref_cod_curso)) {
            $etapas = LegacyCourse::query()
                ->active()
                ->whereKey($this->ref_cod_curso)
                ->value('qtd_etapas');

            for ($i = 1; $i <= $etapas; $i++) {
                $opcoes[$i] = "Etapa {$i}";
            }
        }
        $this->campoLista('etapa_curso', 'Etapa Curso', $opcoes, $this->etapa_curso);
        $opcoes = ['' => 'Selecione', 1 => 'não', 2 => 'sim'];
        $this->campoLista('concluinte', 'Concluinte', $opcoes, $this->concluinte);
        $this->campoTexto('descricao', 'Descrição', $this->descricao, 30, 50, false, false, '', 'Caso o campo seja preenchido, a descrição será apresentada nas listagens e filtros de busca');

        // Regra de avaliação
        $mapper = new RegraAvaliacao_Model_RegraDataMapper;

        // @TODO entender como funciona a tabela para poder popular os campos de regra
        // baseado na instituição escolhida
        $regras = $mapper->findAll([], []);
        $regras = CoreExt_Entity::entityFilterAttr($regras, 'id', 'nome');

        $regras = ['' => 'Selecione'] + $regras;

        $this->campoTabelaInicio('regras', 'Regras de avaliação', ['Regra de avaliação (padrão)', 'Regra de avaliação (alternativa)<br><font size=-1; color=gray>O campo deve ser preenchido se existirem escolas avaliadas de forma alternativa (ex.: escola rural, indígena, etc)</font>', 'Ano escolar'], $this->regras_ano_letivo);
        $this->campoLista('regras_avaliacao_id', 'Regra de avaliação (padrão)', $regras, $this->regras_avaliacao_id);
        $this->campoLista('regras_avaliacao_diferenciada_id', 'Regra de avaliação (alternativa)', $regras, $this->regras_avaliacao_diferenciada_id, '', false, 'Será utilizada quando campo <b>Utilizar regra de avaliação diferenciada</b> estiver marcado no cadastro da escola', '', false, false);
        $this->campoNumero('anos_letivos', 'Ano letivo', $this->anos_letivos, 4, 4, true);
        $this->campoTabelaFim();

        $this->campoRotulo(
            nome: 'autenticador_documentos_digitais',
            campo: '<b>Autenticador de documentos digitais</b>'
        );

        $this->campoMemo('observacao_historico', 'Observação histórico', $this->observacao_historico, 60, 5, false);

        $this->campoCheck('alerta_faixa_etaria', 'Exibir alerta ao tentar matricular alunos fora da faixa etária da série/ano', $this->alerta_faixa_etaria);
        $this->campoCheck('bloquear_matricula_faixa_etaria', 'Bloquear matrículas de alunos fora da faixa etária da série/ano', $this->bloquear_matricula_faixa_etaria);

        $this->campoCheck('exigir_inep', 'Exigir INEP para a matrícula?', $this->exigir_inep);
        $this->campoCheck('importar_serie_pre_matricula', 'Importar os dados da série para o recurso de pré-matrícula online?', $this->importar_serie_pre_matricula);

        $etapas_educacenso = loadJson(file: 'educacenso_json/etapas_ensino.json');
        $etapas_educacenso = array_replace([null => 'Selecione'], $etapas_educacenso);

        $options = ['label' => 'Etapa de ensino', 'resources' => $etapas_educacenso, 'value' => $this->etapa_educacenso, 'required' => false, 'size' => 70];
        $this->inputsHelper()->select(attrName: 'etapa_educacenso', inputOptions: $options);
    }

    public function Novo()
    {
        $this->carga_horaria = parseDecimalBr($this->carga_horaria);

        if ($this->carga_horaria === null) {
            $this->mensagem = 'Carga horária inválida.';

            return false;
        }

        if ($this->regras_avaliacao_id === null) {
            $this->mensagem = 'Regra de avaliação não cadastrada';

            return false;
        }

        $serie = $this->cadastraSerieComRegrasPorAno();

        $this->cod_serie = $cadastrou = $serie?->getKey();

        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_serie_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    private function cadastraSerieComRegrasPorAno(): ?LegacyGrade
    {
        DB::beginTransaction();

        $serie = app(GradeService::class)->create([
            'ref_usuario_cad' => $this->pessoa_logada,
            'ref_cod_curso' => $this->ref_cod_curso,
            'nm_serie' => $this->nm_serie,
            'etapa_curso' => $this->etapa_curso,
            'concluinte' => $this->concluinte,
            'carga_horaria' => $this->carga_horaria,
            'idade_inicial' => $this->idade_inicial,
            'idade_final' => $this->idade_final,
            'regra_avaliacao_id' => $this->regra_avaliacao_id,
            'observacao_historico' => $this->observacao_historico,
            'dias_letivos' => $this->dias_letivos,
            'regra_avaliacao_diferenciada_id' => $this->regra_avaliacao_diferenciada_id,
            'alerta_faixa_etaria' => !is_null($this->alerta_faixa_etaria),
            'bloquear_matricula_faixa_etaria' => !is_null($this->bloquear_matricula_faixa_etaria),
            'idade_ideal' => $this->idade_ideal,
            'exigir_inep' => !is_null($this->exigir_inep),
            'importar_serie_pre_matricula' => !is_null($this->importar_serie_pre_matricula),
            'descricao' => $this->descricao,
            'etapa_educacenso' => $this->etapa_educacenso,
        ]);

        if ($serie === null) {
            DB::rollBack();

            return null;
        }

        $this->cod_serie = $serie->getKey();
        $this->persisteRegraSerieAno();

        DB::commit();

        return $serie;
    }

    public function Editar()
    {
        $this->carga_horaria = parseDecimalBr($this->carga_horaria);

        if ($this->carga_horaria === null) {
            $this->mensagem = 'Carga horária inválida.';

            return false;
        }

        if ($this->regras_avaliacao_id === null) {
            $this->mensagem = 'Regra de avaliação não cadastrada';

            return false;
        }

        if (!is_numeric($this->cod_serie) || !is_numeric($this->pessoa_logada)) {
            $this->mensagem = 'Edição não realizada.<br>';

            return false;
        }

        $editou = $this->editaSerieComRegrasPorAno();

        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_serie_lst.php');
        }

        // Débito de cobertura: trecho ainda sem teste.
        // @codeCoverageIgnoreStart
        $this->mensagem = 'Edição não realizada.<br>';

        return false;
        // @codeCoverageIgnoreEnd
    }

    private function editaSerieComRegrasPorAno(): bool
    {
        DB::beginTransaction();

        $editou = app(GradeService::class)->update($this->cod_serie, [
            'ref_usuario_exc' => $this->pessoa_logada,
            'ref_cod_curso' => $this->ref_cod_curso,
            'nm_serie' => $this->nm_serie,
            'etapa_curso' => $this->etapa_curso,
            'concluinte' => $this->concluinte,
            'carga_horaria' => $this->carga_horaria,
            'ativo' => 1,
            'idade_inicial' => $this->idade_inicial,
            'idade_final' => $this->idade_final,
            'regra_avaliacao_id' => $this->regra_avaliacao_id,
            'observacao_historico' => $this->observacao_historico,
            'dias_letivos' => $this->dias_letivos,
            'regra_avaliacao_diferenciada_id' => $this->regra_avaliacao_diferenciada_id,
            'alerta_faixa_etaria' => !is_null($this->alerta_faixa_etaria),
            'bloquear_matricula_faixa_etaria' => !is_null($this->bloquear_matricula_faixa_etaria),
            'idade_ideal' => $this->idade_ideal,
            'exigir_inep' => !is_null($this->exigir_inep),
            'importar_serie_pre_matricula' => !is_null($this->importar_serie_pre_matricula),
            'descricao' => $this->descricao,
            'etapa_educacenso' => $this->etapa_educacenso,
        ]);

        if (!$editou) {
            // Débito de cobertura: trecho ainda sem teste.
            // @codeCoverageIgnoreStart
            DB::rollBack();

            return false;
            // @codeCoverageIgnoreEnd
        }

        $this->persisteRegraSerieAno();

        DB::commit();

        return true;
    }

    public function Excluir()
    {
        if (!is_numeric($this->cod_serie) || !is_numeric($this->pessoa_logada)) {
            $this->mensagem = 'Exclusão não realizada.<br>';

            return false;
        }

        if ($this->possuiTurmasVinculadas()) {
            $this->mensagem = 'Não foi possível excluir a série, pois a mesma possui turmas vinculadas.';

            return false;
        }

        $excluiu = app(GradeService::class)->delete($this->cod_serie, $this->pessoa_logada);

        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_serie_lst.php');
        }

        // Débito de cobertura: trecho ainda sem teste.
        // @codeCoverageIgnoreStart
        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
        // @codeCoverageIgnoreEnd
    }

    protected function persisteRegraSerieAno()
    {
        $serieAnoMapper = new RegraAvaliacao_Model_SerieAnoDataMapper;
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
        $serieAnoMapper = new RegraAvaliacao_Model_SerieAnoDataMapper;
        $regrasSerieAnoDeletar = $serieAnoMapper->findAll([
            'regraAvaliacao',
            'regraAvaliacaoDiferenciada',
            'serie',
            'anoLetivo',
        ], [
            'serie' => $this->cod_serie,
            ' not ano_letivo = any(\'{'.$anosParaManter.'}\') ',
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
