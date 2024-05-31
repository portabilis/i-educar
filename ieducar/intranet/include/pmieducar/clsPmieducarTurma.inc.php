<?php

use App\Models\SchoolClassInep;
use iEducar\Legacy\Model;
use Illuminate\Support\Facades\Cache;

class clsPmieducarTurma extends Model
{
    const TURNO_MATUTINO = 1;

    const TURNO_VESPERTINO = 2;

    const TURNO_NOTURNO = 3;

    const TURNO_INTEGRAL = 4;

    public $cod_turma;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_ref_cod_serie;

    public $ref_ref_cod_escola;

    public $ref_cod_infra_predio_comodo;

    public $nm_turma;

    public $sgl_turma;

    public $max_aluno;

    public $multiseriada;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_turma_tipo;

    public $hora_inicial = false;

    public $hora_final = false;

    public $hora_inicio_intervalo = false;

    public $hora_fim_intervalo = false;

    public $ano;

    public $ref_cod_regente;

    public $ref_cod_instituicao_regente;

    public $ref_cod_instituicao;

    public $ref_cod_curso;

    public $ref_ref_cod_serie_mult;

    public $ref_ref_cod_escola_mult;

    public $visivel;

    public $data_fechamento;

    public $tipo_atendimento = false;

    public $cod_curso_profissional;

    public $etapa_educacenso;

    public $ref_cod_disciplina_dispensada;

    public $parecer_1_etapa;

    public $parecer_2_etapa;

    public $parecer_3_etapa;

    public $parecer_4_etapa;

    public $nao_informar_educacenso;

    public $tipo_mediacao_didatico_pedagogico = false;

    public $dias_semana;

    public $atividades_complementares;

    public $atividades_aee;

    public $local_funcionamento_diferenciado;

    public $listarNaoInformarEducacenso = true;

    public $codUsuario;

    public $tipo_boletim_diferenciado = false;

    public $horario_funcionamento_turno_matutino;

    public $codigo_inep_matutino;

    public $hora_inicial_matutino;

    public $hora_inicio_intervalo_matutino;

    public $hora_fim_intervalo_matutino;

    public $hora_final_matutino;

    public $horario_funcionamento_turma_vespertino;

    public $codigo_inep_vespertino;

    public $hora_inicial_vespertino;

    public $hora_inicio_intervalo_vespertino;

    public $hora_fim_intervalo_vespertino;

    public $hora_final_vespertino;

    public function __construct($cod_turma = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_ref_cod_serie = null, $ref_ref_cod_escola = null, $ref_cod_infra_predio_comodo = null, $nm_turma = null, $sgl_turma = null, $max_aluno = null, $multiseriada = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $ref_cod_turma_tipo = null, $hora_inicial = null, $hora_final = null, $hora_inicio_intervalo = null, $hora_fim_intervalo = null, $ref_cod_regente = null, $ref_cod_instituicao_regente = null, $ref_cod_instituicao = null, $ref_cod_curso = null, $ref_ref_cod_serie_mult = null, $ref_ref_cod_escola_mult = null, $visivel = null, $turma_turno_id = null, $tipo_boletim = null, $ano = null, $data_fechamento = null, $ref_cod_disciplina_dispensada = null)
    {

        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}turma";

        $this->_campos_lista = $this->_todos_campos = 't.cod_turma, t.ref_usuario_exc, t.ref_usuario_cad, t.ref_ref_cod_serie, t.ref_ref_cod_escola, t.nm_turma, t.sgl_turma, t.max_aluno, t.multiseriada, t.data_cadastro, t.data_exclusao, t.ativo, t.ref_cod_turma_tipo, t.hora_inicial, t.hora_final, t.hora_inicio_intervalo, t.hora_fim_intervalo, t.ref_cod_regente, t.ref_cod_instituicao_regente,t.ref_cod_instituicao, t.ref_cod_curso, t.ref_ref_cod_serie_mult, t.ref_ref_cod_escola_mult, t.visivel, t.turma_turno_id, t.tipo_boletim, t.tipo_boletim_diferenciado, t.ano,
        t.tipo_atendimento, t.cod_curso_profissional, t.etapa_educacenso, t.ref_cod_disciplina_dispensada, t.parecer_1_etapa, t.parecer_2_etapa,
        t.parecer_3_etapa, t.parecer_4_etapa, t.nao_informar_educacenso, t.tipo_mediacao_didatico_pedagogico, t.dias_semana, t.atividades_complementares, t.atividades_aee, t.local_funcionamento_diferenciado, t.estrutura_curricular, t.formas_organizacao_turma, t.unidade_curricular, t.outras_unidades_curriculares_obrigatorias, t.classe_com_lingua_brasileira_sinais,
        t.hora_inicial_matutino, t.hora_inicio_intervalo_matutino, t.hora_fim_intervalo_matutino, t.hora_final_matutino, t.hora_inicial_vespertino, t.hora_inicio_intervalo_vespertino, t.hora_fim_intervalo_vespertino, t.hora_final_vespertino
        ';

