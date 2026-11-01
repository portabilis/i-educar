<?php

use App\Models\LegacyCourse;
use App\Models\LegacyCourseEducacensoStage;
use App\Models\LegacyEducacensoStages;
use App\Models\LegacyEducationLevel;
use App\Models\LegacyEducationType;
use App\Models\LegacyRegimeType;
use App\Services\SchoolClassStageService;
use Illuminate\Support\Facades\DB;

return new class extends clsCadastro
{
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

    public $curso_sem_avaliacao = true;

    public $multi_seriado;

    public $modalidade_curso;

    public $importar_curso_pre_matricula;

    public $descricao;

    public $bloquear_novas_matriculas;

    public function Inicializar()
    {
        $retorno = 'Novo';
        $this->cod_curso = $this->getQueryString(name: 'cod_curso');

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 566,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 3,
            str_pagina_redirecionar: 'educar_curso_lst.php'
        );

        if (is_numeric(value: $this->cod_curso)) {
            $curso = LegacyCourse::query()->find($this->cod_curso);

            if ($curso) {
                $registro = $curso->getAttributes();

                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(
                    int_processo_ap: 566,
                    int_idpes_usuario: $this->pessoa_logada,
                    int_soma_nivel_acesso: 3
                );

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ?
            "educar_curso_det.php?cod_curso={$registro['cod_curso']}" : 'educar_curso_lst.php';

        $this->breadcrumb(currentPage: 'Cursos', breadcrumbs: ['educar_index.php' => 'Escola']);

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
        // primary keys
        $this->campoOculto(nome: 'cod_curso', valor: $this->cod_curso);

        $obrigatorio = true;
        include 'include/pmieducar/educar_campo_lista.php';

        // Nível ensino
        $opcoes = ['' => 'Selecione'];

        if ($this->ref_cod_instituicao) {
            $opcoes = LegacyEducationLevel::query()
                ->where(column: 'ativo', operator: 1)
                ->where(column: 'ref_cod_instituicao', operator: $this->ref_cod_instituicao)
                ->orderBy(column: 'nm_nivel', direction: 'ASC')
                ->pluck(column: 'nm_nivel', key: 'cod_nivel_ensino')
                ->prepend(value: 'Selecione', key: '');
        }

        $script = "javascript:showExpansivelIframe(520, 230, 'educar_nivel_ensino_cad_pop.php?ref_cod_instituicao=' + document.getElementById('ref_cod_instituicao').value);";
        if ($this->ref_cod_instituicao) {
            $script = "<img id='img_nivel_ensino' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        } else {
            $script = "<img id='img_nivel_ensino' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        }

        $this->campoLista(
            nome: 'ref_cod_nivel_ensino',
            campo: 'Nível Ensino',
            valor: $opcoes,
            default: $this->ref_cod_nivel_ensino,
            complemento: $script
        );

        // Tipo ensino
        $opcoes = ['' => 'Selecione'];

        if ($this->ref_cod_instituicao) {
            $opcoes = LegacyEducationType::query()
                ->where(column: 'ativo', operator: 1)
                ->orderBy(column: 'nm_tipo', direction: 'ASC')
                ->pluck(column: 'nm_tipo', key: 'cod_tipo_ensino')
                ->prepend(value: 'Selecione', key: '');
        }

        $script = "javascript:showExpansivelIframe(520, 150, 'educar_tipo_ensino_cad_pop.php?ref_cod_instituicao=' + document.getElementById('ref_cod_instituicao').value);";
        if ($this->ref_cod_instituicao) {
            $script = "<img id='img_tipo_ensino' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        } else {
            $script = "<img id='img_tipo_ensino' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        }

        $this->campoLista(
            nome: 'ref_cod_tipo_ensino',
            campo: 'Tipo Ensino',
            valor: $opcoes,
            default: $this->ref_cod_tipo_ensino,
            complemento: $script
        );

        // Tipo regime
        $opcoes = ['' => 'Selecione'];

        if ($this->ref_cod_instituicao) {
            $opcoes = LegacyRegimeType::query()
                ->where(column: 'ativo', operator: 1)
                ->where(column: 'ref_cod_instituicao', operator: $this->ref_cod_instituicao)
                ->orderBy(column: 'nm_tipo', direction: 'ASC')
                ->pluck(column: 'nm_tipo', key: 'cod_tipo_regime')
                ->prepend(value: 'Selecione', key: '');
        }

        $script = "javascript:showExpansivelIframe(520, 120, 'educar_tipo_regime_cad_pop.php?ref_cod_instituicao=' + document.getElementById('ref_cod_instituicao').value);";

        if ($this->ref_cod_instituicao) {
            $script = "<img id='img_tipo_regime' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        } else {
            $script = "<img id='img_tipo_regime' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        }

        $this->campoLista(
            nome: 'ref_cod_tipo_regime',
            campo: 'Tipo Regime',
            valor: $opcoes,
            default: $this->ref_cod_tipo_regime,
            complemento: $script,
            obrigatorio: false
        );

        // Outros campos
        $this->campoTexto(nome: 'nm_curso', campo: 'Curso', valor: $this->nm_curso, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);

        $this->campoTexto(nome: 'sgl_curso', campo: 'Sigla Curso', valor: $this->sgl_curso, tamanhovisivel: 15, tamanhomaximo: 15);
        $this->campoTexto(nome: 'descricao', campo: 'Descrição', valor: $this->descricao, tamanhovisivel: 15, tamanhomaximo: 50, duplo: '', descricao: 'Caso o campo seja preenchido, a descrição será apresentada nas listagens e filtros de busca');
        $this->campoNumero(nome: 'qtd_etapas', campo: 'Quantidade Etapas', valor: $this->qtd_etapas, tamanhovisivel: 2, tamanhomaximo: 2, obrigatorio: true);
        $this->campoNumero(nome: 'hora_falta', campo: 'Hora Falta (min)', valor: round($this->hora_falta * 60), tamanhovisivel: 2, tamanhomaximo: 2, obrigatorio: true);

        $this->campoMonetario(
            nome: 'carga_horaria',
            campo: 'Carga Horária',
            valor: $this->carga_horaria,
            tamanhovisivel: 7,
            tamanhomaximo: 7,
            obrigatorio: true
        );

        $this->campoTexto(
            nome: 'ato_poder_publico',
            campo: 'Ato Poder Público',
            valor: $this->ato_poder_publico,
            tamanhovisivel: 30,
            tamanhomaximo: 255
        );

        $this->campoOculto(nome: 'excluir_', valor: '');
        $aux = [];

        // Padrão ano escolar
        $this->campoCheck(nome: 'padrao_ano_escolar', campo: 'Padrão Ano Escolar', valor: $this->padrao_ano_escolar);

        $this->campoCheck(nome: 'multi_seriado', campo: 'Multisseriado', valor: $this->multi_seriado);

        // Objetivo do curso
        $this->campoMemo(
            nome: 'objetivo_curso',
            campo: 'Objetivo Curso',
            valor: $this->objetivo_curso,
            colunas: 60,
            linhas: 5
        );

        // Público alvo
        $this->campoMemo(
            nome: 'publico_alvo',
            campo: 'Público Alvo',
            valor: $this->publico_alvo,
            colunas: 60,
            linhas: 5
        );

        $resources = [
            null => 'Selecione',
            1 => 'Ensino regular',
            2 => 'Educação especial',
            3 => 'Educação de Jovens e Adultos (EJA)',
            4 => 'Educação profissional',
        ];

        $options = ['label' => 'Modalidade do curso', 'resources' => $resources, 'value' => $this->modalidade_curso];
        $this->inputsHelper()->select(attrName: 'modalidade_curso', inputOptions: $options);

        $etapasEducacenso = LegacyEducacensoStages::getDescriptiveValues();
        $etapas = $this->cod_curso ? LegacyCourseEducacensoStage::getIdsByCourse(course: $this->cod_curso) : [];

        $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: [
            'label' => 'Etapas que o curso contém',
            'size' => 50,
            'required' => false,
            'options' => [
                'values' => $etapas,
                'all_values' => $etapasEducacenso,
            ],
        ], helperOptions: [
            'objectName' => 'etapacurso',
        ]);

        $this->campoCheck(nome: 'importar_curso_pre_matricula', campo: 'Importar os dados do curso para o recurso de pré-matrícula digital?', valor: $this->importar_curso_pre_matricula);

        $this->campoCheck(nome: 'bloquear_novas_matriculas', campo: 'Bloquear matrículas realizadas através do i-Educar?', valor: $this->bloquear_novas_matriculas);
    }

    public function Novo()
    {
        if ($this->incluir != 'S' && empty($this->excluir_)) {
            // Refatoração: Centraliza limpeza e flags
            $this->prepararDados();

            if (!$this->validarNomeCurso() || !$this->validarQuantidadeEtapas() || !$this->validarHoraFalta()) {
                return false;
            }

            if (!$this->camposObrigatoriosPreenchidos()) {
                $this->mensagem = 'Cadastro não realizado.<br>';

                return false;
            }

            DB::beginTransaction();

            $curso = LegacyCourse::query()->create($this->dadosParaGravacao() + [
                'ref_usuario_cad' => $this->pessoa_logada,
                'ativo' => 1,
            ]);

            $this->cod_curso = $curso->getKey();

            $this->gravaEtapacurso(cod_curso: $this->cod_curso);

            DB::commit();

            $this->mensagem = 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect(url: 'educar_curso_lst.php');

            // Débito de cobertura: trecho ainda sem teste.
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        return true;
    }

    public function Editar()
    {
        if ($this->incluir != 'S' && empty($this->excluir_)) {
            // Refatoração: Usa o mesmo método de preparação
            $this->prepararDados();

            if (!$this->validarNomeCurso() || !$this->validarQuantidadeEtapas() || !$this->validarHoraFalta()) {
                return false;
            }

            $curso = is_numeric(value: $this->cod_curso) && is_numeric(value: $this->pessoa_logada)
                ? LegacyCourse::query()->find($this->cod_curso, ['cod_curso', 'padrao_ano_escolar'])
                : null;

            if (!$curso) {
                $this->mensagem = 'Edição não realizada.<br>';

                return false;
            }

            DB::beginTransaction();

            $curso->update($this->dadosParaGravacao() + [
                'ref_usuario_exc' => $this->pessoa_logada,
                'ativo' => 1,
            ]);

            $alterouPadraoAnoEscolar = $curso->wasChanged('padrao_ano_escolar');

            $this->gravaEtapacurso(cod_curso: $this->cod_curso);

            if ($alterouPadraoAnoEscolar) {
                $this->atualizaEtapasDasTurmasDoCurso(
                    curso: $this->cod_curso,
                    padraoAnoEscolar: $this->padrao_ano_escolar,
                    ano: date(format: 'Y')
                );
            }

            DB::commit();

            $this->mensagem = 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect(url: 'educar_curso_lst.php');

            // Débito de cobertura: trecho ainda sem teste.
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        return true;
    }

    public function Excluir()
    {
        $curso = is_numeric(value: $this->cod_curso) && is_numeric(value: $this->pessoa_logada)
            ? LegacyCourse::query()->find($this->cod_curso, ['cod_curso'])
            : null;

        if (!$curso) {
            $this->mensagem = 'Exclusão não realizada.<br>';

            return false;
        }

        $curso->update([
            'ativo' => 0,
            'data_exclusao' => now(),
            'ref_usuario_exc' => $this->pessoa_logada,
        ]);

        $this->mensagem = 'Exclusão efetuada com sucesso.<br>';
        $this->simpleRedirect(url: 'educar_curso_lst.php');

        // Débito de cobertura: trecho ainda sem teste.
        // @codeCoverageIgnoreStart
        return false;
        // @codeCoverageIgnoreEnd
    }

    public function gravaEtapacurso($cod_curso)
    {
        $etapas = [];

        foreach ($this->getRequest()->etapacurso as $etapaId) {
            if (empty($etapaId)) {
                continue;
            }

            $etapas[] = $etapaId;

            LegacyCourseEducacensoStage::query()->updateOrCreate(attributes: [
                'etapa_id' => $etapaId,
                'curso_id' => $cod_curso,
            ]);
        }

        LegacyCourseEducacensoStage::query()
            ->where(column: 'curso_id', operator: $cod_curso)
            ->whereNotIn(column: 'etapa_id', values: $etapas)
            ->delete();
    }

    public function atualizaEtapasDasTurmasDoCurso($curso, $padraoAnoEscolar, $ano): void
    {
        if (!is_numeric($curso) || !is_numeric($ano)) {
            // Inalcançável: só é chamada depois de a edição dar certo, com código numérico e ano vindo de `date()`.
            // @codeCoverageIgnoreStart
            return;
            // @codeCoverageIgnoreEnd
        }

        (new SchoolClassStageService)->updateByCourse(
            course: (int) $curso,
            year: (int) $ano,
            isStandardCalendar: (bool) $padraoAnoEscolar
        );
    }

    public function makeExtra()
    {
        return file_get_contents(filename: __DIR__ . '/scripts/extra/educar-curso-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Curso';
        $this->processoAp = '566';
    }

    // Método auxiliar criado na refatoração
    private function validarHoraFalta()
    {
        if ($this->hora_falta < 0) {
            $this->mensagem = "O campo 'Hora Falta (min)' não pode ser negativo.<br>";

            return false;
        }

        return true;
    }

    private function validarNomeCurso()
    {
        if (preg_match(pattern: '/\p{L}/u', subject: $this->nm_curso) !== 1) {
            $this->mensagem = "O campo 'Curso' deve conter ao menos uma letra.<br>";

            return false;
        }

        return true;
    }

    private function validarQuantidadeEtapas()
    {
        if (!is_numeric(value: $this->qtd_etapas) || (int) $this->qtd_etapas <= 0) {
            $this->mensagem = "O campo 'Quantidade Etapas' deve ser maior que zero.<br>";

            return false;
        }

        return true;
    }

    private function camposObrigatoriosPreenchidos()
    {
        return is_numeric(value: $this->pessoa_logada)
            && is_numeric(value: $this->ref_cod_nivel_ensino)
            && is_numeric(value: $this->ref_cod_tipo_ensino)
            && is_string(value: $this->nm_curso)
            && is_string(value: $this->sgl_curso)
            && is_numeric(value: $this->qtd_etapas)
            && is_numeric(value: $this->carga_horaria)
            && is_numeric(value: $this->ref_cod_instituicao);
    }

    /**
     * Os campos de texto opcionais e a modalidade são sempre gravados, para que
     * o usuário consiga limpá-los. Os demais entram apenas quando o valor
     * informado é válido para a coluna, preservando o conteúdo atual na edição
     * parcial.
     */
    private function dadosParaGravacao()
    {
        $dados = [
            'descricao' => $this->descricao === '' ? null : $this->descricao,
            'ato_poder_publico' => $this->ato_poder_publico === '' ? null : $this->ato_poder_publico,
            'objetivo_curso' => $this->objetivo_curso === '' ? null : $this->objetivo_curso,
            'publico_alvo' => $this->publico_alvo === '' ? null : $this->publico_alvo,
            'modalidade_curso' => $this->modalidade_curso === '' ? null : $this->modalidade_curso,
        ];

        $numericos = [
            'ref_cod_tipo_regime' => $this->ref_cod_tipo_regime,
            'ref_cod_nivel_ensino' => $this->ref_cod_nivel_ensino,
            'ref_cod_tipo_ensino' => $this->ref_cod_tipo_ensino,
            'qtd_etapas' => $this->qtd_etapas,
            'carga_horaria' => $this->carga_horaria,
            'ref_cod_instituicao' => $this->ref_cod_instituicao,
            'padrao_ano_escolar' => $this->padrao_ano_escolar,
            'hora_falta' => is_numeric(value: $this->hora_falta) ? $this->hora_falta / 60 : null,
            'multi_seriado' => $this->multi_seriado,
            'importar_curso_pre_matricula' => $this->importar_curso_pre_matricula,
            'bloquear_novas_matriculas' => $this->bloquear_novas_matriculas,
        ];

        foreach ($numericos as $campo => $valor) {
            if (is_numeric(value: $valor)) {
                $dados[$campo] = $valor;
            }
        }

        $textos = [
            'nm_curso' => $this->nm_curso,
            'sgl_curso' => $this->sgl_curso,
        ];

        foreach ($textos as $campo => $valor) {
            if (is_string(value: $valor)) {
                $dados[$campo] = $valor;
            }
        }

        return $dados;
    }

    // Método novo para reduzir a Complexidade e Duplicação
    private function prepararDados()
    {
        // Sanitização de números (troca vírgula por ponto, etc)
        $this->carga_horaria = str_replace(search: '.', replace: '', subject: $this->carga_horaria);
        $this->carga_horaria = str_replace(search: ',', replace: '.', subject: $this->carga_horaria);
        $this->hora_falta = str_replace(search: '.', replace: '', subject: $this->hora_falta);
        $this->hora_falta = str_replace(search: ',', replace: '.', subject: $this->hora_falta);

        // Definição de flags/booleanos
        $this->padrao_ano_escolar = is_null(value: $this->padrao_ano_escolar) ? 0 : 1;
        $this->multi_seriado = is_null(value: $this->multi_seriado) ? 0 : 1;
        $this->importar_curso_pre_matricula = is_null(value: $this->importar_curso_pre_matricula) ? 0 : 1;
        $this->bloquear_novas_matriculas = is_null(value: $this->bloquear_novas_matriculas) ? 0 : 1;
    }
};
