<?php

use iEducar\Legacy\Model;

class clsPmieducarCurso extends Model
{
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
    public $modalidade_curso;
    public $importar_curso_pre_matricula;
    public $descricao;

    public function __construct(
        $cod_curso = null,
        $ref_usuario_cad = null,
        $ref_cod_tipo_regime = null,
        $ref_cod_nivel_ensino = null,
        $ref_cod_tipo_ensino = null,
        $ref_cod_tipo_avaliacao = null,
        $nm_curso = null,
        $sgl_curso = null,
        $qtd_etapas = null,
        $frequencia_minima = null,
        $media = null,
        $media_exame = null,
        $falta_ch_globalizada = null,
        $carga_horaria = null,
        $ato_poder_publico = null,
        $edicao_final = null,
        $objetivo_curso = null,
        $publico_alvo = null,
        $data_cadastro = null,
        $data_exclusao = null,
        $ativo = null,
        $ref_usuario_exc = null,
        $ref_cod_instituicao = null,
        $padrao_ano_escolar = null,
        $hora_falta = null,
        $avaliacao_globalizada = null,
        $multi_seriado = null,
        $importar_curso_pre_matricula = null,
        $descricao = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = $this->_schema . 'curso';

        $this->_campos_lista = $this->_todos_campos = 'cod_curso, ref_usuario_cad, ref_cod_tipo_regime, ref_cod_nivel_ensino, ref_cod_tipo_ensino, nm_curso, sgl_curso, qtd_etapas, carga_horaria, ato_poder_publico, objetivo_curso, publico_alvo, data_cadastro, data_exclusao, ativo, ref_usuario_exc, ref_cod_instituicao, padrao_ano_escolar, hora_falta, multi_seriado, modalidade_curso, importar_curso_pre_matricula, descricao';

        if (is_numeric($ref_cod_instituicao)) {
            $this->ref_cod_instituicao = $ref_cod_instituicao;
        }

        if (is_numeric($ref_usuario_exc)) {
            $this->ref_usuario_exc = $ref_usuario_exc;
        }

        if (is_numeric($ref_cod_tipo_regime)) {
            $this->ref_cod_tipo_regime = $ref_cod_tipo_regime;
        }

        if (is_numeric($ref_cod_nivel_ensino)) {
            $this->ref_cod_nivel_ensino = $ref_cod_nivel_ensino;
        }

        if (is_numeric($ref_cod_tipo_ensino)) {
            $this->ref_cod_tipo_ensino = $ref_cod_tipo_ensino;
        }

        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }

        if (is_numeric($cod_curso)) {
            $this->cod_curso = $cod_curso;
        }

        if (is_string($nm_curso)) {
            $this->nm_curso = $nm_curso;
        }

        if (is_string($sgl_curso)) {
            $this->sgl_curso = $sgl_curso;
        }

        if (is_string($descricao)) {
            $this->descricao = $descricao;
        }

        if (is_numeric($qtd_etapas)) {
            $this->qtd_etapas = $qtd_etapas;
        }

        if (is_numeric($carga_horaria)) {
            $this->carga_horaria = $carga_horaria;
        }

        if (is_string($ato_poder_publico)) {
            $this->ato_poder_publico = $ato_poder_publico;
        }

        if (is_string($objetivo_curso)) {
            $this->objetivo_curso = $objetivo_curso;
        }

