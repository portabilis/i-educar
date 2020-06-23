<?php

use iEducar\Legacy\Model;
use Illuminate\Support\Facades\Session;

require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsModulesRotaTransporteEscolar extends Model
{
    public $cod_rota_transporte_escolar;
    public $ref_idpes_destino;
    public $descricao;
    public $ano;
    public $tipo_rota;
    public $km_pav;
    public $km_npav;
    public $ref_cod_empresa_transporte_escolar;
    public $tercerizado;
    public $pessoa_logada;

    public function __construct($cod_rota_transporte_escolar = null, $ref_idpes_destino = null, $descricao = null, $ano = null, $tipo_rota = null, $km_pav = null, $km_npav = null, $ref_cod_empresa_transporte_escolar = null, $tercerizado = null)
    {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}rota_transporte_escolar";

        $this->pessoa_logada = Session::get('id_pessoa');

        $this->_campos_lista = $this->_todos_campos = ' cod_rota_transporte_escolar, ref_idpes_destino, descricao, ano, tipo_rota, km_pav, km_npav, ref_cod_empresa_transporte_escolar, tercerizado';

        if (is_numeric($cod_rota_transporte_escolar)) {
            $this->cod_rota_transporte_escolar = $cod_rota_transporte_escolar;
        }

        if (is_numeric($ref_idpes_destino)) {
            $this->ref_idpes_destino = $ref_idpes_destino;
        }

        if (is_string($descricao)) {
            $this->descricao = $descricao;
        }

        if (is_numeric($ano)) {
            $this->ano = $ano;
        }

        if (is_string($tipo_rota)) {
            $this->tipo_rota = $tipo_rota;
        }

        if (is_numeric($km_pav)) {
            $this->km_pav = $km_pav;
        }

        if (is_numeric($km_npav)) {
            $this->km_npav = $km_npav;
        }

        if (is_numeric($ref_cod_empresa_transporte_escolar)) {
            $this->ref_cod_empresa_transporte_escolar = $ref_cod_empresa_transporte_escolar;
        }

        if (is_string($tercerizado)) {
            $this->tercerizado = $tercerizado;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_idpes_destino) && is_numeric($this->ano) && is_string($this->descricao)
            && is_string($this->tipo_rota) && is_numeric($this->ref_cod_empresa_transporte_escolar)
            && is_string($this->tercerizado)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->ref_idpes_destino)) {
                $campos .= "{$gruda}ref_idpes_destino";
                $valores .= "{$gruda}'{$this->ref_idpes_destino}'";
                $gruda = ', ';
            }

            if (is_string($this->descricao)) {
                $descricao = $db->escapeString($this->descricao);
                $campos .= "{$gruda}descricao";
                $valores .= "{$gruda}'{$descricao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ano)) {
                $campos .= "{$gruda}ano";
                $valores .= "{$gruda}'{$this->ano}'";
                $gruda = ', ';
            }

            if (is_string($this->tipo_rota)) {
                $campos .= "{$gruda}tipo_rota";
                $valores .= "{$gruda}'{$this->tipo_rota}'";
                $gruda = ', ';
            }

            if (is_numeric($this->km_pav)) {
                $campos .= "{$gruda}km_pav";
                $valores .= "{$gruda}'{$this->km_pav}'";
                $gruda = ', ';
            }

            if (is_numeric($this->km_npav)) {
                $campos .= "{$gruda}km_npav";
                $valores .= "{$gruda}'{$this->km_npav}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_empresa_transporte_escolar)) {
                $campos .= "{$gruda}ref_cod_empresa_transporte_escolar";
                $valores .= "{$gruda}'{$this->ref_cod_empresa_transporte_escolar}'";
                $gruda = ', ';
            }

            if (is_string($this->tercerizado)) {
                $campos .= "{$gruda}tercerizado";
                $valores .= "{$gruda}'{$this->tercerizado}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            $this->cod_rota_transporte_escolar = $db->InsertId("{$this->_tabela}_seq");

            if ($this->cod_rota_transporte_escolar) {
                $detalhe = $this->detalhe();
                $auditoria = new clsModulesAuditoriaGeral('rota_transporte_escolar', $this->pessoa_logada, $this->cod_rota_transporte_escolar);
                $auditoria->inclusao($detalhe);
            }

            return $this->cod_rota_transporte_escolar;
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
        if (is_string($this->cod_rota_transporte_escolar)) {
            $db = new clsBanco();
            $set = '';
            $gruda = '';

            if (is_numeric($this->ref_idpes_destino)) {
                $set .= "{$gruda}ref_idpes_destino = '{$this->ref_idpes_destino}'";
                $gruda = ', ';
            }

            if (is_string($this->descricao)) {
                $descricao = $db->escapeString($this->descricao);
                $set .= "{$gruda}descricao = '{$descricao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ano)) {
                $set .= "{$gruda}ano = '{$this->ano}'";
                $gruda = ', ';
            }

            if (is_string($this->tipo_rota)) {
                $set .= "{$gruda}tipo_rota = '{$this->tipo_rota}'";
                $gruda = ', ';
            }

            if (is_numeric($this->km_pav)) {
                $set .= "{$gruda}km_pav = '{$this->km_pav}'";
                $gruda = ', ';
            }

            if (is_numeric($this->km_npav)) {
                $set .= "{$gruda}km_npav = '{$this->km_npav}'";
                $gruda = ', ';
            }

            if (is_numeric($this->ref_cod_empresa_transporte_escolar)) {
                $set .= "{$gruda}ref_cod_empresa_transporte_escolar = '{$this->ref_cod_empresa_transporte_escolar}'";
                $gruda = ', ';
            }

            if (is_string($this->tercerizado)) {
                $set .= "{$gruda}tercerizado = '{$this->tercerizado}'";
                $gruda = ', ';
            }

            if ($set) {
                $detalheAntigo = $this->detalhe();
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_rota_transporte_escolar = '{$this->cod_rota_transporte_escolar}'");
                $auditoria = new clsModulesAuditoriaGeral('rota_transporte_escolar', $this->pessoa_logada, $this->cod_rota_transporte_escolar);
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
        $cod_rota_transporte_escolar = null,
        $descricao = null,
        $ref_idpes_destino = null,
        $nome_destino = null,
        $ano = null,
        $ref_cod_empresa_transporte_escolar = null,
        $nome_empresa = null,
        $tercerizado = null
    ) {
        $db = new clsBanco();

        $sql = "SELECT {$this->_campos_lista}, (
          SELECT
            nome
          FROM
            cadastro.pessoa
          WHERE
            idpes = ref_idpes_destino
         ) AS nome_destino , (
          SELECT
            nome
          FROM
            cadastro.pessoa, modules.empresa_transporte_escolar
          WHERE
            idpes = ref_idpes and cod_empresa_transporte_escolar = ref_cod_empresa_transporte_escolar
         ) AS nome_empresa FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($cod_rota_transporte_escolar)) {
            $filtros .= "{$whereAnd} cod_rota_transporte_escolar = '{$cod_rota_transporte_escolar}'";
            $whereAnd = ' AND ';
        }

        if (is_string($descricao)) {
            $desc= $db->escapeString($descricao);
            $filtros .= "{$whereAnd} translate(upper(descricao),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$desc}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
            $whereAnd = ' AND ';
        }

        if (is_numeric($ref_idpes_destino)) {
            $filtros .= "{$whereAnd} ref_idpes_destino = '{$ref_idpes_destino}'";
            $whereAnd = ' AND ';
        }
        if (is_string($nome_destino)) {
            $filtros .= "
        {$whereAnd} exists (
          SELECT
            1
          FROM
            cadastro.pessoa
          WHERE
            cadastro.pessoa.idpes = ref_idpes_destino
            AND translate(upper(nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$nome_destino}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')
        )";

            $whereAnd = ' AND ';
        }

        if (is_numeric($ano)) {
            $filtros .= "{$whereAnd} ano = '{$ano}'";
            $whereAnd = ' AND ';
        }
        if (is_string($ref_cod_empresa_transporte_escolar)) {
            $filtros .= "{$whereAnd} ref_cod_empresa_transporte_escolar = '{$ref_cod_empresa_transporte_escolar}'";
            $whereAnd = ' AND ';
        }

        if (is_string($nome_empresa)) {
            $filtros .= "
        {$whereAnd} exists (
          SELECT
            nome
          FROM
            cadastro.pessoa, modules.empresa_transporte_escolar
          WHERE
            idpes = ref_idpes and cod_empresa_transporte_escolar = ref_cod_empresa_transporte_escolar
            AND (LOWER(nome)) LIKE (LOWER('%{$nome_empresa}%'))
        )";

            $whereAnd = ' AND ';
        }

        if (is_string($tercerizado)) {
            $filtros .= "{$whereAnd} tercerizado = '{$tercerizado}'";
            $whereAnd = ' AND ';
        }

        $countCampos = count(explode(',', $this->_campos_lista)) + 2;
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
        if (is_numeric($this->cod_rota_transporte_escolar)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos}, (
              SELECT
                nome
              FROM
                cadastro.pessoa
              WHERE
                idpes = ref_idpes_destino
             ) AS nome_destino , (
              SELECT
                nome
              FROM
                cadastro.pessoa, modules.empresa_transporte_escolar
              WHERE
                idpes = ref_idpes and cod_empresa_transporte_escolar = ref_cod_empresa_transporte_escolar
             ) AS nome_empresa FROM {$this->_tabela} WHERE cod_rota_transporte_escolar = '{$this->cod_rota_transporte_escolar}'");
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
        if (is_numeric($this->cod_rota_transporte_escolar)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_rota_transporte_escolar = '{$this->cod_rota_transporte_escolar}'");
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
        if (is_numeric($this->cod_rota_transporte_escolar)) {
            $detalhe = $this->detalhe();

            $sql = "DELETE FROM {$this->_tabela} WHERE cod_rota_transporte_escolar = '{$this->cod_rota_transporte_escolar}'";
            $db = new clsBanco();
            $db->Consulta($sql);

            $auditoria = new clsModulesAuditoriaGeral('rota_transporte_escolar', $this->pessoa_logada, $this->cod_rota_transporte_escolar);
            $auditoria->exclusao($detalhe);

            return true;
        }

        return false;
    }
}