        if (is_numeric($ref_cod_turma_tipo)) {
            $this->ref_cod_turma_tipo = $ref_cod_turma_tipo;
        }
        if (is_numeric($ref_ref_cod_escola) && is_numeric($ref_ref_cod_serie)) {
            $this->ref_ref_cod_escola = $ref_ref_cod_escola;
            $this->ref_ref_cod_serie = $ref_ref_cod_serie;
        }
        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }

        if (is_numeric($ref_cod_regente) && is_numeric($ref_cod_instituicao_regente)) {
            $this->ref_cod_regente = $ref_cod_regente;
            $this->ref_cod_instituicao_regente = $ref_cod_instituicao_regente;
        }

        if (is_numeric($cod_turma)) {
            $this->cod_turma = $cod_turma;
        }
        if (is_string($nm_turma)) {
            $this->nm_turma = $nm_turma;
        }
        if (is_string($sgl_turma)) {
            $this->sgl_turma = $sgl_turma;
        }
        if (is_numeric($max_aluno)) {
            $this->max_aluno = $max_aluno;
        }
        if (is_numeric($multiseriada)) {
            $this->multiseriada = $multiseriada;
        }
        if (is_string($data_cadastro)) {
            $this->data_cadastro = $data_cadastro;
        }
        if (is_string($data_exclusao)) {
            $this->data_exclusao = $data_exclusao;
        }
        if (is_numeric($ativo)) {
            $this->ativo = $ativo;
        }
        if (($hora_inicial)) {
            $this->hora_inicial = $hora_inicial;
        }
        if (($hora_final)) {
            $this->hora_final = $hora_final;
        }
        if (($hora_inicio_intervalo)) {
            $this->hora_inicio_intervalo = $hora_inicio_intervalo;
        }
        if (($hora_fim_intervalo)) {
            $this->hora_fim_intervalo = $hora_fim_intervalo;
        }

        if (is_numeric($ref_cod_instituicao)) {
            $this->ref_cod_instituicao = $ref_cod_instituicao;
        }

        if (is_numeric($ref_cod_curso)) {
            $this->ref_cod_curso = $ref_cod_curso;
        }

        if ((is_numeric($ref_ref_cod_escola_mult) && is_numeric($ref_ref_cod_serie_mult)) || is_null($ref_ref_cod_serie_mult)) {
            if (is_null($ref_ref_cod_serie_mult)) {
                $this->ref_ref_cod_escola_mult = '';
                $this->ref_ref_cod_serie_mult = '';
            } else {
                $this->ref_ref_cod_escola_mult = $ref_ref_cod_escola_mult;
                $this->ref_ref_cod_serie_mult = $ref_ref_cod_serie_mult;
            }
        }
        if (is_bool($visivel)) {
            $this->visivel = dbBool($visivel);
        }

        $this->turma_turno_id = $turma_turno_id;
        $this->tipo_boletim = $tipo_boletim;
        $this->ano = $ano;
        $this->data_fechamento = $data_fechamento;

        if (is_numeric($ref_cod_disciplina_dispensada) || is_null($ref_cod_disciplina_dispensada)) {
            $this->ref_cod_disciplina_dispensada = $ref_cod_disciplina_dispensada;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_usuario_cad) && is_string($this->nm_turma) && is_numeric($this->max_aluno) && is_numeric($this->multiseriada) && is_numeric($this->ref_cod_turma_tipo)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_serie)) {
                $campos .= "{$gruda}ref_ref_cod_serie";
                $valores .= "{$gruda}'{$this->ref_ref_cod_serie}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_escola)) {
                $campos .= "{$gruda}ref_ref_cod_escola";
                $valores .= "{$gruda}'{$this->ref_ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_turma)) {
                $nm_turma = $db->escapeString($this->nm_turma);
                $campos .= "{$gruda}nm_turma";
                $valores .= "{$gruda}'{$nm_turma}'";
                $gruda = ', ';
            }
            if (is_string($this->sgl_turma)) {
                $sgl_turma = $db->escapeString($this->sgl_turma);
                $campos .= "{$gruda}sgl_turma";
                $valores .= "{$gruda}'{$sgl_turma}'";
                $gruda = ', ';
            }
            if (is_numeric($this->max_aluno)) {
                $campos .= "{$gruda}max_aluno";
                $valores .= "{$gruda}'{$this->max_aluno}'";
                $gruda = ', ';
            }
            if (is_numeric($this->multiseriada)) {
                $campos .= "{$gruda}multiseriada";
                $valores .= "{$gruda}'{$this->multiseriada}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_regente)) {
                $campos .= "{$gruda}ref_cod_regente";
                $valores .= "{$gruda}'{$this->ref_cod_regente}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_instituicao_regente)) {
                $campos .= "{$gruda}ref_cod_instituicao_regente";
                $valores .= "{$gruda}'{$this->ref_cod_instituicao_regente}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_instituicao)) {
                $campos .= "{$gruda}ref_cod_instituicao";
                $valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_curso)) {
                $campos .= "{$gruda}ref_cod_curso";
                $valores .= "{$gruda}'{$this->ref_cod_curso}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';
            if (is_numeric($this->ref_cod_turma_tipo)) {
                $campos .= "{$gruda}ref_cod_turma_tipo";
                $valores .= "{$gruda}'{$this->ref_cod_turma_tipo}'";
                $gruda = ', ';
            }
            if (($this->hora_inicial)) {
                $campos .= "{$gruda}hora_inicial";
                $valores .= "{$gruda}'{$this->hora_inicial}'";
                $gruda = ', ';
            }
            if (($this->hora_final)) {
                $campos .= "{$gruda}hora_final";
                $valores .= "{$gruda}'{$this->hora_final}'";
                $gruda = ', ';
            }
            if (($this->hora_inicio_intervalo)) {
                $campos .= "{$gruda}hora_inicio_intervalo";
                $valores .= "{$gruda}'{$this->hora_inicio_intervalo}'";
                $gruda = ', ';
            }
            if (($this->hora_fim_intervalo)) {
                $campos .= "{$gruda}hora_fim_intervalo";
                $valores .= "{$gruda}'{$this->hora_fim_intervalo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_escola_mult)) {
                $campos .= "{$gruda}ref_ref_cod_escola_mult";
                $valores .= "{$gruda}'{$this->ref_ref_cod_escola_mult}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_serie_mult)) {
                $campos .= "{$gruda}ref_ref_cod_serie_mult";
                $valores .= "{$gruda}'{$this->ref_ref_cod_serie_mult}'";
                $gruda = ', ';
            }

            if (is_bool($this->visivel)) {
                $this->visivel = $this->visivel ? 'true' : 'false';
                $campos .= "{$gruda}visivel";
                $valores .= "{$gruda}'{$this->visivel}'";
                $gruda = ', ';
            }

            if (is_numeric($this->turma_turno_id)) {
                $campos .= "{$gruda}turma_turno_id";
                $valores .= "{$gruda}'{$this->turma_turno_id}'";
                $gruda = ', ';
            }

            if (is_numeric($this->tipo_boletim)) {
                $campos .= "{$gruda}tipo_boletim";
                $valores .= "{$gruda}'{$this->tipo_boletim}'";
                $gruda = ', ';
            }

            if (is_numeric($this->tipo_boletim_diferenciado)) {
                $campos .= "{$gruda}tipo_boletim_diferenciado";
                $valores .= "{$gruda}'{$this->tipo_boletim_diferenciado}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ano)) {
                $campos .= "{$gruda}ano";
                $valores .= "{$gruda}'{$this->ano}'";
                $gruda = ', ';
            }

            if (is_string($this->data_fechamento) && $this->data_fechamento != '') {
                $campos .= "{$gruda}data_fechamento";
                $valores .= "{$gruda}'{$this->data_fechamento}'";
                $gruda = ', ';
            }

            if (is_numeric($this->tipo_atendimento)) {
                $campos .= "{$gruda}tipo_atendimento";
                $valores .= "{$gruda}'{$this->tipo_atendimento}'";
                $gruda = ', ';
            }

            if (is_numeric($this->cod_curso_profissional)) {
                $campos .= "{$gruda}cod_curso_profissional";
                $valores .= "{$gruda}'{$this->cod_curso_profissional}'";
                $gruda = ', ';
            }

            if (is_numeric($this->etapa_educacenso)) {
                $campos .= "{$gruda}etapa_educacenso";
                $valores .= "{$gruda}'{$this->etapa_educacenso}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_disciplina_dispensada)) {
                $campos .= "{$gruda}ref_cod_disciplina_dispensada";
                $valores .= "{$gruda}'{$this->ref_cod_disciplina_dispensada}'";
                $gruda = ', ';
            } elseif (is_null($this->ref_cod_disciplina_dispensada)) {
                $campos .= "{$gruda}ref_cod_disciplina_dispensada";
                $valores .= "{$gruda}null";
                $gruda = ', ';
            }

            if (is_numeric($this->nao_informar_educacenso)) {
                $campos .= "{$gruda}nao_informar_educacenso";
                $valores .= "{$gruda}'{$this->nao_informar_educacenso}'";
                $gruda = ', ';
            }

            if (is_numeric($this->tipo_mediacao_didatico_pedagogico)) {
                $campos .= "{$gruda}tipo_mediacao_didatico_pedagogico";
                $valores .= "{$gruda}'{$this->tipo_mediacao_didatico_pedagogico}'";
                $gruda = ', ';
            }

            if (is_numeric($this->local_funcionamento_diferenciado)) {
                $campos .= "{$gruda}local_funcionamento_diferenciado";
                $valores .= "{$gruda}'{$this->local_funcionamento_diferenciado}'";
                $gruda = ', ';
            }

            if (is_string($this->dias_semana)) {
                $campos .= "{$gruda}dias_semana";
                $valores .= "{$gruda}'{$this->dias_semana}'";
                $gruda = ', ';
            }

            if (is_string($this->atividades_complementares)) {
                $campos .= "{$gruda}atividades_complementares";
                $valores .= "{$gruda}'{$this->atividades_complementares}'";
                $gruda = ', ';
            }

            if (is_string($this->atividades_aee)) {
                $campos .= "{$gruda}atividades_aee";
                $valores .= "{$gruda}'{$this->atividades_aee}'";
            }

            if (($this->hora_inicial_matutino)) {
                $campos .= "{$gruda}hora_inicial_matutino";
                $valores .= "{$gruda}'{$this->hora_inicial_matutino}'";
                $gruda = ', ';
            }

            if (($this->hora_inicio_intervalo_matutino)) {
                $campos .= "{$gruda}hora_inicio_intervalo_matutino";
                $valores .= "{$gruda}'{$this->hora_inicio_intervalo_matutino}'";
                $gruda = ', ';
            }

            if (($this->hora_fim_intervalo_matutino)) {
                $campos .= "{$gruda}hora_fim_intervalo_matutino";
                $valores .= "{$gruda}'{$this->hora_fim_intervalo_matutino}'";
                $gruda = ', ';
            }

            if (($this->hora_final_matutino)) {
                $campos .= "{$gruda}hora_final_matutino";
                $valores .= "{$gruda}'{$this->hora_final_matutino}'";
                $gruda = ', ';
            }

            if (($this->hora_inicial_vespertino)) {
                $campos .= "{$gruda}hora_inicial_vespertino";
                $valores .= "{$gruda}'{$this->hora_inicial_vespertino}'";
                $gruda = ', ';
            }

            if (($this->hora_inicio_intervalo_vespertino)) {
                $campos .= "{$gruda}hora_inicio_intervalo_vespertino";
                $valores .= "{$gruda}'{$this->hora_inicio_intervalo_vespertino}'";
                $gruda = ', ';
            }

            if (($this->hora_fim_intervalo_vespertino)) {
                $campos .= "{$gruda}hora_fim_intervalo_vespertino";
                $valores .= "{$gruda}'{$this->hora_fim_intervalo_vespertino}'";
                $gruda = ', ';
            }

            if (($this->hora_final_vespertino)) {
                $campos .= "{$gruda}hora_final_vespertino";
                $valores .= "{$gruda}'{$this->hora_final_vespertino}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_turma_seq");
        }

        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->cod_turma) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_serie)) {
                $set .= "{$gruda}ref_ref_cod_serie = '{$this->ref_ref_cod_serie}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_ref_cod_escola)) {
                $set .= "{$gruda}ref_ref_cod_escola = '{$this->ref_ref_cod_escola}'";
                $gruda = ', ';
            }
            if (is_string($this->nm_turma)) {
                $nm_turma = $db->escapeString($this->nm_turma);
                $set .= "{$gruda}nm_turma = '{$nm_turma}'";
                $gruda = ', ';
            }
            if (is_string($this->sgl_turma)) {
                $sgl_turma = $db->escapeString($this->sgl_turma);
                $set .= "{$gruda}sgl_turma = '{$sgl_turma}'";
                $gruda = ', ';
            }
            if (is_numeric($this->max_aluno)) {
                $set .= "{$gruda}max_aluno = '{$this->max_aluno}'";
                $gruda = ', ';
            }
            if (is_numeric($this->multiseriada)) {
                $set .= "{$gruda}multiseriada = '{$this->multiseriada}'";
                $gruda = ', ';
            }

            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_regente)) {
                $set .= "{$gruda}ref_cod_regente = '{$this->ref_cod_regente}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}ref_cod_regente = null";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_instituicao_regente)) {
                $set .= "{$gruda}ref_cod_instituicao_regente = '{$this->ref_cod_instituicao_regente}'";
                $gruda = ', ';
            }
            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';
            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_turma_tipo)) {
                $set .= "{$gruda}ref_cod_turma_tipo = '{$this->ref_cod_turma_tipo}'";
                $gruda = ', ';
            }
            if (($this->hora_inicial)) {
                $set .= "{$gruda}hora_inicial = '{$this->hora_inicial}'";
                $gruda = ', ';
            } elseif ($this->hora_inicial !== false) {
                $set .= "{$gruda}hora_inicial = NULL";
                $gruda = ', ';
            }

            if (($this->hora_final)) {
                $set .= "{$gruda}hora_final = '{$this->hora_final}'";
                $gruda = ', ';
            } elseif ($this->hora_final !== false) {
                $set .= "{$gruda}hora_final = NULL";
                $gruda = ', ';
            }

            if (($this->hora_inicio_intervalo)) {
                $set .= "{$gruda}hora_inicio_intervalo = '{$this->hora_inicio_intervalo}'";
                $gruda = ', ';
            } elseif ($this->hora_inicio_intervalo !== false) {
                $set .= "{$gruda}hora_inicio_intervalo = NULL";
                $gruda = ', ';
            }

            if (($this->hora_fim_intervalo)) {
                $set .= "{$gruda}hora_fim_intervalo = '{$this->hora_fim_intervalo}'";
                $gruda = ', ';
            } elseif ($this->hora_fim_intervalo !== false) {
                $set .= "{$gruda}hora_fim_intervalo = NULL";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_instituicao)) {
                $set .= "{$gruda}ref_cod_instituicao = '{$this->ref_cod_instituicao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_curso)) {
                $set .= "{$gruda}ref_cod_curso = '{$this->ref_cod_curso}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_ref_cod_escola_mult)) {
                $set .= "{$gruda}ref_ref_cod_escola_mult = '{$this->ref_ref_cod_escola_mult}'";
                $gruda = ', ';
            } elseif (empty($this->ref_ref_cod_escola_mult)) {
                $set .= "{$gruda}ref_ref_cod_escola_mult = NULL";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_ref_cod_serie_mult)) {
                $set .= "{$gruda}ref_ref_cod_serie_mult = '{$this->ref_ref_cod_serie_mult}'";
                $gruda = ', ';
            } elseif (empty($this->ref_ref_cod_serie_mult)) {
                $set .= "{$gruda}ref_ref_cod_serie_mult = NULL";
                $gruda = ', ';
            }

            if (is_bool($this->visivel)) {
                $this->visivel = $this->visivel ? 'true' : 'false';
                $set .= "{$gruda}visivel = {$this->visivel}";
                $gruda = ', ';
            }

            if (is_numeric($this->turma_turno_id)) {
                $set .= "{$gruda}turma_turno_id = '{$this->turma_turno_id}'";
                $gruda = ', ';
            }

            if (is_numeric($this->tipo_boletim)) {
                $set .= "{$gruda}tipo_boletim = '{$this->tipo_boletim}'";
                $gruda = ', ';
            }

            if (is_numeric($this->tipo_boletim_diferenciado)) {
                $set .= "{$gruda}tipo_boletim_diferenciado = '{$this->tipo_boletim_diferenciado}'";
                $gruda = ', ';
            } elseif ($this->tipo_boletim_diferenciado !== false) {
                $set .= "{$gruda}tipo_boletim_diferenciado = NULL";
                $gruda = ', ';
            }

            if (is_numeric($this->ano)) {
                $set .= "{$gruda}ano = '{$this->ano}'";
                $gruda = ', ';
            }

            if (is_string($this->data_fechamento) && $this->data_fechamento != '') {
                $set .= "{$gruda}data_fechamento = '{$this->data_fechamento}'";
                $gruda = ', ';
            }

            if (is_numeric($this->tipo_atendimento)) {
                $set .= "{$gruda}tipo_atendimento = '{$this->tipo_atendimento}'";
                $gruda = ', ';
            } elseif ($this->tipo_atendimento !== false) {
                $set .= "{$gruda}tipo_atendimento = NULL";
                $gruda = ', ';
            }

            if (is_numeric($this->cod_curso)) {
                $set .= "{$gruda}cod_curso = '{$this->cod_curso}'";
                $gruda = ', ';
            }

            if (is_numeric($this->cod_curso_profissional)) {
                $set .= "{$gruda}cod_curso_profissional = '{$this->cod_curso_profissional}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}cod_curso_profissional = NULL";
                $gruda = ', ';
            }

            if (is_numeric($this->etapa_educacenso)) {
                $set .= "{$gruda}etapa_educacenso = '{$this->etapa_educacenso}'";
                $gruda = ', ';
            } elseif (is_null($this->etapa_educacenso)) {
                $set .= "{$gruda}etapa_educacenso = NULL";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_disciplina_dispensada)) {
                $set .= "{$gruda}ref_cod_disciplina_dispensada = {$this->ref_cod_disciplina_dispensada}";
                $gruda = ', ';
            } elseif (is_null($this->ref_cod_disciplina_dispensada)) {
                $set .= "{$gruda}ref_cod_disciplina_dispensada = NULL";
                $gruda = ', ';
            }
            if (is_numeric($this->nao_informar_educacenso)) {
                $set .= "{$gruda}nao_informar_educacenso = '{$this->nao_informar_educacenso}'";
                $gruda = ', ';
            }
            if (is_string($this->parecer_1_etapa)) {
                $set .= "{$gruda}parecer_1_etapa = '{$this->parecer_1_etapa}'";
                $gruda = ', ';
            }
            if (is_string($this->parecer_2_etapa)) {
                $set .= "{$gruda}parecer_2_etapa = '{$this->parecer_2_etapa}'";
                $gruda = ', ';
            }
            if (is_string($this->parecer_3_etapa)) {
                $set .= "{$gruda}parecer_3_etapa = '{$this->parecer_3_etapa}'";
                $gruda = ', ';
            }
            if (is_string($this->parecer_4_etapa)) {
                $set .= "{$gruda}parecer_4_etapa = '{$this->parecer_4_etapa}'";
                $gruda = ', ';
            }

            if (is_numeric($this->tipo_mediacao_didatico_pedagogico)) {
                $set .= "{$gruda}tipo_mediacao_didatico_pedagogico = '{$this->tipo_mediacao_didatico_pedagogico}'";
                $gruda = ', ';
            } elseif ($this->tipo_mediacao_didatico_pedagogico !== false) {
                $set .= "{$gruda}tipo_mediacao_didatico_pedagogico = NULL";
                $gruda = ', ';
            }

            if (is_numeric($this->local_funcionamento_diferenciado)) {
                $set .= "{$gruda}local_funcionamento_diferenciado = '{$this->local_funcionamento_diferenciado}'";
            } else {
                $set .= "{$gruda}local_funcionamento_diferenciado = NULL ";
            }

            $gruda = ', ';
            if (is_string($this->dias_semana)) {
                $set .= "{$gruda}dias_semana = '{$this->dias_semana}'";
                $gruda = ', ';
            }

            if (is_string($this->atividades_complementares)) {
                $set .= "{$gruda}atividades_complementares = '{$this->atividades_complementares}'";
                $gruda = ', ';
            }

            if (is_string($this->atividades_aee)) {
                $set .= "{$gruda}atividades_aee = '{$this->atividades_aee}'";
            }

            if (($this->hora_inicial)) {
                $set .= "{$gruda}hora_inicial = '{$this->hora_inicial}'";
                $gruda = ', ';
            } elseif ($this->hora_inicial !== false) {
                $set .= "{$gruda}hora_inicial = NULL";
                $gruda = ', ';
            }

            if (($this->hora_inicial_matutino)) {
                $set .= "{$gruda}hora_inicial_matutino = '{$this->hora_inicial_matutino}'";
                $gruda = ', ';
            } elseif ($this->hora_inicial_matutino !== false) {
                $set .= "{$gruda}hora_inicial_matutino = NULL";
                $gruda = ', ';
            }

            if (($this->hora_inicio_intervalo_matutino)) {
                $set .= "{$gruda}hora_inicio_intervalo_matutino = '{$this->hora_inicio_intervalo_matutino}'";
                $gruda = ', ';
            } elseif ($this->hora_inicio_intervalo_matutino !== false) {
                $set .= "{$gruda}hora_inicio_intervalo_matutino = NULL";
                $gruda = ', ';
            }

            if (($this->hora_fim_intervalo_matutino)) {
                $set .= "{$gruda}hora_fim_intervalo_matutino = '{$this->hora_fim_intervalo_matutino}'";
                $gruda = ', ';
            } elseif ($this->hora_fim_intervalo_matutino !== false) {
                $set .= "{$gruda}hora_fim_intervalo_matutino = NULL";
                $gruda = ', ';
            }

            if (($this->hora_final_matutino)) {
                $set .= "{$gruda}hora_final_matutino = '{$this->hora_final_matutino}'";
                $gruda = ', ';
            } elseif ($this->hora_final_matutino !== false) {
                $set .= "{$gruda}hora_final_matutino = NULL";
                $gruda = ', ';
            }

            if (($this->hora_inicial_vespertino)) {
                $set .= "{$gruda}hora_inicial_vespertino = '{$this->hora_inicial_vespertino}'";
                $gruda = ', ';
            } elseif ($this->hora_inicial_vespertino !== false) {
                $set .= "{$gruda}hora_inicial_vespertino = NULL";
                $gruda = ', ';
            }

            if (($this->hora_inicio_intervalo_vespertino)) {
                $set .= "{$gruda}hora_inicio_intervalo_vespertino = '{$this->hora_inicio_intervalo_vespertino}'";
                $gruda = ', ';
            } elseif ($this->hora_inicio_intervalo_vespertino !== false) {
                $set .= "{$gruda}hora_inicio_intervalo_vespertino = NULL";
                $gruda = ', ';
            }

            if (($this->hora_fim_intervalo_vespertino)) {
                $set .= "{$gruda}hora_fim_intervalo_vespertino = '{$this->hora_fim_intervalo_vespertino}'";
                $gruda = ', ';
            } elseif ($this->hora_fim_intervalo_vespertino !== false) {
                $set .= "{$gruda}hora_fim_intervalo_vespertino = NULL";
                $gruda = ', ';
            }

            if (($this->hora_final_vespertino)) {
                $set .= "{$gruda}hora_final_vespertino = '{$this->hora_final_vespertino}'";
                $gruda = ', ';
            } elseif ($this->hora_final_vespertino !== false) {
                $set .= "{$gruda}hora_final_vespertino = NULL";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_turma = '{$this->cod_turma}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array|false
     */
    public function lista($int_cod_turma = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_ref_cod_serie = null, $int_ref_ref_cod_escola = null, $int_ref_cod_infra_predio_comodo = null, $str_nm_turma = null, $str_sgl_turma = null, $int_max_aluno = null, $int_multiseriada = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_turma_tipo = null, $time_hora_inicial_ini = null, $time_hora_inicial_fim = null, $time_hora_final_ini = null, $time_hora_final_fim = null, $time_hora_inicio_intervalo_ini = null, $time_hora_inicio_intervalo_fim = null, $time_hora_fim_intervalo_ini = null, $time_hora_fim_intervalo_fim = null, $int_ref_cod_curso = null, $int_ref_cod_instituicao = null, $int_ref_cod_regente = null, $int_ref_cod_instituicao_regente = null, $int_ref_ref_cod_escola_mult = null, $int_ref_ref_cod_serie_mult = null, $int_qtd_min_alunos_matriculados = null, $bool_verifica_serie_multiseriada = false, $bool_tem_alunos_aguardando_nota = null, $visivel = null, $turma_turno_id = null, $tipo_boletim = null, $ano = null, $somenteAnoLetivoEmAndamento = false)
    {
        $db = new clsBanco();

        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} t";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_turma)) {
            $filtros .= "{$whereAnd} t.cod_turma = '{$int_cod_turma}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} t.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} t.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_ref_cod_serie)) {
            $filtros .= "{$whereAnd}
                CASE
                    WHEN multiseriada = 1 THEN EXISTS (
                        SELECT 1
                        FROM pmieducar.turma_serie ts
                        WHERE ts.turma_id = t.cod_turma
                        AND ts.serie_id = {$int_ref_ref_cod_serie}
                    )
                    ELSE t.ref_ref_cod_serie = {$int_ref_ref_cod_serie}
                END";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_ref_cod_escola)) {
            $filtros .= "{$whereAnd} ( t.ref_ref_cod_escola = '{$int_ref_ref_cod_escola}' )";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_turma)) {
            $nm_turma = $db->escapeString($str_nm_turma);
            $filtros .= "{$whereAnd} exists(select 1 from pmieducar.turma where unaccent(nm_turma) ILIKE unaccent('%{$nm_turma}%'))";
            $whereAnd = ' AND ';
        }
        if (is_string($str_sgl_turma)) {
            $filtros .= "{$whereAnd} t.sgl_turma LIKE '%{$str_sgl_turma}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_max_aluno)) {
            $filtros .= "{$whereAnd} t.max_aluno = '{$int_max_aluno}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_multiseriada)) {
            $filtros .= "{$whereAnd} t.multiseriada = '{$int_multiseriada}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} t.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} t.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} t.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} t.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} t.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} t.ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_turma_tipo)) {
            $filtros .= "{$whereAnd} t.ref_cod_turma_tipo = '{$int_ref_cod_turma_tipo}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_inicial_ini)) {
            $filtros .= "{$whereAnd} t.hora_inicial >= '{$time_hora_inicial_ini}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_inicial_fim)) {
            $filtros .= "{$whereAnd} t.hora_inicial <= '{$time_hora_inicial_fim}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_final_ini)) {
            $filtros .= "{$whereAnd} t.hora_final >= '{$time_hora_final_ini}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_final_fim)) {
            $filtros .= "{$whereAnd} t.hora_final <= '{$time_hora_final_fim}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_inicio_intervalo_ini)) {
            $filtros .= "{$whereAnd} t.hora_inicio_intervalo >= '{$time_hora_inicio_intervalo_ini}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_inicio_intervalo_fim)) {
            $filtros .= "{$whereAnd} t.hora_inicio_intervalo <= '{$time_hora_inicio_intervalo_fim}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_fim_intervalo_ini)) {
            $filtros .= "{$whereAnd} t.hora_fim_intervalo >= '{$time_hora_fim_intervalo_ini}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_fim_intervalo_fim)) {
            $filtros .= "{$whereAnd} t.hora_fim_intervalo <= '{$time_hora_fim_intervalo_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_regente)) {
            $filtros .= "{$whereAnd} t.ref_cod_regente = '{$int_ref_cod_regente}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao_regente)) {
            $filtros .= "{$whereAnd} t.ref_cod_instituicao_regente = '{$int_ref_cod_instituicao_regente}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} t.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_curso)) {
            $filtros .= "{$whereAnd} t.ref_cod_curso = '{$int_ref_cod_curso}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_qtd_min_alunos_matriculados)) {
            $filtros .= "{$whereAnd} (SELECT COUNT(0) FROM pmieducar.matricula_turma WHERE ref_cod_turma = t.cod_turma) >= '{$int_qtd_min_alunos_matriculados}' ";
            $whereAnd = ' AND ';
        }

        if (is_bool($visivel)) {
            if ($visivel) {
                $filtros .= "{$whereAnd} t.visivel = TRUE";
                $whereAnd = ' AND ';
            } else {
                $filtros .= "{$whereAnd} t.visivel = FALSE";
                $whereAnd = ' AND ';
            }
        } elseif (is_array($visivel) && count($visivel)) {
            $filtros .= "{$whereAnd} t.visivel IN (" . implode(',', $visivel) . ')';
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} t.visivel = TRUE";
            $whereAnd = ' AND ';
        }

        if (is_numeric($turma_turno_id)) {
            $filtros .= "{$whereAnd} t.turma_turno_id = '{$turma_turno_id}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($tipo_boletim)) {
            $filtros .= "{$whereAnd} t.tipo_boletim = '{$tipo_boletim}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ano)) {
            $filtros .= "{$whereAnd} t.ano = '{$ano}'";
            $whereAnd = ' AND ';
        }

        if ($somenteAnoLetivoEmAndamento) {
            $filtros .= "{$whereAnd}  t.ano in (SELECT ano FROM pmieducar.escola_ano_letivo WHERE andamento = 1 AND ativo = 1 AND ref_cod_escola = t.ref_ref_cod_escola)";
            $whereAnd = ' AND ';
        }

        if (!$this->listarNaoInformarEducacenso) {
            $filtros .= "{$whereAnd} COALESCE(t.nao_informar_educacenso,0) <> 1";
        }

        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();
        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} t {$filtros}");

        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     * (Modificação da lista2, agora trazendo somente turmas do ano atual)
     *
     * @return array|false
     */
    public function lista3($int_cod_turma = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_ref_cod_serie = null, $int_ref_ref_cod_escola = null, $int_ref_cod_infra_predio_comodo = null, $str_nm_turma = null, $str_sgl_turma = null, $int_max_aluno = null, $int_multiseriada = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_turma_tipo = null, $time_hora_inicial_ini = null, $time_hora_inicial_fim = null, $time_hora_final_ini = null, $time_hora_final_fim = null, $time_hora_inicio_intervalo_ini = null, $time_hora_inicio_intervalo_fim = null, $time_hora_fim_intervalo_ini = null, $time_hora_fim_intervalo_fim = null, $int_ref_cod_curso = null, $int_ref_cod_instituicao = null, $int_ref_cod_regente = null, $int_ref_cod_instituicao_regente = null, $int_ref_ref_cod_escola_mult = null, $int_ref_ref_cod_serie_mult = null, $int_qtd_min_alunos_matriculados = null, $visivel = null, $turma_turno_id = null, $tipo_boletim = null, $ano = null)
    {
        $sql = "SELECT {$this->_campos_lista},c.nm_curso,s.nm_serie,i.nm_instituicao FROM {$this->_tabela} t left outer join {$this->_schema}serie s on (t.ref_ref_cod_serie = s.cod_serie), {$this->_schema}curso c, {$this->_schema}instituicao i ";
        $filtros = '';

        $whereAnd = ' WHERE t.ref_cod_curso = c.cod_curso AND c.ref_cod_instituicao = i.cod_instituicao AND ';

        if (is_numeric($int_cod_turma)) {
            $filtros .= "{$whereAnd} t.cod_turma = '{$int_cod_turma}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} t.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} t.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_ref_cod_serie)) {
            $filtros .= "{$whereAnd}
                CASE
                    WHEN multiseriada = 1 THEN EXISTS (
                        SELECT 1
                        FROM pmieducar.turma_serie ts
                        WHERE ts.turma_id = t.cod_turma
                        AND ts.serie_id = {$int_ref_ref_cod_serie}
                    )
                    ELSE t.ref_ref_cod_serie = {$int_ref_ref_cod_serie}
                END";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_ref_cod_escola)) {
            $filtros .= "{$whereAnd} t.ref_ref_cod_escola = '{$int_ref_ref_cod_escola}'";
            $whereAnd = ' AND ';
        } elseif ($this->codUsuario) {
            $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                               FROM pmieducar.escola_usuario
                                                                                WHERE escola_usuario.ref_cod_escola = t.ref_ref_cod_escola
                                                                                  AND escola_usuario.ref_cod_usuario = '{$this->codUsuario}')";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_turma)) {
            $filtros .= "{$whereAnd} exists(select 1 from pmieducar.turma where unaccent(nm_turma) ILIKE unaccent('%{$str_nm_turma}%'))";
            $whereAnd = ' AND ';
        }
        if (is_string($str_sgl_turma)) {
            $filtros .= "{$whereAnd} t.sgl_turma LIKE '%{$str_sgl_turma}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_max_aluno)) {
            $filtros .= "{$whereAnd} t.max_aluno = '{$int_max_aluno}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_multiseriada)) {
            $filtros .= "{$whereAnd} t.multiseriada = '{$int_multiseriada}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} t.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} t.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} t.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} t.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} t.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} t.ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_turma_tipo)) {
            $filtros .= "{$whereAnd} t.ref_cod_turma_tipo = '{$int_ref_cod_turma_tipo}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_inicial_ini)) {
            $filtros .= "{$whereAnd} t.hora_inicial >= '{$time_hora_inicial_ini}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_inicial_fim)) {
            $filtros .= "{$whereAnd} t.hora_inicial <= '{$time_hora_inicial_fim}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_final_ini)) {
            $filtros .= "{$whereAnd} t.hora_final >= '{$time_hora_final_ini}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_final_fim)) {
            $filtros .= "{$whereAnd} t.hora_final <= '{$time_hora_final_fim}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_inicio_intervalo_ini)) {
            $filtros .= "{$whereAnd} t.hora_inicio_intervalo >= '{$time_hora_inicio_intervalo_ini}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_inicio_intervalo_fim)) {
            $filtros .= "{$whereAnd} t.hora_inicio_intervalo <= '{$time_hora_inicio_intervalo_fim}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_fim_intervalo_ini)) {
            $filtros .= "{$whereAnd} t.hora_fim_intervalo >= '{$time_hora_fim_intervalo_ini}'";
            $whereAnd = ' AND ';
        }
        if (($time_hora_fim_intervalo_fim)) {
            $filtros .= "{$whereAnd} t.hora_fim_intervalo <= '{$time_hora_fim_intervalo_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_regente)) {
            $filtros .= "{$whereAnd} t.ref_cod_regente = '{$int_ref_cod_regente}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao_regente)) {
            $filtros .= "{$whereAnd} t.ref_cod_instituicao_regente = '{$int_ref_cod_instituicao_regente}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} t.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_curso)) {
            $filtros .= "{$whereAnd}
                CASE
                    WHEN multiseriada = 1 THEN EXISTS (
                        SELECT 1
                        FROM pmieducar.turma_serie ts
                        JOIN pmieducar.serie s on s.cod_serie = ts.serie_id
                        WHERE ts.turma_id = t.cod_turma
                        AND s.ref_cod_curso = {$int_ref_cod_curso}
                    )
                    ELSE t.ref_cod_curso = {$int_ref_cod_curso}
                END";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_qtd_min_alunos_matriculados)) {
            $filtros .= "{$whereAnd} (SELECT COUNT(0) FROM pmieducar.matricula_turma WHERE ref_cod_turma = t.cod_turma) >= '{$int_qtd_min_alunos_matriculados}' ";
            $whereAnd = ' AND ';
        }
        if (is_bool($visivel)) {
            if ($visivel) {
                $filtros .= "{$whereAnd} t.visivel = TRUE";
                $whereAnd = ' AND ';
            } else {
                $filtros .= "{$whereAnd} t.visivel = FALSE";
                $whereAnd = ' AND ';
            }
        } elseif (is_array($visivel) && count($visivel)) {
            $filtros .= "{$whereAnd} t.visivel IN (" . implode(',', $visivel) . ')';
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} t.visivel = TRUE";
            $whereAnd = ' AND ';
        }

        if (is_numeric($turma_turno_id)) {
            $filtros .= "{$whereAnd} t.turma_turno_id = '{$turma_turno_id}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($tipo_boletim)) {
            $filtros .= "{$whereAnd} t.tipo_boletim = '{$tipo_boletim}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ano)) {
            $filtros .= "{$whereAnd} t.ano = '{$ano}'";
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} t left outer join {$this->_schema}serie s on (t.ref_ref_cod_serie = s.cod_serie), {$this->_schema}curso c , {$this->_schema}instituicao i {$filtros}");

        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();

                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array|false
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_turma)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} t WHERE t.cod_turma = '{$this->cod_turma}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    public function checaAnoLetivoEmAndamento()
    {
        if (is_numeric($this->cod_turma)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_turma = '{$this->cod_turma}' AND turma.ano in( SELECT ano FROM pmieducar.escola_ano_letivo enl WHERE enl.ref_cod_escola = turma.ref_ref_cod_escola AND andamento = 1)");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    public static function verificaDisciplinaDispensada($turmaId, $componenteId)
    {
        return static::getDisciplinaDispensada($turmaId) == $componenteId;
    }

    public static function getDisciplinaDispensada($turmaId)
    {
        $key = json_encode(compact('turmaId'));

        $disciplina_dispensada = Cache::store('array')->remember("getDisciplinaDispensada:{$key}", now()->addMinute(), function () use ($turmaId) {
            $sql = 'SELECT ref_cod_disciplina_dispensada as disciplina_dispensada FROM pmieducar.turma WHERE cod_turma = $1';

            $params = ['params' => $turmaId, 'return_only' => 'first-field'];

            return Portabilis_Utils_Database::fetchPreparedQuery($sql, $params) ?? 'null';
        });

        if ($disciplina_dispensada === 'null') {
            $disciplina_dispensada = null;
        }

        return $disciplina_dispensada;
    }

    public function possuiAlunosVinculados()
    {
        $sql = 'SELECT 1
              from pmieducar.matricula
             inner join pmieducar.matricula_turma on(matricula.cod_matricula = matricula_turma.ref_cod_matricula)
             where matricula.ativo = 1
               and matricula_turma.ref_cod_turma = $1
               and matricula_turma.ativo = 1';
        $params = ['params' => $this->cod_turma, 'return_only' => 'first-field'];

        return Portabilis_Utils_Database::fetchPreparedQuery($sql, $params);
    }

    public function getInep()
    {
        if ($this->cod_turma) {
            return SchoolClassInep::query()
                ->where('cod_turma', $this->cod_turma)
                ->whereNull('turma_turno_id')
                ->value('cod_turma_inep');
        }
    }

    public function getInepTurno(int $turmaTurnoId)
    {
        if ($this->cod_turma) {
            return SchoolClassInep::query()
                ->where('cod_turma', $this->cod_turma)
                ->where('turma_turno_id', $turmaTurnoId)
                ->value('cod_turma_inep');
        }
    }
}
