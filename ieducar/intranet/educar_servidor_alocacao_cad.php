<?php

use App\Models\Employee;
use App\Models\EmployeeAllocation;
use App\Models\LegacyBondType;
use App\Models\LegacyEmployeeRole;
use App\Models\LegacyPerson;
use App\Models\LegacySchool;

return new class extends clsCadastro
{
    private const CARGA_HORARIA_MAXIMA = 36;

    public $pessoa_logada;

    public $cod_servidor_alocacao;

    public $ref_ref_cod_instituicao;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_cod_escola;

    public $ref_cod_servidor;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $carga_horaria_alocada;

    public $carga_horaria_disponivel;

    public $hora_inicial;

    public $hora_final;

    public $hora_atividade;

    public $horas_excedentes;

    public $periodo;

    public $ref_cod_funcionario_vinculo;

    public $ano;

    public $data_admissao;

    public $data_saida;

    public $alocacao_array = [];

    public $alocacao_excluida_array = [];

    public static $escolasPeriodos = [];

    public static $periodos = [];

    public function Inicializar()
    {
        $retorno = 'Novo';

        $ref_cod_servidor = $_GET['ref_cod_servidor'];
        $ref_ref_cod_instituicao = $_GET['ref_cod_instituicao'];
        $cod_servidor_alocacao = $_GET['cod_servidor_alocacao'];

        if (is_numeric($cod_servidor_alocacao)) {
            $this->cod_servidor_alocacao = $cod_servidor_alocacao;

            $servidorAlocacao = EmployeeAllocation::query()->find($this->cod_servidor_alocacao);

            if ($servidorAlocacao) {
                $this->ref_ref_cod_instituicao = $servidorAlocacao['ref_ref_cod_instituicao'];
                $this->ref_cod_servidor = $servidorAlocacao['ref_cod_servidor'];
                $this->ref_cod_escola = $servidorAlocacao['ref_cod_escola'];
                $this->periodo = $servidorAlocacao['periodo'];
                $this->carga_horaria_alocada = $servidorAlocacao['carga_horaria'];
                $this->cod_servidor_funcao = $servidorAlocacao['ref_cod_servidor_funcao'];
                $this->ref_cod_funcionario_vinculo = $servidorAlocacao['ref_cod_funcionario_vinculo'];
                $this->ativo = $servidorAlocacao['ativo'];
                $this->ano = $servidorAlocacao['ano'];
                $this->data_admissao = $servidorAlocacao['data_admissao'];
                $this->data_saida = $servidorAlocacao['data_saida'];
                $this->hora_inicial = $servidorAlocacao['hora_inicial'];
                $this->hora_final = $servidorAlocacao['hora_final'];
                $this->hora_atividade = $servidorAlocacao['hora_atividade'];
                $this->horas_excedentes = $servidorAlocacao['horas_excedentes'];
            }
        } elseif (is_numeric($ref_cod_servidor) && is_numeric($ref_ref_cod_instituicao)) {
            $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
            $this->ref_cod_servidor = $ref_cod_servidor;
            $this->ref_cod_instituicao = $ref_ref_cod_instituicao;
        } else {
            $this->simpleRedirect('educar_servidor_lst.php');
        }

        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(
            635,
            $this->pessoa_logada,
            7,
            'educar_servidor_lst.php'
        );

        if ($obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7)) {
            $this->fexcluir = true;
        }

        $this->url_cancelar = sprintf(
            'educar_servidor_alocacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor,
            $this->ref_ref_cod_instituicao
        );
        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb('Alocar servidor', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $obj_inst = new clsPmieducarInstituicao($this->ref_ref_cod_instituicao);
        $inst_det = $obj_inst->detalhe();

        $this->campoRotulo('nm_instituicao', 'Instituição', $inst_det['nm_instituicao']);
        $this->campoOculto('ref_ref_cod_instituicao', $this->ref_ref_cod_instituicao);
        $this->campoOculto('cod_servidor_alocacao', $this->cod_servidor_alocacao);

        // Dados do servidor
        $objTemp = new clsPmieducarServidor(
            $this->ref_cod_servidor,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_ref_cod_instituicao
        );

        $det = $objTemp->detalhe();

        if ($det) {
            $this->carga_horaria_disponivel = $det['carga_horaria'];
        }

        if ($this->ref_cod_servidor) {
            $nm_servidor = LegacyPerson::whereKey($this->ref_cod_servidor)->value('nome');
        }

        $this->campoRotulo('nm_servidor', 'Servidor', $nm_servidor);

        $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);

        // Carga horária
        $carga = $this->carga_horaria_disponivel;
        $carga = str_pad($carga, 2, 0, STR_PAD_LEFT);
        $this->campoRotulo('carga_horaria_disponivel', 'Carga horária do servidor', $carga . ':00');
        $cargadisponivel = $this->cargaHorariaAlocadaNoAno($this->ano ?: date('Y'));
        $this->campoRotulo('carga_horaria_sem_alocacao', 'Carga horária alocada', substr($cargadisponivel, 0, -3));

        $this->inputsHelper()->integer('ano', ['value' => $this->ano, 'max_length' => 4]);

        $this->inputsHelper()->dynamic('escola');

        // Períodos
        $periodo = [
            1 => 'Matutino',
            2 => 'Vespertino',
            3 => 'Noturno',
        ];

        self::$periodos = $periodo;

        $this->campoLista('periodo', 'Período', $periodo, $this->periodo, null, false, '', '', false, true);

        $options = [
            'label' => 'Data de admissão',
            'placeholder' => 'dd/mm/yyyy',
            'hint' => 'A data deve estar em branco ou dentro do período de datas da exportação para o Educacenso, para o servidor ser exportado.',
            'value' => $this->data_admissao,
            'required' => false,
        ];

        $this->inputsHelper()->date('data_admissao', $options);

        $options = [
            'label' => 'Data de saída',
            'placeholder' => 'dd/mm/yyyy',
            'hint' => 'A data deve estar em branco ou fora do período de datas da exportação para o Educacenso, para o servidor ser exportado.',
            'value' => $this->data_saida,
            'required' => false,
        ];

        $this->inputsHelper()->date('data_saida', $options);

        // Funções
        $lista_funcoes = LegacyEmployeeRole::query()
            ->when(is_numeric($this->ref_ref_cod_instituicao), fn ($q) => $q->whereInstitution($this->ref_ref_cod_instituicao))
            ->when(is_numeric($this->ref_cod_servidor), fn ($q) => $q->whereEmployee($this->ref_cod_servidor))
            ->join('pmieducar.funcao', 'pmieducar.funcao.cod_funcao', 'pmieducar.servidor_funcao.ref_cod_funcao')
            ->get([
                'pmieducar.servidor_funcao.cod_servidor_funcao',
                'pmieducar.servidor_funcao.matricula',
                'pmieducar.funcao.nm_funcao as funcao',
            ]);

        $opcoes = ['' => 'Selecione'];

        if ($lista_funcoes->isNotEmpty()) {
            foreach ($lista_funcoes as $funcao) {
                $opcoes[$funcao['cod_servidor_funcao']] = (!empty($funcao['matricula']) ? "{$funcao['funcao']} - {$funcao['matricula']}" : $funcao['funcao']);
            }
        }

        $this->campoLista('cod_servidor_funcao', 'Função', $opcoes, $this->cod_servidor_funcao, '', false, '', '', false, false);

        // Vínculos
        $opcoes = ['' => 'Selecione'] + LegacyBondType::orderBy('cod_funcionario_vinculo')->pluck('nm_vinculo', 'cod_funcionario_vinculo')->all();

        $this->campoLista('ref_cod_funcionario_vinculo', 'Vínculo', $opcoes, $this->ref_cod_funcionario_vinculo, null, false, '', '', false, false);

        $this->campoRotulo('informacao_carga_horaria', '<b>Informações sobre carga horária</b>');
        $this->campoHora('hora_inicial', 'Hora de início', $this->hora_inicial);
        $this->campoHora('hora_final', 'Hora de término', $this->hora_final);
        $this->campoHoraServidor('carga_horaria_alocada', 'Carga horária', $this->carga_horaria_alocada, true);
        $this->campoHora('hora_atividade', 'Hora atividade', $this->hora_atividade);
        $this->campoHora('horas_excedentes', 'Horas excedentes', $this->horas_excedentes);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(
            635,
            $this->pessoa_logada,
            7,
            "educar_servidor_alocacao_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}"
        );

        $dataAdmissao = $this->data_admissao ? Portabilis_Date_Utils::brToPgSql($this->data_admissao) : null;
        $dataSaida = $this->data_saida ? Portabilis_Date_Utils::brToPgSql($this->data_saida) : null;

        $carga_horaria_disponivel = $this->hhmmToMinutes($this->carga_horaria_disponivel);
        if ($dataSaida > now() || $dataSaida == null) {
            $carga_horaria_alocada = $this->hhmmToMinutes($this->carga_horaria_alocada);
        }
        $carga_horaria_alocada += $this->hhmmToMinutes($this->cargaHorariaAlocadaNoAno($this->ano, $this->cod_servidor_alocacao));

        if ($carga_horaria_disponivel >= $carga_horaria_alocada) {
            if ($this->periodoAlocado()) {
                $this->mensagem = 'Período informado já foi alocado. Por favor, selecione outro.<br />';

                return false;
            }

            $cadastrou = $this->cadastraAlocacao($dataAdmissao, $dataSaida);

            if (!$cadastrou) {
                $this->mensagem = 'Cadastro não realizado.<br />';

                return false;
            }

            // Excluí alocação existente
            if (is_numeric($this->cod_servidor_alocacao)) {
                $alocacaoAnterior = EmployeeAllocation::query()->find($this->cod_servidor_alocacao);
                $alocacaoAnterior?->delete();
            }

            // Atualiza código da alocação
            $this->cod_servidor_alocacao = $cadastrou;
        } else {
            $this->mensagem = 'Não é possível alocar quantidade superior de horas do que o disponível.<br />';
            $this->alocacao_array = null;

            return false;
        }

        $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
        $this->simpleRedirect(sprintf('educar_servidor_alocacao_det.php?cod_servidor_alocacao=%d', $this->cod_servidor_alocacao));
    }

    public function Editar()
    {
        return false;
    }

    public function Excluir()
    {
        if (is_numeric($this->cod_servidor_alocacao)) {
            $alocacao = EmployeeAllocation::query()->find($this->cod_servidor_alocacao);
            $alocacao?->delete();

            $this->mensagem = 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect(sprintf('educar_servidor_alocacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d', $this->ref_cod_servidor, $this->ref_ref_cod_instituicao));
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    private function cargaHorariaAlocadaNoAno($ano, $ignorarAlocacao = null)
    {
        if (!is_numeric($this->ref_cod_servidor) || !is_numeric($ano)) {
            return '';
        }

        return EmployeeAllocation::query()
            ->whereEmployee($this->ref_cod_servidor)
            ->whereYearEq($ano)
            ->withoutLeaveDate()
            ->when(is_numeric($ignorarAlocacao), fn ($q) => $q->whereKeyNot($ignorarAlocacao))
            ->selectRaw('SUM(carga_horaria) as total')
            ->value('total');
    }

    private function periodoAlocado()
    {
        if (!is_numeric($this->ref_cod_escola)
            || !is_numeric($this->periodo)
            || !is_numeric($this->ano)
            || !is_numeric($this->ref_cod_servidor)
        ) {
            return false;
        }

        return EmployeeAllocation::query()
            ->whereSchool($this->ref_cod_escola)
            ->whereEmployee($this->ref_cod_servidor)
            ->whereYearEq($this->ano)
            ->wherePeriod($this->periodo)
            ->active()
            ->withoutLeaveDate()
            ->when(is_numeric($this->cod_servidor_alocacao), fn ($q) => $q->whereKeyNot($this->cod_servidor_alocacao))
            ->exists();
    }

    private function cadastraAlocacao($dataAdmissao, $dataSaida)
    {
        if (!is_numeric($this->ref_ref_cod_instituicao) || !is_numeric($this->ref_cod_servidor)) {
            return false;
        }

        $servidorValido = Employee::query()
            ->where('cod_servidor', $this->ref_cod_servidor)
            ->whereInstitution($this->ref_ref_cod_instituicao)
            ->exists();

        if (!$servidorValido) {
            return false;
        }

        if (!is_numeric($this->ref_cod_escola) || !LegacySchool::query()->whereKey($this->ref_cod_escola)->exists()) {
            return false;
        }

        if (!$this->cargaHorariaValida($this->carga_horaria_alocada) || !is_numeric($this->periodo) || !$this->periodo) {
            return false;
        }

        $alocacao = EmployeeAllocation::query()->create([
            'ref_ref_cod_instituicao' => $this->ref_ref_cod_instituicao,
            'ref_usuario_cad' => $this->pessoa_logada,
            'ref_cod_escola' => $this->ref_cod_escola,
            'ref_cod_servidor' => $this->ref_cod_servidor,
            'ref_cod_servidor_funcao' => is_numeric($this->cod_servidor_funcao) ? $this->cod_servidor_funcao : null,
            'ref_cod_funcionario_vinculo' => is_numeric($this->ref_cod_funcionario_vinculo) ? $this->ref_cod_funcionario_vinculo : null,
            'carga_horaria' => $this->carga_horaria_alocada,
            'hora_inicial' => $this->hora_inicial ?: null,
            'hora_final' => $this->hora_final ?: null,
            'hora_atividade' => $this->hora_atividade ?: null,
            'horas_excedentes' => $this->horas_excedentes ?: null,
            'periodo' => $this->periodo,
            'ano' => is_numeric($this->ano) ? $this->ano : null,
            'data_admissao' => $dataAdmissao ?: null,
            'data_saida' => $dataSaida ?: null,
            'ativo' => 1,
        ]);

        return $alocacao->cod_servidor_alocacao;
    }

    private function cargaHorariaValida($cargaHoraria)
    {
        if (!is_string($cargaHoraria) || !str_contains($cargaHoraria, ':')) {
            return false;
        }

        return self::CARGA_HORARIA_MAXIMA * 60 >= $this->hhmmToMinutes($cargaHoraria);
    }

    public function hhmmToMinutes($hhmm)
    {
        [$hora, $minuto] = explode(':', $hhmm);

        return ((int) $hora * 60) + $minuto;
    }

    public function arrayHhmmToMinutes($array)
    {
        $total = 0;
        foreach ($array as $value) {
            $total += $this->hhmmToMinutes($value);
        }

        return $total;
    }

    public function Formular()
    {
        $this->title = 'Servidores - Servidor Alocação';
        $this->processoAp = 635;
    }
};
