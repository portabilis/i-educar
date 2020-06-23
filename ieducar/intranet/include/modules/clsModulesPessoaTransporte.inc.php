<?php

use iEducar\Legacy\Model;
use Illuminate\Support\Facades\Session;

require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsModulesPessoaTransporte extends Model
{
    public $cod_pessoa_transporte;
    public $ref_idpes;
    public $ref_cod_rota_transporte_escolar;
    public $ref_cod_ponto_transporte_escolar;
    public $ref_idpes_destino;
    public $observacao;
    public $turno;
    public $pessoa_logada;

    /**
     * Construtor.
     */
    public function __construct(
        $cod_pessoa_transporte = null,
        $ref_cod_rota_transporte_escolar = null,
        $ref_idpes = null,
        $ref_cod_ponto_transporte_escolar = null,
        $ref_idpes_destino = null,
        $observacao = null,
        $turno = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}pessoa_transporte";

        $this->pessoa_logada = Session::get('id_pessoa');

        $this->_campos_lista = $this->_todos_campos = 'cod_pessoa_transporte, ref_cod_rota_transporte_escolar,
                                                  ref_idpes, ref_cod_ponto_transporte_escolar, ref_idpes_destino, observacao, turno';

        if (is_numeric($cod_pessoa_transporte)) {
            $this->cod_pessoa_transporte = $cod_pessoa_transporte;
        }

        if (is_numeric($ref_cod_rota_transporte_escolar)) {
            $this->ref_idpes = $ref_idpes;
        }

        if (is_numeric($ref_idpes)) {
            $this->ref_idpes = $ref_idpes;
        }

        if (is_numeric($ref_cod_ponto_transporte_escolar)) {
            $this->ref_cod_ponto_transporte_escolar = $ref_cod_ponto_transporte_escolar;
        }

        if (is_numeric($ref_idpes_destino)) {
            $this->ref_idpes_destino = $ref_idpes_destino;
        }

        if (is_string($observacao)) {
            $this->observacao = $observacao;
        }

        if (is_numeric($turno)) {
            $this->turno = $turno;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_idpes) && is_numeric($this->ref_cod_rota_transporte_escolar)) {
            $db = new clsBanco();
            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->cod_pessoa_transporte)) {
                $campos .= "{$gruda}cod_pessoa_transporte";
                $valores .= "{$gruda}'{$this->cod_pessoa_transporte}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_rota_transporte_escolar)) {
                $campos .= "{$gruda}ref_cod_rota_transporte_escolar";
                $valores .= "{$gruda}'{$this->ref_cod_rota_transporte_escolar}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_idpes)) {
                $campos .= "{$gruda}ref_idpes";
                $valores .= "{$gruda}'{$this->ref_idpes}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_ponto_transporte_escolar)) {
                $campos .= "{$gruda}ref_cod_ponto_transporte_escolar";
                $valores .= "{$gruda}'{$this->ref_cod_ponto_transporte_escolar}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_idpes_destino)) {
                $campos .= "{$gruda}ref_idpes_destino";
                $valores .= "{$gruda}'{$this->ref_idpes_destino}'";
                $gruda = ', ';
            }

            if (is_string($this->observacao)) {
                $observacao = $db->escapeString($this->observacao);
                $campos .= "{$gruda}observacao";
                $valores .= "{$gruda}'{$observacao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->turno)) {
                $campos .= "{$gruda}turno";
                $valores .= "{$gruda}'{$this->turno}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            $this->cod_pessoa_transporte = $db->InsertId("{$this->_tabela}_seq");

            if ($this->cod_pessoa_transporte) {
                $detalhe = $this->detalhe();
                $auditoria = new clsModulesAuditoriaGeral('pessoa_transporte', $this->pessoa_logada, $this->cod_pessoa_transporte);
                $auditoria->inclusao($detalhe);
            }

            return $this->cod_pessoa_transporte;
        }

        return false;
    }

    /**
     * Edita os dados de um registro.
     *
     * @return bool
     */
    public function edita()
    {
        if (is_numeric($this->cod_pessoa_transporte)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->ref_cod_rota_transporte_escolar)) {
                $set .= "{$gruda}ref_cod_rota_transporte_escolar = '{$this->ref_cod_rota_transporte_escolar}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_idpes)) {
                $set .= "{$gruda}ref_idpes = '{$this->ref_idpes}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_ponto_transporte_escolar)) {
                $set .= "{$gruda}ref_cod_ponto_transporte_escolar = '{$this->ref_cod_ponto_transporte_escolar}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}ref_cod_ponto_transporte_escolar = null";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_idpes_destino)) {
                $set .= "{$gruda}ref_idpes_destino = '{$this->ref_idpes_destino}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}ref_idpes_destino = null";
                $gruda = ', ';
            }

            if (is_string($this->observacao)) {
                $observacao = $db->escapeString($this->observacao);
                $set .= "{$gruda}observacao = '{$observacao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->turno)) {
                $set .= "{$gruda}turno = '{$this->turno}'";
                $gruda = ', ';
            }

            if ($set) {
                $detalheAntigo = $this->detalhe();
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_pessoa_transporte = '{$this->cod_pessoa_transporte}'");
                $auditoria = new clsModulesAuditoriaGeral('pessoa_transporte', $this->pessoa_logada, $this->cod_pessoa_transporte);
                $auditoria->alteracao($detalheAntigo, $this->detalhe());

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
        $cod_pessoa_transporte = null,
        $ref_idpes = null,
        $ref_cod_rota_transporte_escolar = null,
        $ref_cod_ponto_transporte_escolar = null,
        $ref_idpes_destino = null,
        $nome_pessoa = null,
        $nome_destino = null,
        $ano_rota = null
    ) {
        $db = new clsBanco();

        $sql = 'SELECT pt.cod_pessoa_transporte,
                   pt.ref_cod_rota_transporte_escolar,
                   pt.ref_idpes,
                   pt.ref_cod_ponto_transporte_escolar,
                   pt.ref_idpes_destino,
                   pt.observacao,
                   pt.turno,
                   pd.nome AS nome_destino,
                   p.nome AS nome_pessoa,
                   rte.descricao AS nome_rota,
                   pte.descricao AS nome_ponto,
                   pd2.nome AS nome_destino2';

        $sqlConditions = "
      FROM {$this->_tabela} pt
      LEFT JOIN cadastro.pessoa pd
        ON (pd.idpes = pt.ref_idpes_destino)
      LEFT JOIN cadastro.pessoa p
        ON (p.idpes = pt.ref_idpes)
      LEFT JOIN modules.rota_transporte_escolar rte
        ON (rte.cod_rota_transporte_escolar = pt.ref_cod_rota_transporte_escolar)
      LEFT JOIN modules.ponto_transporte_escolar pte
        ON (pte.cod_ponto_transporte_escolar = pt.ref_cod_ponto_transporte_escolar)
      LEFT JOIN cadastro.pessoa pd2
        ON (
          pd2.idpes = rte.ref_idpes_destino AND
          pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar
        )
    ";

        $sql .= $sqlConditions;

        $filtros = '';

        $whereAnd = ' WHERE ';
        $whereNomes = '';
        if (is_numeric($cod_pessoa_transporte)) {
            $filtros .= "{$whereAnd} cod_pessoa_transporte = '{$cod_pessoa_transporte}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_idpes)) {
            $filtros .= "{$whereAnd} ref_idpes = '{$ref_idpes}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_rota_transporte_escolar)) {
            $filtros .= "{$whereAnd} ref_cod_rota_transporte_escolar = '{$ref_cod_rota_transporte_escolar}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_cod_ponto_transporte_escolar)) {
            $filtros .= "{$whereAnd} ref_cod_ponto_transporte_escolar = '{$ref_cod_ponto_transporte_escolar}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_idpes_destino)) {
            $filtros .= "{$whereAnd} ref_idpes_destino = '{$ref_idpes_destino}'";
            $whereAnd = ' AND ';
        }

        if (is_string($nome_pessoa)) {
            $nm_pessoa = $db->escapeString($nome_pessoa);
            $filtros .= "{$whereAnd} unaccent(p.nome) ILIKE unaccent('%{$nm_pessoa}%')";
            $whereAnd = ' AND ';
        }

        if (is_string($nome_destino)) {
            $nm_destino = $db->escapeString($nome_destino);
            $filtros .= "{$whereAnd} unaccent(pd.nome) ILIKE unaccent('%{$nm_destino}%') OR unaccent(pd2.nome) ILIKE unaccent('%{$nm_destino}%')";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ano_rota)) {
            $filtros .= "{$whereAnd} rte.ano = '{$ano_rota}'";
            $whereAnd = ' AND ';
        }

        $countCampos = count(explode(',', $this->_campos_lista)) + 2;
        $resultado = [];

        $sql .= $filtros . $whereNomes . $this->getOrderby() . $this->getLimite();

        $sqlCount = "
      SELECT COUNT(0) {$sqlConditions} {$filtros} {$whereNomes}
    ";

        $this->_total = $db->CampoUnico($sqlCount);

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
        if (is_numeric($this->cod_pessoa_transporte) || is_numeric($this->ref_idpes)) {
            $db = new clsBanco();
            $sql = "SELECT {$this->_todos_campos}, (
              SELECT
                nome
              FROM
                cadastro.pessoa
              WHERE
                idpes = ref_idpes_destino
             ) AS nome_destino, (
              SELECT
                nome
              FROM
                cadastro.pessoa
              WHERE
                idpes = ref_idpes
             ) AS nome_pessoa, (
              SELECT
                descricao
              FROM
                modules.rota_transporte_escolar
              WHERE
                ref_cod_rota_transporte_escolar = cod_rota_transporte_escolar
             ) AS nome_rota, (
              SELECT
                descricao
              FROM
                modules.ponto_transporte_escolar
              WHERE
                ref_cod_ponto_transporte_escolar = cod_ponto_transporte_escolar
             ) AS nome_ponto, (
              SELECT
                nome
              FROM
                cadastro.pessoa p, modules.rota_transporte_escolar rt
              WHERE
                p.idpes = rt.ref_idpes_destino and ref_cod_rota_transporte_escolar = rt.cod_rota_transporte_escolar
             ) AS nome_destino2 FROM {$this->_tabela} WHERE ";

            if (is_numeric($this->cod_pessoa_transporte)) {
                $sql .= " cod_pessoa_transporte = '{$this->cod_pessoa_transporte}'";
            } else {
                $sql .= " ref_idpes = '{$this->ref_idpes}' ORDER BY cod_pessoa_transporte DESC LIMIT 1 ";
            }

            $db->Consulta($sql);
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     */
    public function existe()
    {
        if (is_numeric($this->cod_pessoa_transporte)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_pessoa_transporte = '{$this->cod_pessoa_transporte}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * Exclui um registro.
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->cod_pessoa_transporte)) {
            $detalhe = $this->detalhe();

            $sql = "DELETE FROM {$this->_tabela} WHERE cod_pessoa_transporte = '{$this->cod_pessoa_transporte}'";
            $db = new clsBanco();
            $db->Consulta($sql);

            $auditoria = new clsModulesAuditoriaGeral('pessoa_transporte', $this->pessoa_logada, $this->cod_pessoa_transporte);
            $auditoria->exclusao($detalhe);

            return true;
        }

        return false;
    }
}
