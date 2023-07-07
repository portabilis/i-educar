<?php

use App\Models\LegacyCalendarDay;
use App\Models\LegacyCalendarDayReason;
use App\Models\LegacyCalendarYear;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

return new class() extends clsCadastro
{
    public $pessoa_logada;

    public $ref_cod_calendario_ano_letivo;

    public $mes;

    public $dia;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_cod_calendario_dia_motivo;

    public $descricao;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ano;

    public $ref_cod_escola;

    public $_calendarioTurmaDataMapper;

    public function _getCalendarioTurmaDataMapper()
    {
        if (is_null(value: $this->_calendarioTurmaDataMapper)) {
            $this->_calendarioTurmaDataMapper = new Calendario_Model_TurmaDataMapper();
        }

        return $this->_calendarioTurmaDataMapper;
    }

    /**
     * Verifica se existe uma instância de Calendario_Model_Turma.
     *
     *
     * @param int $codCalendarioAnoLetivo Código da chave primária pmieducar.calendario_ano_letivo
     * @param int $mes
     * @param int $dia
     * @param int $ano
     * @param int $codTurma               Código da chave primária de pmieducar.turma
     * @return bool
     */
    public function _hasEntry($codCalendarioAnoLetivo, $mes, $dia, $ano, $codTurma)
    {
        $args = [
            'calendarioAnoLetivo' => $codCalendarioAnoLetivo,
            'mes' => $mes,
            'dia' => $dia,
            'ano' => $ano,
            'turma' => $codTurma,
        ];

        try {
            $this->_getCalendarioTurmaDataMapper()->find(pkey: $args);

            return true;
        } catch (Exception) {
        }

        return false;
    }

    /**
     * Retorna um array de instâncias de Calendario_Model_Turma para um dado
     * calendário de ano letivo de escola em mês, dia e ano específicos.
     *
     *
     * @param int $codCalendarioAnoLetivo Código de pmieducar.calendario_ano_letivo
     * @param int $mes
     * @param int $dia
     * @param int $ano
     * @return array (cod_turma => Calendario_Model_Turma)
     */
    public function _getEntries($codCalendarioAnoLetivo, $mes, $dia, $ano)
    {
        $where = [
            'calendarioAnoLetivo' => $codCalendarioAnoLetivo,
            'mes' => $mes,
            'dia' => $dia,
            'ano' => $ano,
        ];

        $turmas = $this->_getCalendarioTurmaDataMapper()->findAll(where: $where);

        $ret = [];
        foreach ($turmas as $turma) {
            $ret[$turma->turma] = $turma;
        }

        return $ret;
    }

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->dia = $_GET['dia'];
        $this->mes = $_GET['mes'];
        $this->ref_cod_calendario_ano_letivo = $_GET['ref_cod_calendario_ano_letivo'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 620,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_calendario_anotacao_lst.php'
        );

        if (is_numeric(value: $this->ref_cod_calendario_ano_letivo) &&
            is_numeric(value: $this->mes) && is_numeric(value: $this->dia)
        ) {
            $registro = LegacyCalendarDay::query()
                ->where('ref_cod_calendario_ano_letivo', $this->ref_cod_calendario_ano_letivo)
                ->where('mes', $this->mes)
                ->where('dia', $this->dia)
                ->first();

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $obj_permissoes = new clsPermissoes();

                if ($obj_permissoes->permissao_excluir(int_processo_ap: 620, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
                    if ($this->descricao) {
                        $this->fexcluir = true;
                    }
                }

                $retorno = 'Editar';
            }

            $this->ano = LegacyCalendarYear::query()
                ->where('cod_calendario_ano_letivo', $this->ref_cod_calendario_ano_letivo)
                ->value('ano');
        }

        $this->url_cancelar = sprintf(
            'educar_calendario_anotacao_lst.php?ref_cod_calendario_ano_letivo=%d&ano=%d&mes=%d&dia=%d',
            $registro['ref_cod_calendario_ano_letivo'],
            $this->ano,
            $registro['mes'],
            $registro['dia']
        );
        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // Primary keys
        $this->campoRotulo(
            nome: 'dia_',
            campo: 'Dia',
            valor: sprintf('<b>%d/%d/%d</b>', $this->dia, $this->mes, $this->ano)
        );

        $this->campoOculto(
            nome: 'ref_cod_calendario_ano_letivo',
            valor: $this->ref_cod_calendario_ano_letivo
        );

        $det_calendario_ano_letivo = LegacyCalendarYear::find($this->ref_cod_calendario_ano_letivo)?->getAttributes();
        $ref_cod_escola = $det_calendario_ano_letivo['ref_cod_escola'];

        $this->campoRotulo(nome: 'ano', campo: 'Ano Letivo', valor: $this->ano);
        $this->campoOculto(nome: 'mes', valor: $this->mes);
        $this->campoOculto(nome: 'dia', valor: $this->dia);

        // Foreign keys
        $opcoes = LegacyCalendarDayReason::query()
            ->orderBy('nm_motivo', 'ASC')
            ->pluck('nm_motivo', 'cod_calendario_dia_motivo')
            ->prepend('Selecione', '');

        $this->campoLista(
            nome: 'ref_cod_calendario_dia_motivo',
            campo: 'Calendário Dia Motivo',
            valor: $opcoes,
            default: $this->ref_cod_calendario_dia_motivo,
            obrigatorio: false
        );

        $seletor = '<label><input id="_turmas_sel" onclick="new ied_forms.checkAll(document, \'formcadastro\', \'turmas\')" type="checkbox" /> Selecionar todas</label>';
        $this->campoRotulo(nome: 'turmas_rotulo', campo: 'Turmas', valor: $seletor);
        $turmas = App_Model_IedFinder::getTurmas(escolaId: $ref_cod_escola, ano: $this->ano);

        foreach ($turmas as $codTurma => $nomeTurma) {
            $checked = $this->_hasEntry(
                codCalendarioAnoLetivo: $this->ref_cod_calendario_ano_letivo,
                mes: $this->mes,
                dia: $this->dia,
                ano: $this->ano,
                codTurma: $codTurma
            );

            $this->campoCheck(nome: 'turmas[' . $codTurma  . ']', campo: '', valor: $checked, desc: $nomeTurma);
        }

        $this->campoMemo(nome: 'descricao', campo: 'Descrição', valor: $this->descricao, colunas: 30, linhas: 10, obrigatorio: true);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 620,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_calendario_anotacao_lst.php'
        );

        $obj = new LegacyCalendarDay();
        $obj->ref_cod_calendario_ano_letivo = $this->ref_cod_calendario_ano_letivo;
        $obj->mes = $this->mes;
        $obj->dia = $this->dia;
        $obj->ref_cod_calendario_dia_motivo = $this->ref_cod_calendario_dia_motivo;
        $obj->descricao = $this->descricao;
        $obj->ref_usuario_cad = $this->pessoa_logada;

        $obj->save();

        $cadastrou = $obj->save();

        foreach ($this->turmas as $codTurma => $turma) {
            $calendarioTurma = new Calendario_Model_Turma(options: [
                'calendarioAnoLetivo' => $this->ref_cod_calendario_ano_letivo,
                'ano' => $this->ano,
                'mes' => $this->mes,
                'dia' => $this->dia,
                'turma' => $codTurma,
            ]);
            $this->_getCalendarioTurmaDataMapper()->save(instance: $calendarioTurma);
        }

        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso. <br />';
            $url = sprintf(
                'educar_calendario_anotacao_lst.php?dia=%d&mes=%d&ano=%d&ref_cod_calendario_ano_letivo=%d',
                $this->dia,
                $this->mes,
                $this->ano,
                $this->ref_cod_calendario_ano_letivo
            );

            throw new HttpResponseException(
                response: new RedirectResponse(url: $url)
            );
        }

        $this->mensagem = 'Cadastro não realizado. <br />';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 620,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_calendario_anotacao_lst.php'
        );

        $obj = LegacyCalendarDay::query()
            ->where('ref_cod_calendario_ano_letivo', $this->ref_cod_calendario_ano_letivo)
            ->where('mes', $this->mes)
            ->where('dia', $this->dia)
            ->first();

        $obj->ref_usuario_exc = $this->pessoa_logada;
        $obj->ref_cod_calendario_dia_motivo = $this->ref_cod_calendario_dia_motivo;
        $obj->descricao = $this->descricao;

        $editou = $obj->save();

        // Inicialização de arrays
        $insert = $delete = $entries = $intersect = [];

        if (isset($this->turmas)) {
            foreach ($this->turmas as $codTurma => $turma) {
                $calendarioTurma = new Calendario_Model_Turma(options: [
                    'calendarioAnoLetivo' => $this->ref_cod_calendario_ano_letivo,
                    'ano' => $this->ano,
                    'mes' => $this->mes,
                    'dia' => $this->dia,
                    'turma' => $codTurma,
                ]);
                $insert[$codTurma] = $calendarioTurma;
            }
        }

        // Instâncias persistidas de Calendario_Model_Turma
        $entries = $this->_getEntries(
            codCalendarioAnoLetivo: $this->ref_cod_calendario_ano_letivo,
            mes: $this->mes,
            dia: $this->dia,
            ano: $this->ano
        );

        // Instâncias para apagar
        $delete = array_diff(array_keys(array: $entries), array_keys(array: $insert));

        // Instâncias já persistidas
        $intersect = array_intersect(array_keys(array: $entries), array_keys(array: $insert));

        foreach ($delete as $id) {
            $this->_getCalendarioTurmaDataMapper()->delete(instance: $entries[$id]);
        }

        foreach ($insert as $key => $entry) {
            if (in_array(needle: $key, haystack: $intersect)) {
                continue;
            }
            $this->_getCalendarioTurmaDataMapper()->save(instance: $entry);
        }

        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso. <br />';
            $url = sprintf(
                'educar_calendario_anotacao_lst.php?dia=%d&mes=%d&ano=%d&ref_cod_calendario_ano_letivo=%d',
                $this->dia,
                $this->mes,
                $this->ano,
                $this->ref_cod_calendario_ano_letivo
            );

            throw new HttpResponseException(
                response: new RedirectResponse(url: $url)
            );
        }

        $this->mensagem = 'Edição não realizada. <br />';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(
            int_processo_ap: 620,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_calendario_anotacao_lst.php'
        );

        $obj = LegacyCalendarDay::query()
            ->where('ref_cod_calendario_ano_letivo', $this->ref_cod_calendario_ano_letivo)
            ->where('mes', $this->mes)
            ->where('dia', $this->dia)
            ->first();

        $excluiu = $obj->delete();

        $entries = $this->_getEntries(
            codCalendarioAnoLetivo: $this->ref_cod_calendario_ano_letivo,
            mes: $this->mes,
            dia: $this->dia,
            ano: $this->ano
        );

        foreach ($entries as $entry) {
            $this->_getCalendarioTurmaDataMapper()->delete(instance: $entry);
        }

        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso. <br />';
            $url = sprintf(
                'educar_calendario_anotacao_lst.php?dia=%d&mes=%d&ano=%d&ref_cod_calendario_ano_letivo=%d',
                $this->dia,
                $this->mes,
                $this->ano,
                $this->ref_cod_calendario_ano_letivo
            );

            throw new HttpResponseException(
                response: new RedirectResponse(url: $url)
            );
        }

        $this->mensagem = 'Exclusão não realizada. <br />';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Calendário Dia';
        $this->processoAp = 620;
    }
};
