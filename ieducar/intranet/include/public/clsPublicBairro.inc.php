<?php

use iEducar\Legacy\Model;

require_once 'include/public/geral.inc.php';

class clsPublicBairro extends Model
{
    public $idmun;
    public $geom;
    public $idbai;
    public $nome;
    public $idsetorbai;
    public $idpes_rev;
    public $data_rev;
    public $origem_gravacao;
    public $idpes_cad;
    public $data_cad;
    public $operacao;
    public $zona_localizacao;
    public $iddis;

    /**
     * Construtor.
     *
     * @param int    $idmun
     * @param string $geom
     * @param int    $idbai
     * @param string $nome
     * @param int    $idpes_rev
     * @param string $data_rev
     * @param string $origem_gravacao
     * @param int    $idpes_cad
     * @param string $data_cad
     * @param string $operacao
     * @param null   $idsis_rev
     * @param null   $idsis_cad
     * @param int    $zona_localizacao
     * @param null   $iddis
     */
    public function __construct(
        $idmun = null,
        $geom = null,
        $idbai = null,
        $nome = null,
        $idpes_rev = null,
        $data_rev = null,
        $origem_gravacao = null,
        $idpes_cad = null,
        $data_cad = null,
        $operacao = null,
        $idsis_rev = null,
        $idsis_cad = null,
        $zona_localizacao = 1,
        $iddis = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'public.';
        $this->_tabela = $this->_schema . 'bairro';

        $this->_campos_lista = $this->_todos_campos = 'b.idmun, b.geom, b.idbai, ' .
            'b.nome, b.idpes_rev, b.data_rev, b.origem_gravacao, b.idpes_cad, ' .
            'b.data_cad, b.operacao, b.zona_localizacao, b.iddis, b.idsetorbai ';

        if (is_numeric($idpes_rev)) {
                    $this->idpes_rev = $idpes_rev;
        }

        if (is_numeric($idpes_cad)) {
                    $this->idpes_cad = $idpes_cad;
        }

        if (is_numeric($idmun)) {
                    $this->idmun = $idmun;
        }

        if (is_string($geom)) {
            $this->geom = $geom;
        }

        if (is_numeric($idbai)) {
            $this->idbai = $idbai;
        }

        if (is_string($nome)) {
            $this->nome = $nome;
        }

        if (is_string($data_rev)) {
            $this->data_rev = $data_rev;
        }

        if (is_string($origem_gravacao)) {
            $this->origem_gravacao = $origem_gravacao;
        }

        if (is_string($data_cad)) {
            $this->data_cad = $data_cad;
        }

        if (is_string($operacao)) {
            $this->operacao = $operacao;
        }

        if (is_numeric($zona_localizacao)) {
            $this->zona_localizacao = $zona_localizacao;
        }

        if (is_numeric($iddis)) {
            $this->iddis = $iddis;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function cadastra()
    {
        if (is_numeric($this->idmun) && is_string($this->nome) &&
            is_string($this->origem_gravacao) && is_string($this->operacao)
        ) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_numeric($this->idmun)) {
                $campos .= "{$gruda}idmun";
                $valores .= "{$gruda}'{$this->idmun}'";
                $gruda = ', ';
            }

            if (is_string($this->geom)) {
                $campos .= "{$gruda}geom";
                $valores .= "{$gruda}'{$this->geom}'";
                $gruda = ', ';
            }

            if (is_string($this->nome)) {
                $campos .= "{$gruda}nome";
                $valores .= "{$gruda}'" . pg_escape_string($this->nome) . '\'';
                $gruda = ', ';
            }

            if (is_numeric($this->idpes_rev)) {
                $campos .= "{$gruda}idpes_rev";
                $valores .= "{$gruda}'{$this->idpes_rev}'";
                $gruda = ', ';
            }

            if (is_string($this->data_rev)) {
                $campos .= "{$gruda}data_rev";
                $valores .= "{$gruda}'{$this->data_rev}'";
                $gruda = ', ';
            }

            if (is_string($this->origem_gravacao)) {
                $campos .= "{$gruda}origem_gravacao";
                $valores .= "{$gruda}'{$this->origem_gravacao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->idpes_cad)) {
                $campos .= "{$gruda}idpes_cad";
                $valores .= "{$gruda}'{$this->idpes_cad}'";
                $gruda = ', ';
            }

            $campos .= "{$gruda}data_cad";
            $valores .= "{$gruda}NOW()";
            $gruda = ', ';

            if (is_string($this->operacao)) {
                $campos .= "{$gruda}operacao";
                $valores .= "{$gruda}'{$this->operacao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->zona_localizacao)) {
                $campos .= "{$gruda}zona_localizacao";
                $valores .= "{$gruda}'{$this->zona_localizacao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->iddis)) {
                $campos .= "{$gruda}iddis";
                $valores .= "{$gruda}'{$this->iddis}'";
                $gruda = ', ';
            }

            if (is_numeric($this->idsetorbai)) {
                $campos .= "{$gruda}idsetorbai";
                $valores .= "{$gruda}'{$this->idsetorbai}'";
                $gruda = ', ';
            }

            $db->Consulta(sprintf(
                'INSERT INTO %s (%s) VALUES (%s)',
                $this->_tabela,
                $campos,
                $valores
            ));

            return $db->InsertId('seq_bairro');
        }

        return false;
    }

