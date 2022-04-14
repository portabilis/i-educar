<?php

use iEducar\Legacy\Model;

class clsPmieducarExemplarEmprestimo extends Model
{
    public $cod_emprestimo;
    public $ref_usuario_devolucao;
    public $ref_usuario_cad;
    public $ref_cod_cliente;
    public $ref_cod_exemplar;
    public $data_retirada;
    public $data_devolucao;
    public $valor_multa;
    public $ref_cod_biblioteca;
    public $em_atraso;

    public function __construct($cod_emprestimo = null, $ref_usuario_devolucao = null, $ref_usuario_cad = null, $ref_cod_cliente = null, $ref_cod_exemplar = null, $data_retirada = null, $data_devolucao = null, $valor_multa = null, $ref_cod_biblioteca = null)
    {
        $db = new clsBanco();
        $this->_schema = 'pmieducar.';
        $this->_tabela = "{$this->_schema}exemplar_emprestimo";

        $this->_campos_lista = $this->_todos_campos = 'ee.cod_emprestimo, ee.ref_usuario_devolucao, ee.ref_usuario_cad, ee.ref_cod_cliente, ee.ref_cod_exemplar, ee.data_retirada, ee.data_devolucao, ee.valor_multa';

        if (is_numeric($ref_usuario_devolucao)) {
            $this->ref_usuario_devolucao = $ref_usuario_devolucao;
        }
        if (is_numeric($ref_usuario_cad)) {
            $this->ref_usuario_cad = $ref_usuario_cad;
        }
        if (is_numeric($ref_cod_cliente)) {
            $this->ref_cod_cliente = $ref_cod_cliente;
        }
        if (is_numeric($ref_cod_exemplar)) {
            $this->ref_cod_exemplar = $ref_cod_exemplar;
        }

        if (is_numeric($cod_emprestimo)) {
            $this->cod_emprestimo = $cod_emprestimo;
        }
        if (is_string($data_retirada)) {
            $this->data_retirada = $data_retirada;
        }
        if (is_string($data_devolucao)) {
            $this->data_devolucao = $data_devolucao;
        }
        if (is_numeric($valor_multa)) {
            $this->valor_multa = $valor_multa;
        }
        if (is_numeric($ref_cod_biblioteca)) {
            $this->ref_cod_biblioteca = $ref_cod_biblioteca;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_cliente) && is_numeric($this->ref_cod_exemplar)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_usuario_devolucao)) {
                $campos .= "{$gruda}ref_usuario_devolucao";
                $valores .= "{$gruda}'{$this->ref_usuario_devolucao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $campos .= "{$gruda}ref_usuario_cad";
                $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
                $gruda = ', ';

                $campos .= "{$gruda}data_retirada";
                $valores .= "{$gruda}NOW()";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_cliente)) {
                $campos .= "{$gruda}ref_cod_cliente";
                $valores .= "{$gruda}'{$this->ref_cod_cliente}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_exemplar)) {
                $campos .= "{$gruda}ref_cod_exemplar";
                $valores .= "{$gruda}'{$this->ref_cod_exemplar}'";
                $gruda = ', ';
            }
            if (is_string($this->data_devolucao)) {
                $campos .= "{$gruda}data_devolucao";
                $valores .= "{$gruda}'{$this->data_devolucao}'";
                $gruda = ', ';

                $campos .= "{$gruda}data_cadastro";
                $valores .= "{$gruda}NOW()";
                $gruda = ', ';
            }
            if (is_numeric($this->valor_multa)) {
                $campos .= "{$gruda}valor_multa";
                $valores .= "{$gruda}'{$this->valor_multa}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            return $db->InsertId("{$this->_tabela}_cod_emprestimo_seq");
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
        if (is_numeric($this->cod_emprestimo)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_usuario_devolucao)) {
                $set .= "{$gruda}ref_usuario_devolucao = '{$this->ref_usuario_devolucao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_usuario_cad)) {
                $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_cliente)) {
                $set .= "{$gruda}ref_cod_cliente = '{$this->ref_cod_cliente}'";
                $gruda = ', ';
            }
            if (is_numeric($this->ref_cod_exemplar)) {
                $set .= "{$gruda}ref_cod_exemplar = '{$this->ref_cod_exemplar}'";
                $gruda = ', ';
            }
            if (is_string($this->data_retirada)) {
                $set .= "{$gruda}data_retirada = '{$this->data_retirada}'";
                $gruda = ', ';
            }
            if (is_string($this->data_devolucao)) {
                $set .= "{$gruda}data_devolucao = '{$this->data_devolucao}'";
                $gruda = ', ';
            }
            if (is_numeric($this->valor_multa)) {
                $set .= "{$gruda}valor_multa = '{$this->valor_multa}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_emprestimo = '{$this->cod_emprestimo}'");

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
    public function lista(
        $int_cod_emprestimo = null,
        $int_ref_usuario_devolucao = null,
        $int_ref_usuario_cad = null,
        $int_ref_cod_cliente = null,
        $int_ref_cod_exemplar = null,
        $date_data_retirada_ini = null,
        $date_data_retirada_fim = null,
        $date_data_devolucao_ini = null,
        $date_data_devolucao_fim = null,
        $int_valor_multa = null,
        $devolvido = false,
        $int_ref_cod_biblioteca = null,
        $multa = false,
        $int_ref_cod_instituicao = null,
        $int_ref_cod_escola = null,
        $str_titulo_exemplar = null,
        $tombo = null
    ) {
        $sql = "SELECT {$this->_campos_lista}, a.ref_cod_biblioteca, b.ref_cod_instituicao, b.ref_cod_escola, b.bloqueia_emprestimo_em_atraso
                  FROM {$this->_tabela} ee,
                                 {$this->_schema}exemplar e,
                                     {$this->_schema}acervo a,
                                     {$this->_schema}biblioteca b,
                                 {$this->_schema}cliente c,
                                 {$this->_schema}cliente_tipo_cliente ctc,
                                 {$this->_schema}cliente_tipo_exemplar_tipo ctet";

        $whereAnd = ' AND ';

        $filtros = ' WHERE ee.ref_cod_exemplar = e.cod_exemplar
                       AND e.ref_cod_acervo = a.cod_acervo
                                     AND a.ref_cod_biblioteca = b.cod_biblioteca
                                 AND c.cod_cliente = ee.ref_cod_cliente
                                 AND ctc.ref_cod_cliente = c.cod_cliente
                                 AND ctet.ref_cod_cliente_tipo = ctc.ref_cod_cliente_tipo
                                 AND ctet.ref_cod_exemplar_tipo = a.ref_cod_exemplar_tipo';

        if (is_numeric($int_cod_emprestimo)) {
            $filtros .= "{$whereAnd} ee.cod_emprestimo = '{$int_cod_emprestimo}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_devolucao)) {
            $filtros .= "{$whereAnd} ee.ref_usuario_devolucao = '{$int_ref_usuario_devolucao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} ee.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_cliente)) {
            $filtros .= "{$whereAnd} ee.ref_cod_cliente = '{$int_ref_cod_cliente}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_exemplar)) {
            $filtros .= "{$whereAnd} ee.ref_cod_exemplar = '{$int_ref_cod_exemplar}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_retirada_ini)) {
            $filtros .= "{$whereAnd} ee.data_retirada >= '{$date_data_retirada_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_retirada_fim)) {
            $filtros .= "{$whereAnd} ee.data_retirada <= '{$date_data_retirada_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_devolucao_ini)) {
            $filtros .= "{$whereAnd} ee.data_devolucao >= '{$date_data_devolucao_ini}'";
            $whereAnd = ' AND ';
        }
        if (is_string($date_data_devolucao_fim)) {
            $filtros .= "{$whereAnd} ee.data_devolucao <= '{$date_data_devolucao_fim}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_valor_multa)) {
            $filtros .= "{$whereAnd} ee.valor_multa = '{$int_valor_multa}'";
            $whereAnd = ' AND ';
        }
        if (!is_null($devolvido)) {
            if ($devolvido) {
                $filtros .= "{$whereAnd} ee.data_devolucao IS NOT NULL";
            } else {
                $filtros .= "{$whereAnd} ee.data_devolucao IS NULL";
            }
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_biblioteca)) {
            $filtros .= "{$whereAnd} a.ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
            $whereAnd = ' AND ';
        }
        if (!is_null($multa)) {
            if ($multa) {
                $filtros .= "{$whereAnd} ee.valor_multa IS NOT NULL";
                $whereAnd = ' AND ';
            }
        }
        if (is_numeric($int_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} b.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} b.ref_cod_escola = '{$int_ref_cod_escola}'";
            $whereAnd = ' AND ';
        }
        if (is_string($str_titulo_exemplar)) {
            $filtros .= "{$whereAnd} a.titulo LIKE '%{$str_titulo_exemplar}%'";
            $whereAnd = ' AND ';
        }
        if (is_numeric($tombo)) {
            $filtros .= "{$whereAnd} e.tombo = {$tombo} ";
            $whereAnd = ' AND ';
        }

        if ($this->em_atraso) {
            $filtros .= "{$whereAnd} CURRENT_DATE > (ee.data_retirada::date + ctet.dias_emprestimo::integer)";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0)
                                            FROM {$this->_tabela} ee,
                                                                                         {$this->_schema}exemplar e,
                                                                                         {$this->_schema}acervo a,
                                                                                         {$this->_schema}biblioteca b,
                                                                                         {$this->_schema}cliente c,
                                                                                         {$this->_schema}cliente_tipo_cliente ctc,
                                                                                         {$this->_schema}cliente_tipo_exemplar_tipo ctet {$filtros}");

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
        if (is_numeric($this->cod_emprestimo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} ee WHERE ee.cod_emprestimo = '{$this->cod_emprestimo}'");
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
        if (is_numeric($this->cod_emprestimo)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_emprestimo = '{$this->cod_emprestimo}'");
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
        if (is_numeric($this->cod_emprestimo)) {
        }

        return false;
    }

    public function clienteDividaTotal($int_idpes = null, $int_cod_cliente = null, $int_cod_cliente_tipo = null, $int_cod_biblioteca = null, $int_cod_escola = null, $int_cod_instituicao = null, $int_valor = null)
    {
        $db = new clsBanco();

        $sql = 'SELECT c.cod_cliente,
                       c.ref_idpes,
                       sum( ee.valor_multa ) AS valor_multa,
                       ( SELECT sum( pm.valor_pago )
                           FROM pmieducar.pagamento_multa pm
                          WHERE pm.ref_cod_cliente = c.cod_cliente ) AS valor_pago,
                       b.cod_biblioteca,
                       b.nm_biblioteca,
                       e.cod_escola,
                       i.nm_instituicao
                  FROM pmieducar.exemplar_emprestimo   ee,
                       pmieducar.cliente                c,
                       pmieducar.cliente_tipo_cliente ctc,
                       pmieducar.cliente_tipo          ct,
                       pmieducar.biblioteca             b,
                       pmieducar.escola                 e,
                       pmieducar.instituicao            i
                 WHERE c.cod_cliente            = ee.ref_cod_cliente
                   AND c.cod_cliente            = ctc.ref_cod_cliente
                   AND ctc.ref_cod_cliente_tipo = ct.cod_cliente_tipo
                   AND ct.ref_cod_biblioteca    = b.cod_biblioteca
                   AND b.ref_cod_escola         = e.cod_escola
                   AND b.ref_cod_instituicao    = i.cod_instituicao
                   AND e.ref_cod_instituicao    = i.cod_instituicao';

        $sql2 = 'SELECT count( DISTINCT c.cod_cliente )
                  FROM pmieducar.exemplar_emprestimo   ee,
                       pmieducar.cliente                c,
                       pmieducar.cliente_tipo_cliente ctc,
                       pmieducar.cliente_tipo          ct,
                       pmieducar.biblioteca             b,
                       pmieducar.escola                 e,
                       pmieducar.instituicao            i
                 WHERE c.cod_cliente            = ee.ref_cod_cliente
                   AND c.cod_cliente            = ctc.ref_cod_cliente
                   AND ctc.ref_cod_cliente_tipo = ct.cod_cliente_tipo
                   AND ct.ref_cod_biblioteca    = b.cod_biblioteca
                   AND b.ref_cod_escola         = e.cod_escola
                   AND b.ref_cod_instituicao    = i.cod_instituicao
                   AND e.ref_cod_instituicao    = i.cod_instituicao';
        if (is_numeric($int_idpes)) {
            $sql .= " AND c.ref_idpes = {$int_idpes}";
            $sql2 .= " AND c.ref_idpes = {$int_idpes}";
        }
        if (is_numeric($int_cod_cliente)) {
            $sql .= " AND c.cod_cliente = {$int_cod_cliente}";
            $sql2 .= " AND c.cod_cliente = {$int_cod_cliente}";
        }
        if (is_numeric($int_cod_cliente_tipo)) {
            $sql .= " AND ct.cod_cliente_tipo = {$int_cod_cliente_tipo}";
            $sql2 .= " AND ct.cod_cliente_tipo = {$int_cod_cliente_tipo}";
        }
        if (is_numeric($int_cod_biblioteca)) {
            $sql .= " AND b.cod_biblioteca = {$int_cod_biblioteca}";
            $sql2 .= " AND b.cod_biblioteca = {$int_cod_biblioteca}";
        }
        if (is_numeric($int_cod_escola)) {
            $sql .= " AND e.cod_escola = {$int_cod_escola}";
            $sql2 .= " AND e.cod_escola = {$int_cod_escola}";
        }
        if (is_numeric($int_cod_instituicao)) {
            $sql .= " AND i.cod_instituicao = {$int_cod_instituicao}";
            $sql2 .= " AND i.cod_instituicao = {$int_cod_instituicao}";
        }
        if (is_numeric($int_valor)) {
            $sql .= ' AND ee.valor_multa IS NOT NULL';
            $sql2 .= ' AND ee.valor_multa IS NOT NULL';
        }
        $sql .= ' GROUP BY c.cod_cliente, c.ref_idpes, b.cod_biblioteca, b.nm_biblioteca, e.cod_escola, i.nm_instituicao';

        // echo $sql; die;
        $this->_total = $db->CampoUnico($sql2);
        $db->Consulta($sql);

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();

            $tupla['_total'] = $this->_total;
            $resultado[] = $tupla;
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna uma lista com os clientes da instituicao, escola, biblioteca indicados com os valores
     *
     * @return string
     */
    public function listaDividaPagamentoCliente($int_cod_cliente, $int_idpes, $int_cod_cliente_tipo, $int_cod_usuario, $int_cod_biblioteca = null, $int_cod_escola = null, $int_cod_instituicao = null, $pago = false)
    {
        $obj_nivel = new clsPermissoes();
        $nivel = $obj_nivel->nivel_acesso($int_cod_usuario);
        $db = new clsBanco();
        $tabelas = '';
        $condicoes = '';
        if (is_numeric($int_cod_cliente) || is_numeric($int_idpes)) {
            $tabelas .= ', pmieducar.cliente c';
            if (is_numeric($int_cod_cliente)) {
                $condicoes .= " AND ee.ref_cod_cliente = c.cod_cliente AND c.cod_cliente = {$int_cod_cliente}";
            }
            if (is_numeric($int_idpes) && is_numeric($int_cod_cliente)) {
                $condicoes .= " AND c.ref_idpes = {$int_idpes}";
            } elseif (is_numeric($int_idpes)) {
                $condicoes .= " AND ee.ref_cod_cliente = c.cod_cliente AND c.ref_idpes = {$int_idpes}";
            }
        }
        if (is_numeric($int_cod_cliente_tipo) && is_numeric($int_cod_cliente)) {
            $tabelas .= ', pmieducar.cliente_tipo ct, pmieducar.cliente_tipo_cliente ctc';
            $condicoes .= " AND ctc.ref_cod_cliente = c.cod_cliente AND ctc.ref_cod_cliente_tipo = ct.cod_cliente_tipo AND ct.cod_cliente_tipo = {$int_cod_cliente_tipo}";
        } elseif (is_numeric($int_cod_cliente_tipo)) {
            $tabelas .= ', pmieducar.cliente c, pmieducar.cliente_tipo ct, pmieducar.cliente_tipo_cliente ctc';
            $condicoes .= " AND ee.ref_cod_cliente = c.cod_cliente AND ctc.ref_cod_cliente = c.cod_cliente AND ctc.ref_cod_cliente_tipo = ct.cod_cliente_tipo AND ct.cod_cliente_tipo = {$int_cod_cliente_tipo}";
        }
        if (is_numeric($int_cod_biblioteca)) {
            $tabelas .= ', pmieducar.biblioteca b';
            $condicoes .= " AND a.ref_cod_biblioteca = b.cod_biblioteca AND b.cod_biblioteca = {$int_cod_biblioteca}";
        }
        if ($nivel == 2 || $nivel == 1) {
            if (is_numeric($int_cod_escola) && is_numeric($int_cod_biblioteca)) {
                $tabelas .= ', pmieducar.escola es';
                $condicoes .= " AND b.ref_cod_escola = es.cod_escola AND es.cod_escola = {$int_cod_escola}";
            } elseif (is_numeric($int_cod_escola)) {
                $tabelas .= ', pmieducar.biblioteca b, pmieducar.escola es';
                $condicoes .= " AND a.ref_cod_biblioteca = b.cod_biblioteca AND b.ref_cod_escola = es.cod_escola AND es.cod_escola = {$int_cod_escola}";
            }
        }
        if ($nivel == 1) {
            if (is_numeric($int_cod_instituicao) && (is_numeric($int_cod_biblioteca) || is_numeric($int_cod_escola))) {
                $condicoes .= " AND b.ref_cod_instituicao = {$int_cod_instituicao} ";
            } elseif (is_numeric($int_cod_instituicao)) {
                $tabelas .= ', pmieducar.biblioteca b';
                $condicoes .= " AND a.ref_cod_biblioteca = b.cod_biblioteca AND b.ref_cod_instituicao = {$int_cod_instituicao} ";
            }
        }
        if (!$pago) {
            $condicoes .= ' AND ee.valor_multa <> coalesce( ( SELECT sum( pm.valor_pago )
                                                                FROM pmieducar.pagamento_multa pm
                                                               WHERE pm.ref_cod_cliente    = ee.ref_cod_cliente
                                                                 AND pm.ref_cod_biblioteca = a.ref_cod_biblioteca ), 0 ) ';
        }
        if ($nivel == 8) {
            $tabelas .= ', pmieducar.biblioteca_usuario bu';
            $condicoes .= " AND a.ref_cod_biblioteca = bu.ref_cod_biblioteca AND bu.ref_cod_usuario = {$int_cod_usuario} ";
        }
        if ($nivel == 2) {
            if (!(is_numeric($int_cod_biblioteca) || is_numeric($int_cod_escola) || (is_numeric($int_cod_instituicao) && $nivel == 1))) {
                $tabelas .= ', pmieducar.usuario u, pmieducar.biblioteca b';
            } else {
                $tabelas .= ', pmieducar.usuario u';
            }
            $condicoes .= " AND b.ref_cod_instituicao = u.ref_cod_instituicao AND a.ref_cod_biblioteca = b.cod_biblioteca AND u.cod_usuario = {$int_cod_usuario} ";
        }

        $sql = "  SELECT ee.ref_cod_cliente,
                         a.ref_cod_biblioteca,
                         sum( ee.valor_multa ) AS valor_multa,
                         ( SELECT sum( pm.valor_pago )
                             FROM pmieducar.pagamento_multa pm
                            WHERE pm.ref_cod_cliente    = ee.ref_cod_cliente
                              AND pm.ref_cod_biblioteca = a.ref_cod_biblioteca ) AS valor_pago
                    FROM pmieducar.exemplar_emprestimo          ee,
                         pmieducar.exemplar                      e,
                         pmieducar.acervo                        a{$tabelas}
                   WHERE ee.ref_cod_exemplar  = e.cod_exemplar
                     AND e.ref_cod_acervo     = a.cod_acervo
                     AND ee.valor_multa IS NOT NULL{$condicoes}
                GROUP BY ee.ref_cod_cliente,
                         a.ref_cod_biblioteca";

        $sql2 = " SELECT count(0) FROM (
                    SELECT ee.ref_cod_cliente,
                           a.ref_cod_biblioteca,
                           sum( ee.valor_multa ) AS valor_multa,
                           ( SELECT sum( pm.valor_pago )
                               FROM pmieducar.pagamento_multa pm
                              WHERE pm.ref_cod_cliente    = ee.ref_cod_cliente
                                AND pm.ref_cod_biblioteca = a.ref_cod_biblioteca ) AS valor_pago
                      FROM pmieducar.exemplar_emprestimo          ee,
                           pmieducar.exemplar                      e,
                           pmieducar.acervo                      a{$tabelas}
                     WHERE ee.ref_cod_exemplar  = e.cod_exemplar
                       AND e.ref_cod_acervo     = a.cod_acervo
                       AND ee.valor_multa IS NOT NULL{$condicoes}
                  GROUP BY ee.ref_cod_cliente,
                           a.ref_cod_biblioteca
                  ) AS subquery";

        $this->_total = $db->CampoUnico($sql2);
        $db->Consulta($sql);

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();

            $tupla['_total'] = $this->_total;
            $resultado[] = $tupla;
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna uma lista o total de dívida por cliente
     *
     * @return string
     */
    public function listaTotalMulta($int_cod_cliente = null)
    {
        $db = new clsBanco();
        $tabela = '';
        $condicao = '';
        if (is_numeric($int_cod_cliente)) {
            $condicao .= " AND c.cod_cliente = {$int_cod_cliente}";
        }
        $sql = "SELECT   c.cod_cliente,
                         c.ref_idpes,
                         sum( ee.valor_multa ) AS valor_multa_total,
                         ( SELECT sum( pm.valor_pago )
                             FROM pmieducar.pagamento_multa pm
                            WHERE pm.ref_cod_cliente = c.cod_cliente ) AS valor_pago_total
                    FROM pmieducar.exemplar_emprestimo          ee,
                         pmieducar.cliente                       c{$tabela}
                   WHERE c.cod_cliente = ee.ref_cod_cliente
                     AND ee.valor_multa IS NOT NULL{$condicao}
                GROUP BY c.cod_cliente,
                         c.ref_idpes";
        $db->Consulta($sql);

        while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();

            $tupla['_total'] = $this->_total;
            $resultado[] = $tupla;
        }
        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    public function totalMultaPorBiblioteca($int_ref_cod_cliente = null, $int_ref_cod_biblioteca = null, $multa = false)
    {
        $sql = "SELECT SUM(ee.valor_multa) FROM {$this->_tabela} ee, {$this->_schema}exemplar e, {$this->_schema}acervo a, {$this->_schema}biblioteca b";

        $whereAnd = ' AND ';

        $filtros = ' WHERE ee.ref_cod_exemplar = e.cod_exemplar AND e.ref_cod_acervo = a.cod_acervo AND a.ref_cod_biblioteca = b.cod_biblioteca ';

        if (is_numeric($int_ref_cod_cliente)) {
            $filtros .= "{$whereAnd} ee.ref_cod_cliente = '{$int_ref_cod_cliente}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_biblioteca)) {
            $filtros .= "{$whereAnd} a.ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
            $whereAnd = ' AND ';
        }
        if (!is_null($multa)) {
            if ($multa) {
                $filtros .= "{$whereAnd} ee.valor_multa IS NOT NULL";
                $whereAnd = ' AND ';
            }
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} ee, {$this->_schema}exemplar e, {$this->_schema}acervo a, {$this->_schema}biblioteca b {$filtros}");

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
}
