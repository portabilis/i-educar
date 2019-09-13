<?php

use iEducar\Legacy\Model;

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarCliente extends Model
{
    public $cod_cliente;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_idpes;
    public $login;
    public $senha;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $observacoes;

    public function __construct(
        $cod_cliente = null,
        $ref_usuario_exc = null,
        $ref_usuario_cad = null,
        $ref_idpes = null,
        $login = null,
        $senha = null,
        $data_cadastro = null,
        $data_exclusao = null,
        $ativo = null,
        $observacoes = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}cliente";

        $this->_campos_lista = $this->_todos_campos = 'c.cod_cliente, c.ref_usuario_exc, c.ref_usuario_cad, c.ref_idpes, c.login, c.senha, c.data_cadastro, c.data_exclusao, c.ativo, c.observacoes';

        if (is_numeric($ref_usuario_cad)) {
                    $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_usuario_exc)) {
                    $this->ref_usuario_exc = $ref_usuario_exc;
        }
        if (is_numeric($ref_idpes)) {
                    $this->ref_idpes = $ref_idpes;
        }

        if (is_numeric($cod_cliente)) {
            $this->cod_cliente = $cod_cliente;
        }
        if (is_numeric($login)) {
            $this->login = $login;
        }
        if (is_string($senha)) {
            $this->senha = $senha;
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
        if (is_string($observacoes)) {
            $this->observacoes = $observacoes;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_idpes)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_idpes)) {
                $campos .= "{$gruda}ref_idpes";
                $valores .= "{$gruda}'{$this->ref_idpes}'";
                $gruda = ', ';
            }
            if (is_numeric($this->login)) {
                $campos .= "{$gruda}login";
                $valores .= "{$gruda}'{$this->login}'";
                $gruda = ', ';
            }
            if (is_string($this->senha)) {
                $campos .= "{$gruda}senha";
                $valores .= "{$gruda}'{$this->senha}'";
                $gruda = ', ';
            }
            $campos .= "{$gruda}data_cadastro";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';
            $campos .= "{$gruda}ativo";
            $valores .= "{$gruda}'1'";
            $gruda = ', ';
            if (is_string($this->observacoes)) {
                $campos .= "{$gruda}observacoes";
                $valores .= "{$gruda}'{$this->observacoes}'";
                $gruda = ', ';
            }

            // echo $this->observacoes; die;

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_cliente_seq");
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
        if (is_numeric($this->cod_cliente) && is_numeric($this->ref_usuario_exc)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_usuario_exc)) {
                $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_idpes)) {
                $set .= "{$gruda}ref_idpes = '{$this->ref_idpes}'";
                $gruda = ', ';
            }
            if (is_numeric($this->login)) {
                $set .= "{$gruda}login = '{$this->login}'";
                $gruda = ', ';
            }
            if (is_string($this->senha)) {
                $set .= "{$gruda}senha = '{$this->senha}'";
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
            if (is_string($this->observacoes)) {
                $set .= "{$gruda}observacoes = '{$this->observacoes}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_cliente = '{$this->cod_cliente}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function lista($int_cod_cliente = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_idpes = null, $int_login = null, $str_senha = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $str_nm_cliente = null, $str_suspenso = null, $int_ref_cod_biblioteca = null)
    {
        $tab_adicional = '';
        $condicao = '';
        $camp_adicional = '';
        if (is_string($str_suspenso)) {
            $tab_adicional .= ", {$this->_schema}cliente_suspensao cs ";
            $condicao .= ' AND c.cod_cliente = cs.ref_cod_cliente ';
        }
        if (is_numeric($int_ref_cod_biblioteca) || is_array($int_ref_cod_biblioteca)) {
            $tab_adicional .= ", {$this->_schema}cliente_tipo ct ";
            $tab_adicional .= ", {$this->_schema}cliente_tipo_cliente ctc ";
        }
        $sql = "SELECT {$this->_campos_lista}, p.nome{$camp_adicional} FROM {$this->_tabela} c, cadastro.pessoa p {$tab_adicional}";
        $whereAnd = ' AND ';

        $filtros = "WHERE c.ref_idpes = p.idpes {$condicao}";

        if (is_numeric($int_cod_cliente)) {
            $filtros .= "{$whereAnd} c.cod_cliente = '{$int_cod_cliente}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} c.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} c.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_idpes)) {
            $filtros .= "{$whereAnd} c.ref_idpes = '{$int_ref_idpes}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_login)) {
            $filtros .= "{$whereAnd} c.login = '{$int_login}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_senha)) {
            $filtros .= "{$whereAnd} c.senha = '{$str_senha}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} c.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} c.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} c.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} c.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} c.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} c.ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_cliente)) {
            $filtros .= "{$whereAnd} translate(upper(p.nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$str_nm_cliente}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
            $whereAnd = ' AND ';
        }
        if (is_array($int_ref_cod_biblioteca)) {
            $bibs = implode(', ', $int_ref_cod_biblioteca);
            $filtros .= "{$whereAnd} c.cod_cliente = ctc.ref_cod_cliente AND ctc.ref_cod_cliente_tipo = ct.cod_cliente_tipo AND ct.ref_cod_biblioteca IN ($bibs) ";
            $whereAnd = ' AND ';
        } elseif (is_numeric($int_ref_cod_biblioteca)) {
            $filtros .= "{$whereAnd} c.cod_cliente = ctc.ref_cod_cliente AND ctc.ref_cod_cliente_tipo = ct.cod_cliente_tipo AND ct.ref_cod_biblioteca = '$int_ref_cod_biblioteca' ";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} c, cadastro.pessoa p{$tab_adicional} {$filtros}");

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
     * Retorna uma lista filtrada de acordo com os argumentos.
     *
     * @return Array
     */
    public function listaCompleta(
        $int_cod_cliente = null,
        $int_ref_usuario_exc = null,
        $int_ref_usuario_cad = null,
        $int_ref_idpes = null,
        $int_login = null,
        $str_senha = null,
        $date_data_cadastro_ini = null,
        $date_data_cadastro_fim = null,
        $date_data_exclusao_ini = null,
        $date_data_exclusao_fim = null,
        $int_ativo = 1,
        $str_nm_cliente = null,
        $str_suspenso = null,
        $int_cod_cliente_tipo = null,
        $int_cod_escola = null,
        $int_cod_biblioteca = null,
        $int_cod_instituicao = null
    ) {
        $db = new clsBanco();

        $filtros = '';
        $whereAnd = ' AND ';

        if (is_numeric($int_cod_cliente)) {
            $filtros .= "{$whereAnd} c.cod_cliente = '{$int_cod_cliente}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} c.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} c.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_idpes)) {
            $filtros .= "{$whereAnd} c.ref_idpes = '{$int_ref_idpes}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_login)) {
            $filtros .= "{$whereAnd} c.login = '{$int_login}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_senha)) {
            $filtros .= "{$whereAnd} c.senha = '{$str_senha}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} c.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} c.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} c.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} c.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} c.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} c.ativo = '0'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_nm_cliente)) {
            $filtros .= "{$whereAnd} translate(upper(p.nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$str_nm_cliente}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_cod_cliente_tipo)) {
            $filtros .= "{$whereAnd} ct.cod_cliente_tipo = '{$int_cod_cliente_tipo}'";
            $whereAnd = ' AND ';
        }

        if (is_array($int_cod_biblioteca)) {
            $array_biblioteca = implode(', ', $int_cod_biblioteca);
            $filtros .= "{$whereAnd} b.cod_biblioteca IN ({$array_biblioteca})";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_cod_biblioteca)) {
            $filtros .= "{$whereAnd} b.cod_biblioteca = '{$int_cod_biblioteca}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_cod_escola)) {
            $filtros .= "{$whereAnd} e.cod_escola = '{$int_cod_escola}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_cod_instituicao)) {
            $filtros .= "{$whereAnd} i.cod_instituicao = '{$int_cod_instituicao}'";
            $whereAnd = ' AND ';
        }

        // se S(suspenso) ou R(egular), filtra por tal situacao
        if (in_array($str_suspenso, ['S', 'R'])) {
            $existencia = $str_suspenso == 'R' ? 'not' : '';
            $condicaoSuspenso = " AND $existencia exists (select 1 from pmieducar.cliente_suspensao where ref_cod_cliente = c.cod_cliente and data_liberacao is null and data_suspensao + (dias||' day')::interval >= now())";
        }

        $db = new clsBanco();
        $resultado = [];

        $select = '
            SELECT
              c.cod_cliente,
              c.ref_idpes,
              c.ref_usuario_cad,
              c.login,
              p.nome,
              ct.nm_tipo,
              ct.cod_cliente_tipo,
              b.nm_biblioteca,
              b.cod_biblioteca,
              e.cod_escola as cod_escola,
              i.cod_instituicao,
              (SELECT \'S\'::text
                FROM pmieducar.cliente_suspensao cs
                WHERE cs.ref_cod_cliente = c.cod_cliente
                AND cs.data_liberacao IS NULL LIMIT 1) AS id_suspensao ';
        $sql = "
            FROM
              pmieducar.cliente                c,
              pmieducar.cliente_tipo_cliente ctc,
              pmieducar.cliente_tipo          ct,
              pmieducar.biblioteca             b,
              pmieducar.escola                 e,
              pmieducar.instituicao            i,
              cadastro.pessoa                  p
            WHERE
              c.cod_cliente             = ctc.ref_cod_cliente
              AND ct.cod_cliente_tipo   = ctc.ref_cod_cliente_tipo
              AND b.cod_biblioteca      = ct.ref_cod_biblioteca
              AND e.cod_escola          = b.ref_cod_escola
              AND i.cod_instituicao     = b.ref_cod_instituicao
              AND e.ref_cod_instituicao = i.cod_instituicao{$condicaoSuspenso}
              AND p.idpes               = c.ref_idpes
              AND c.ativo               = '{$int_ativo}'
              AND ctc.ativo             = '{$int_ativo}'
              $filtros";

        $this->_total = $db->CampoUnico('SELECT COUNT(0) ' . $sql);

        $sql .= $this->getOrderby() . $this->getLimite();
        $db->Consulta($select . $sql);

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $resultado[] = $tupla;
        }

        if (count($resultado) > 0) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna uma lista filtrados de acordo com os parametros
     *
     * @return array
     */
    public function listaPesquisaCliente($int_cod_cliente = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_idpes = null, $int_login = null, $str_senha = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $str_nm_cliente = null, $int_ref_cod_biblioteca = null)
    {
        $sql = "SELECT {$this->_campos_lista}, ct.ref_cod_biblioteca, p.nome FROM {$this->_tabela} c, {$this->_schema}cliente_tipo_cliente ctc, {$this->_schema}cliente_tipo ct, cadastro.pessoa p";
        $filtros = '';

        $whereAnd = ' WHERE c.cod_cliente = ctc.ref_cod_cliente AND ctc.ref_cod_cliente_tipo = ct.cod_cliente_tipo AND c.ref_idpes = p.idpes AND';

        if (is_numeric($int_cod_cliente)) {
            $filtros .= "{$whereAnd} c.cod_cliente = '{$int_cod_cliente}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} c.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} c.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_idpes)) {
            $filtros .= "{$whereAnd} c.ref_idpes = '{$int_ref_idpes}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_login)) {
            $filtros .= "{$whereAnd} c.login = '{$int_login}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_senha)) {
            $filtros .= "{$whereAnd} c.senha = '{$str_senha}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} c.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} c.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} c.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} c.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_null($int_ativo) || $int_ativo) {
            $filtros .= "{$whereAnd} c.ativo = '1'";
            $whereAnd = ' AND ';
        } else {
            $filtros .= "{$whereAnd} c.ativo = '0'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_nm_cliente)) {
            $filtros .= "{$whereAnd} p.nome LIKE '%{$str_nm_cliente}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_biblioteca)) {
            $filtros .= "{$whereAnd} ct.ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} c, {$this->_schema}cliente_tipo_cliente ctc, {$this->_schema}cliente_tipo ct, cadastro.pessoa p {$filtros}");

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
     * @return array
     */
    public function detalhe()
    {
        if (is_numeric($this->cod_cliente)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} c WHERE c.cod_cliente = '{$this->cod_cliente}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        } elseif (is_numeric($this->ref_idpes)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} c WHERE c.ref_idpes = '{$this->ref_idpes}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->cod_cliente)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_cliente = '{$this->cod_cliente}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->cod_cliente) && is_numeric($this->ref_usuario_exc)) {
            $this->ativo = 0;

            return $this->edita();
        }

        return false;
    }

    /**
     * Retorna um array com o codigo do tipo de cliente e o nome do tipo de cliente
     *
     * @return array
     */
    public function retornaTipoCliente($int_cod_cliente, $int_cod_biblioteca)
    {
        if (is_numeric($int_cod_cliente) && is_numeric($int_cod_biblioteca)) {
            $db = new clsBanco();
            $db->Consulta("SELECT ct.cod_cliente_tipo,
                                   ct.nm_tipo
                              FROM pmieducar.cliente                c,
                                   pmieducar.cliente_tipo_cliente ctc,
                                   pmieducar.cliente_tipo          ct
                             WHERE c.cod_cliente         = ctc.ref_cod_cliente
                               AND ct.cod_cliente_tipo   = ctc.ref_cod_cliente_tipo
                               AND c.cod_cliente         = '{$int_cod_cliente}'
                               AND ct.ref_cod_biblioteca = '{$int_cod_biblioteca}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }
}