        if (is_string($publico_alvo)) {
            $this->publico_alvo = $publico_alvo;
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

        if (is_numeric($padrao_ano_escolar)) {
            $this->padrao_ano_escolar = $padrao_ano_escolar;
        }

        if (is_numeric($hora_falta)) {
            $this->hora_falta = $hora_falta;
        }

        $this->multi_seriado = $multi_seriado;
        $this->importar_curso_pre_matricula = $importar_curso_pre_matricula;
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_nivel_ensino) &&
            is_numeric($this->ref_cod_tipo_ensino) && is_string($this->nm_curso) &&
            is_string($this->sgl_curso) && is_numeric($this->qtd_etapas) &&
            is_numeric($this->carga_horaria) && is_numeric($this->ref_cod_instituicao)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_tipo_regime)) {
                $campos .= "{$gruda}ref_cod_tipo_regime";
                $valores .= "{$gruda}'{$this->ref_cod_tipo_regime}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_nivel_ensino)) {
                $campos .= "{$gruda}ref_cod_nivel_ensino";
                $valores .= "{$gruda}'{$this->ref_cod_nivel_ensino}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_tipo_ensino)) {
                $campos .= "{$gruda}ref_cod_tipo_ensino";
                $valores .= "{$gruda}'{$this->ref_cod_tipo_ensino}'";
                $gruda = ', ';
            }

            if (is_string($this->nm_curso)) {
                $nm_curso = $db->escapeString($this->nm_curso);
                $campos .= "{$gruda}nm_curso";
                $valores .= "{$gruda}'{$nm_curso}'";
                $gruda = ', ';
            }

            if (is_string($this->sgl_curso)) {
                $sgl_curso = $db->escapeString($this->sgl_curso);
                $campos .= "{$gruda}sgl_curso";
                $valores .= "{$gruda}'{$sgl_curso}'";
                $gruda = ', ';
            }

            if (is_string($this->descricao)) {
                $descricao = $db->escapeString($this->descricao);
                $campos .= "{$gruda}descricao";
                $valores .= "{$gruda}'{$descricao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->qtd_etapas)) {
                $campos .= "{$gruda}qtd_etapas";
                $valores .= "{$gruda}'{$this->qtd_etapas}'";
                $gruda = ', ';
            }

            if (is_numeric($this->carga_horaria)) {
                $campos .= "{$gruda}carga_horaria";
                $valores .= "{$gruda}'{$this->carga_horaria}'";
                $gruda = ', ';
            }

            if (is_string($this->ato_poder_publico)) {
                $ato_poder_publico = $db->escapeString($this->ato_poder_publico);
                $campos .= "{$gruda}ato_poder_publico";
                $valores .= "{$gruda}'{$ato_poder_publico}'";
                $gruda = ', ';
            }

            if (is_string($this->objetivo_curso)) {
                $objetivo_curso = $db->escapeString($this->objetivo_curso);
                $campos .= "{$gruda}objetivo_curso";
                $valores .= "{$gruda}'{$objetivo_curso}'";
                $gruda = ', ';
            }

            if (is_string($this->publico_alvo)) {
                $publico_alvo = $db->escapeString($this->publico_alvo);
                $campos .= "{$gruda}publico_alvo";
                $valores .= "{$gruda}'{$publico_alvo}'";
                $gruda = ', ';
            }

            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';

            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';

            if (is_numeric($this->ref_cod_instituicao)) {
                $campos .= "{$gruda}ref_cod_instituicao";
                $valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->padrao_ano_escolar)) {
                $campos .= "{$gruda}padrao_ano_escolar";
                $valores .= "{$gruda}'{$this->padrao_ano_escolar}'";
                $gruda = ', ';
            }

            if (is_numeric($this->hora_falta)) {
                $campos .= "{$gruda}hora_falta";
                $valores .= "{$gruda}'{$this->hora_falta}'";
                $gruda = ', ';
            }

            if (is_numeric($this->multi_seriado)) {
                $campos .= "{$gruda}multi_seriado";
                $valores .= "{$gruda}'{$this->multi_seriado}'";
                $gruda = ', ';
            }

            if (is_numeric($this->importar_curso_pre_matricula)) {
                $campos .= "{$gruda}importar_curso_pre_matricula";
                $valores .= "{$gruda}'{$this->importar_curso_pre_matricula}'";
                $gruda = ', ';
            }

            if (is_numeric($this->modalidade_curso)) {
                $campos .= "{$gruda}modalidade_curso";
                $valores .= "{$gruda}'{$this->modalidade_curso}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_curso_seq");
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
        $gruda = '';
        if (is_numeric($this->cod_curso) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_tipo_regime)) {
                $set .= "{$gruda}ref_cod_tipo_regime = '{$this->ref_cod_tipo_regime}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_nivel_ensino)) {
                $set .= "{$gruda}ref_cod_nivel_ensino = '{$this->ref_cod_nivel_ensino}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_tipo_ensino)) {
                $set .= "{$gruda}ref_cod_tipo_ensino = '{$this->ref_cod_tipo_ensino}'";
                $gruda = ', ';
            }

            if (is_string($this->nm_curso)) {
                $nm_curso = $db->escapeString($this->nm_curso);
                $set .= "{$gruda}nm_curso = '{$nm_curso}'";
                $gruda = ', ';
            }

            if (is_string($this->sgl_curso)) {
                $sgl_curso = $db->escapeString($this->sgl_curso);
                $set .= "{$gruda}sgl_curso = '{$sgl_curso}'";
                $gruda = ', ';
            }

            if (is_string($this->descricao)) {
                $descricao = $db->escapeString($this->descricao);
                $set .= "{$gruda}descricao = '{$descricao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->qtd_etapas)) {
                $set .= "{$gruda}qtd_etapas = '{$this->qtd_etapas}'";
                $gruda = ', ';
            }

            if (is_numeric($this->carga_horaria)) {
                $set .= "{$gruda}carga_horaria = '{$this->carga_horaria}'";
                $gruda = ', ';
            }

            if (is_string($this->ato_poder_publico)) {
                $ato_poder_publico = $db->escapeString($this->ato_poder_publico);
                $set .= "{$gruda}ato_poder_publico = '{$ato_poder_publico}'";
                $gruda = ', ';
            }

            if (is_string($this->objetivo_curso)) {
                $objetivo_curso = $db->escapeString($this->objetivo_curso);
                $set .= "{$gruda}objetivo_curso = '{$objetivo_curso}'";
                $gruda = ', ';
            }

            if (is_string($this->publico_alvo)) {
                $publico_alvo = $db->escapeString($this->publico_alvo);
                $set .= "{$gruda}publico_alvo = '{$publico_alvo}'";
                $gruda = ', ';
            }

            if (is_string($this->data_cadastro)) {
                $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
                $gruda = ', ';
            }

            $set .= "{$gruda}data_exclusao = NOW()";
            $gruda = ', ';

            if (is_numeric($this->ativo)) {
                $set .= "{$gruda}ativo = '{$this->ativo}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_instituicao)) {
                $set .= "{$gruda}ref_cod_instituicao = '{$this->ref_cod_instituicao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->padrao_ano_escolar)) {
                $set .= "{$gruda}padrao_ano_escolar = '{$this->padrao_ano_escolar}'";
                $gruda = ', ';
            }

            if (is_numeric($this->hora_falta)) {
                $set .= "{$gruda}hora_falta = '{$this->hora_falta}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}hora_falta = 0";
                $gruda = ', ';
            }

            if (is_numeric($this->multi_seriado)) {
                $set .= "{$gruda}multi_seriado = '{$this->multi_seriado}'";
                $gruda = ', ';
            }

            if (is_numeric($this->importar_curso_pre_matricula)) {
                $set .= "{$gruda}importar_curso_pre_matricula = '{$this->importar_curso_pre_matricula}'";
                $gruda = ', ';
            }

            if (is_numeric($this->modalidade_curso)) {
                $set .= "{$gruda}modalidade_curso = '{$this->modalidade_curso}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_curso = '{$this->cod_curso}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @return array
     */
    public function lista(
        $int_cod_curso = null,
        $int_ref_usuario_cad = null,
        $int_ref_cod_tipo_regime = null,
        $int_ref_cod_nivel_ensino = null,
        $int_ref_cod_tipo_ensino = null,
        $int_ref_cod_tipo_avaliacao = null,
        $str_nm_curso = null,
        $str_sgl_curso = null,
        $int_qtd_etapas = null,
        $int_frequencia_minima = null,
        $int_media = null,
        $int_media_exame = null,
        $int_falta_ch_globalizada = null,
        $int_carga_horaria = null,
        $str_ato_poder_publico = null,
        $int_edicao_final = null,
        $str_objetivo_curso = null,
        $str_publico_alvo = null,
        $date_data_cadastro_ini = null,
        $date_data_cadastro_fim = null,
        $date_data_exclusao_ini = null,
        $date_data_exclusao_fim = null,
        $int_ativo = null,
        $int_ref_usuario_exc = null,
        $int_ref_cod_instituicao = null,
        $int_padrao_ano_escolar = null,
        $int_hora_falta = null,
        $bool_avaliacao_globalizada = null
    ) {
        $db = new clsBanco();

        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_curso)) {
            $filtros .= "{$whereAnd} cod_curso = '{$int_cod_curso}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_tipo_regime)) {
            $filtros .= "{$whereAnd} ref_cod_tipo_regime = '{$int_ref_cod_tipo_regime}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_nivel_ensino)) {
            $filtros .= "{$whereAnd} ref_cod_nivel_ensino = '{$int_ref_cod_nivel_ensino}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_tipo_ensino)) {
            $filtros .= "{$whereAnd} ref_cod_tipo_ensino = '{$int_ref_cod_tipo_ensino}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_nm_curso)) {
            $str_nome_curso = $db->escapeString($str_nm_curso);
            $filtros .= "{$whereAnd} translate(upper(nm_curso),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$str_nome_curso}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
            $whereAnd = ' AND ';
        }

        if (is_string($str_sgl_curso)) {
            $filtros .= "{$whereAnd} sgl_curso LIKE '%{$str_sgl_curso}%'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_qtd_etapas)) {
            $filtros .= "{$whereAnd} qtd_etapas = '{$int_qtd_etapas}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_carga_horaria)) {
            $filtros .= "{$whereAnd} carga_horaria = '{$int_carga_horaria}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_ato_poder_publico)) {
            $filtros .= "{$whereAnd} ato_poder_publico LIKE '%{$str_ato_poder_publico}%'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_objetivo_curso)) {
            $filtros .= "{$whereAnd} objetivo_curso LIKE '%{$str_objetivo_curso}%'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_publico_alvo)) {
            $filtros .= "{$whereAnd} publico_alvo LIKE '%{$str_publico_alvo}%'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} ativo = '0'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_padrao_ano_escolar)) {
            $filtros .= "{$whereAnd} padrao_ano_escolar = '{$int_padrao_ano_escolar}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_hora_falta)) {
            $filtros .= "{$whereAnd} hora_falta = '{$int_hora_falta}'";
            $whereAnd = ' AND ';
        }

        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

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
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_curso)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos},fcn_upper(nm_curso) as nm_curso_upper FROM {$this->_tabela} WHERE cod_curso = '{$this->cod_curso}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro ou FALSE caso não exista.
     *
     * @return array|bool
     */
    public function existe()
    {
        if (is_numeric($this->cod_curso)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_curso = '{$this->cod_curso}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    public function cursoDeAtividadeComplementar()
    {
        if (is_numeric($this->cod_curso)) {
            $db = new clsBanco();

            $sql = "SELECT 1
                FROM {$this->_tabela}
               INNER JOIN pmieducar.tipo_ensino ON (tipo_ensino.cod_tipo_ensino = curso.ref_cod_tipo_ensino)
               WHERE tipo_ensino.atividade_complementar = TRUE
                 AND cod_curso =" . $this->cod_curso;
            $cursoDeAtividadeComplementar = $db->CampoUnico($sql);

            if ($cursoDeAtividadeComplementar) {
                return true;
            }

            return false;
        }
    }

    /**
     * Exclui um registro.
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->cod_curso) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }
}