    /**
     * Edita os dados de um registro.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function edita()
    {
        if (is_numeric($this->idbai)) {
            $db = new clsBanco();
            $set = '';

            if (is_numeric($this->idmun)) {
                $set .= "{$gruda}idmun = '{$this->idmun}'";
                $gruda = ', ';
            }

            if (is_string($this->geom)) {
                $set .= "{$gruda}geom = '{$this->geom}'";
                $gruda = ', ';
            }

            if (is_string($this->nome)) {
                $set .= "{$gruda}nome = '" . pg_escape_string($this->nome) . '\'';
                $gruda = ', ';
            }

            if (is_numeric($this->idpes_rev)) {
                $set .= "{$gruda}idpes_rev = '{$this->idpes_rev}'";
                $gruda = ', ';
            }

            if (is_string($this->data_rev)) {
                $set .= "{$gruda}data_rev = '{$this->data_rev}'";
                $gruda = ', ';
            }

            if (is_string($this->origem_gravacao)) {
                $set .= "{$gruda}origem_gravacao = '{$this->origem_gravacao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->idpes_cad)) {
                $set .= "{$gruda}idpes_cad = '{$this->idpes_cad}'";
                $gruda = ', ';
            }

            if (is_string($this->data_cad)) {
                $set .= "{$gruda}data_cad = '{$this->data_cad}'";
                $gruda = ', ';
            }

            if (is_string($this->operacao)) {
                $set .= "{$gruda}operacao = '{$this->operacao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->zona_localizacao)) {
                $set .= "{$gruda}zona_localizacao = '{$this->zona_localizacao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->iddis)) {
                $set .= "{$gruda}iddis = '{$this->iddis}'";
                $gruda = ', ';
            }

            if (is_numeric($this->idsetorbai)) {
                $set .= "{$gruda}idsetorbai = '{$this->idsetorbai}'";
                $gruda = ', ';
            } else {
                $set .= "{$gruda}idsetorbai = NULL ";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta(sprintf(
                    'UPDATE %s SET %s WHERE idbai = \'%d\'',
                    $this->_tabela,
                    $set,
                    $this->idbai
                ));

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @param int    $int_idmun
     * @param string $str_geom
     * @param string $str_nome
     * @param int    $int_idpes_rev
     * @param string $date_data_rev_ini
     * @param string $date_data_rev_fim
     * @param string $str_origem_gravacao
     * @param int    $int_idpes_cad
     * @param string $date_data_cad_ini
     * @param string $date_data_cad_fim
     * @param string $str_operacao
     * @param int    $zona_localizacao
     *
     * @return array
     *
     * @throws Exception
     */
    public function lista(
        $int_idmun = null,
        $str_geom = null,
        $str_nome = null,
        $int_idpes_rev = null,
        $date_data_rev_ini = null,
        $date_data_rev_fim = null,
        $str_origem_gravacao = null,
        $int_idpes_cad = null,
        $date_data_cad_ini = null,
        $date_data_cad_fim = null,
        $str_operacao = null,
        $int_idsis_rev = null,
        $int_idsis_cad = null,
        $int_idpais = null,
        $str_sigla_uf = null,
        $int_idbai = null,
        $zona_localizacao = null,
        $int_iddis = null
    ) {
        $select = ', m.nome AS nm_municipio, m.sigla_uf, u.nome AS nm_estado, u.idpais, p.nome AS nm_pais, d.nome AS nm_distrito ';
        $from = 'b, public.municipio m, public.uf u, public.pais p, public.distrito d ';

        $sql = sprintf(
            'SELECT %s %s FROM %s %s',
            $this->_campos_lista,
            $select,
            $this->_tabela,
            $from
        );

        $whereAnd = ' AND ';

        $filtros = ' WHERE b.idmun = m.idmun AND m.sigla_uf = u.sigla_uf AND u.idpais = p.idpais AND b.iddis = d.iddis ';

        if (is_numeric($int_idmun)) {
            $filtros .= "{$whereAnd} b.idmun = '{$int_idmun}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_geom)) {
            $filtros .= "{$whereAnd} b.geom LIKE '%{$str_geom}%'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_idbai)) {
            $filtros .= "{$whereAnd} b.idbai = '{$int_idbai}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_nome)) {
            $filtros .= "{$whereAnd} translate(upper(b.nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$str_nome}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_idpes_rev)) {
            $filtros .= "{$whereAnd} b.idpes_rev = '{$int_idpes_rev}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_rev_ini)) {
            $filtros .= "{$whereAnd} b.data_rev >= '{$date_data_rev_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_rev_fim)) {
            $filtros .= "{$whereAnd} b.data_rev <= '{$date_data_rev_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_origem_gravacao)) {
            $filtros .= "{$whereAnd} b.origem_gravacao LIKE '%{$str_origem_gravacao}%'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_idpes_cad)) {
            $filtros .= "{$whereAnd} b.idpes_cad = '{$int_idpes_cad}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cad_ini)) {
            $filtros .= "{$whereAnd} b.data_cad >= '{$date_data_cad_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cad_fim)) {
            $filtros .= "{$whereAnd} b.data_cad <= '{$date_data_cad_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_operacao)) {
            $filtros .= "{$whereAnd} b.operacao LIKE '%{$str_operacao}%'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($zona_localizacao)) {
            $filtros .= "{$whereAnd} b.zona_localizacao = '{$zona_localizacao}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_idpais)) {
            $filtros .= "{$whereAnd} p.idpais = '{$int_idpais}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_sigla_uf)) {
            $filtros .= "{$whereAnd} u.sigla_uf = '{$str_sigla_uf}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_iddis)) {
            $filtros .= "{$whereAnd} d.iddis = '{$int_iddis}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();

        $countCampos = count(explode(', ', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico(sprintf(
            'SELECT COUNT(0) FROM %s %s %s',
            $this->_tabela,
            $from,
            $filtros
        ));

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
     *
     * @throws Exception
     */
    public function detalhe()
    {
        if (is_numeric($this->idbai)) {
            $db = new clsBanco();

            $sql = sprintf(
                'SELECT %s FROM %s b WHERE b.idbai = \'%d\'',
                $this->_todos_campos,
                $this->_tabela,
                $this->idbai
            );

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
     *
     * @throws Exception
     */
    public function existe()
    {
        if (is_numeric($this->idbai)) {
            $db = new clsBanco();

            $sql = sprintf(
                'SELECT 1 FROM %s WHERE idbai = \'%d\'',
                $this->_tabela,
                $this->idbai
            );

            $db->Consulta($sql);

            if ($db->ProximoRegistro()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Exclui um registro
     *
     * @return bool
     *
     * @throws Exception
     */
    public function excluir()
    {
        if (is_numeric($this->idbai)) {
            $db = new clsBanco();

            $sql = sprintf(
                'DELETE FROM %s WHERE idbai = \'%d\'',
                $this->_tabela,
                $this->idbai
            );

            $db->Consulta($sql);

            return true;
        }

        return false;
    }
}
