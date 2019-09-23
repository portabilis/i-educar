<?php

use iEducar\Legacy\Model;

require_once 'include/public/geral.inc.php';

class clsPublicDistrito extends Model
{
    public $idmun;
    public $geom;
    public $iddis;
    public $nome;
    public $idpes_rev;
    public $data_rev;
    public $origem_gravacao;
    public $idpes_cad;
    public $data_cad;
    public $operacao;
    public $cod_ibge;

    public function __construct(
        $idmun = null,
        $geom = null,
        $iddis = null,
        $nome = null,
        $idpes_rev = null,
        $data_rev = null,
        $origem_gravacao = null,
        $idpes_cad = null,
        $data_cad = null,
        $operacao = null,
        $idsis_rev = null,
        $idsis_cad = null,
        $cod_ibge = null
    ) {
        $db = new clsBanco();
        $this->_schema = 'public.';
        $this->_tabela = $this->_schema . 'distrito ';

        $this->_campos_lista = $this->_todos_campos = 'd.idmun, d.geom, d.iddis, ' .
            'd.nome, d.idpes_rev, d.data_rev, d.origem_gravacao, d.idpes_cad, ' .
            'd.data_cad, d.operacao, d.cod_ibge ';

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

        if (is_numeric($iddis)) {
            $this->iddis = $iddis;
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

        if (is_string($cod_ibge)) {
            $this->cod_ibge = $cod_ibge;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
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

            if (is_string($this->cod_ibge)) {
                $campos .= "{$gruda}cod_ibge";
                $valores .= "{$gruda}'{$this->cod_ibge}'";
                $gruda = ', ';
            }

            $db->Consulta(sprintf(
                'INSERT INTO %s (%s) VALUES (%s)',
                $this->_tabela,
                $campos,
                $valores
            ));

            return $db->InsertId('seq_distrito');
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
        if (is_numeric($this->iddis)) {
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

            if (is_string($this->cod_ibge)) {
                $set .= "{$gruda}cod_ibge = '{$this->cod_ibge}'";
                $gruda = ', ';
            }

            if ($set) {
                $db->Consulta(sprintf(
                    'UPDATE %s SET %s WHERE iddis = \'%d\'',
                    $this->_tabela,
                    $set,
                    $this->iddis
                ));

                return true;
            }
        }

        return false;
    }

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
        $int_iddis = null,
        $cod_ibge = null
    ) {
        $select = ', m.nome AS nm_municipio, m.sigla_uf, u.nome AS nm_estado, u.idpais, p.nome AS nm_pais ';
        $from = ' d, public.municipio m, public.uf u, public.pais p ';

        $sql = sprintf(
            'SELECT %s %s FROM %s %s',
            $this->_campos_lista,
            $select,
            $this->_tabela,
            $from
        );

        $whereAnd = ' AND ';

        $filtros = ' WHERE d.idmun = m.idmun AND m.sigla_uf = u.sigla_uf AND u.idpais = p.idpais ';

        if (is_numeric($int_idmun)) {
            $filtros .= "{$whereAnd} d.idmun = '{$int_idmun}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_geom)) {
            $filtros .= "{$whereAnd} d.geom LIKE '%{$str_geom}%'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_iddis)) {
            $filtros .= "{$whereAnd} d.iddis = '{$int_iddis}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_nome)) {
            $filtros .= "{$whereAnd} translate(upper(d.nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$str_nome}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_idpes_rev)) {
            $filtros .= "{$whereAnd} d.idpes_rev = '{$int_idpes_rev}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_rev_ini)) {
            $filtros .= "{$whereAnd} d.data_rev >= '{$date_data_rev_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_rev_fim)) {
            $filtros .= "{$whereAnd} d.data_rev <= '{$date_data_rev_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_origem_gravacao)) {
            $filtros .= "{$whereAnd} d.origem_gravacao LIKE '%{$str_origem_gravacao}%'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_idpes_cad)) {
            $filtros .= "{$whereAnd} d.idpes_cad = '{$int_idpes_cad}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cad_ini)) {
            $filtros .= "{$whereAnd} d.data_cad >= '{$date_data_cad_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cad_fim)) {
            $filtros .= "{$whereAnd} d.data_cad <= '{$date_data_cad_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_operacao)) {
            $filtros .= "{$whereAnd} d.operacao LIKE '%{$str_operacao}%'";
            $whereAnd = ' AND ';
        }

        if (is_string($cod_ibge)) {
            $filtros .= "{$whereAnd} d.cod_ibge = '{$cod_ibge}'";
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
     */
    public function detalhe()
    {
        if (is_numeric($this->iddis)) {
            $db = new clsBanco();

            $sql = sprintf(
                'SELECT %s FROM %s d WHERE d.iddis = \'%d\'',
                $this->_todos_campos,
                $this->_tabela,
                $this->iddis
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
     */
    public function existe()
    {
        if (is_numeric($this->iddis)) {
            $db = new clsBanco();

            $sql = sprintf(
                'SELECT 1 FROM %s WHERE iddis = \'%d\'',
                $this->_tabela,
                $this->iddis
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
     */
    public function excluir()
    {
        if (is_numeric($this->iddis)) {
            $db = new clsBanco();

            $sql = sprintf(
                'DELETE FROM %s WHERE iddis = \'%d\'',
                $this->_tabela,
                $this->iddis
            );

            $db->Consulta($sql);

            return true;
        }

        return false;
    }
}
