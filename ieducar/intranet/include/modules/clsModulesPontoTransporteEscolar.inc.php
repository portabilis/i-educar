<?php

use iEducar\Legacy\Model;
use Illuminate\Support\Facades\Session;

require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsModulesPontoTransporteEscolar extends Model
{
    public $cod_ponto_transporte_escolar;
    public $descricao;
    public $cep;
    public $idbai;
    public $idlog;
    public $complemento;
    public $numero;
    public $latitude;
    public $longitude;
    public $pessoa_logada;

    public function __construct($cod_ponto_transporte_escolar = null, $descricao = null)
    {
        $db = new clsBanco();
        $this->_schema = 'modules.';
        $this->_tabela = "{$this->_schema}ponto_transporte_escolar";

        $this->pessoa_logada = Session::get('id_pessoa');

        $this->_campos_lista = $this->_todos_campos = ' cod_ponto_transporte_escolar, descricao, cep, idlog, idbai, complemento, numero, latitude, longitude ';

        if (is_numeric($cod_ponto_transporte_escolar)) {
            $this->cod_ponto_transporte_escolar = $cod_ponto_transporte_escolar;
        }

        if (is_string($descricao)) {
            $this->descricao = $descricao;
        }
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     */
    public function cadastra()
    {
        if (is_string($this->descricao)) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            if (is_string($this->descricao)) {
                $campos .= "{$gruda}descricao";
                $valores .= "{$gruda}'{$this->descricao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->cep)) {
                $campos .= "{$gruda}cep";
                $valores .= "{$gruda} {$this->cep}";
                $gruda = ', ';
            }

            if (is_numeric($this->idlog)) {
                $campos .= "{$gruda}idlog";
                $valores .= "{$gruda} {$this->idlog}";
                $gruda = ', ';
            }

            if (is_numeric($this->idbai)) {
                $campos .= "{$gruda}idbai";
                $valores .= "{$gruda} {$this->idbai}";
                $gruda = ', ';
            }

            if (is_numeric($this->numero)) {
                $campos .= "{$gruda}numero";
                $valores .= "{$gruda}'{$this->numero}'";
                $gruda = ', ';
            }

            if (is_string($this->complemento)) {
                $campos .= "{$gruda}complemento";
                $valores .= "{$gruda}'{$this->complemento}'";
                $gruda = ', ';
            }

            if (is_numeric($this->latitude)) {
                $campos .= "{$gruda}latitude";
                $valores .= "{$gruda}'{$this->latitude}'";
                $gruda = ', ';
            }

            if (is_numeric($this->longitude)) {
                $campos .= "{$gruda}longitude";
                $valores .= "{$gruda}'{$this->longitude}'";
                $gruda = ', ';
            }

            $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");

            $this->cod_ponto_transporte_escolar = $db->InsertId("{$this->_tabela}_seq");

            if ($this->cod_ponto_transporte_escolar) {
                $detalhe = $this->detalhe();
                $auditoria = new clsModulesAuditoriaGeral('ponto_transporte_escolar', $this->pessoa_logada, $this->cod_ponto_transporte_escolar);
                $auditoria->inclusao($detalhe);
            }

            return $this->cod_ponto_transporte_escolar;
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
        if (is_string($this->cod_ponto_transporte_escolar)) {
            $db = new clsBanco();
            $set = '';
            $gruda = '';

            if (is_string($this->descricao)) {
                $set .= "{$gruda}descricao = '{$this->descricao}'";
                $gruda = ', ';
            }

            if (is_numeric($this->cep)) {
                $set .= "{$gruda}cep = '{$this->cep}'";
                $gruda = ', ';
            }

            if (is_numeric($this->idlog)) {
                $set .= "{$gruda}idlog = '{$this->idlog}'";
                $gruda = ', ';
            }

            if (is_numeric($this->idbai)) {
                $set .= "{$gruda}idbai = '{$this->idbai}'";
                $gruda = ', ';
            }

            if (is_string($this->complemento)) {
                $set .= "{$gruda}complemento = '{$this->complemento}'";
                $gruda = ', ';
            }

            if (is_numeric($this->numero)) {
                $set .= "{$gruda}numero = '{$this->numero}'";
                $gruda = ', ';
            }

            if (is_numeric($this->latitude)) {
                $set .= "{$gruda}latitude = '{$this->latitude}'";
                $gruda = ', ';
            }

            if (is_numeric($this->longitude)) {
                $set .= "{$gruda}longitude = '{$this->longitude}'";
                $gruda = ', ';
            }

            if ($set) {
                $detalheAntigo = $this->detalhe();
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_ponto_transporte_escolar = '{$this->cod_ponto_transporte_escolar}'");
                $auditoria = new clsModulesAuditoriaGeral('ponto_transporte_escolar', $this->pessoa_logada, $this->cod_ponto_transporte_escolar);
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
    public function lista($cod_ponto_transporte_escolar = null, $descricao = null)
    {
        $sql = "SELECT {$this->_campos_lista},

              (SELECT l.nome FROM public.logradouro l WHERE l.idlog = ponto_transporte_escolar.idlog) as logradouro,

              (SELECT l.idtlog FROM public.logradouro l WHERE l.idlog = ponto_transporte_escolar.idlog) as idtlog,

              (SELECT b.nome FROM public.bairro b WHERE b.idbai = ponto_transporte_escolar.idbai) as bairro,

              (SELECT b.zona_localizacao FROM public.bairro b WHERE b.idbai = ponto_transporte_escolar.idbai) as zona_localizacao,

              (SELECT m.nome FROM public.municipio m, public.logradouro l WHERE m.idmun = l.idmun AND l.idlog = ponto_transporte_escolar.idlog) as municipio,

              (SELECT m.sigla_uf FROM public.municipio m, public.logradouro l WHERE m.idmun = l.idmun AND l.idlog = ponto_transporte_escolar.idlog) as sigla_uf,

              (SELECT l.idmun FROM public.logradouro l WHERE l.idlog = ponto_transporte_escolar.idlog) as idmun,

              (SELECT bairro.iddis FROM public.bairro
                WHERE idbai = ponto_transporte_escolar.idbai) as iddis,

              (SELECT distrito.nome FROM public.distrito
                INNER JOIN public.bairro ON (bairro.iddis = distrito.iddis)
                WHERE idbai = ponto_transporte_escolar.idbai) as distrito

            FROM {$this->_tabela}
    ";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($cod_ponto_transporte_escolar)) {
            $filtros .= "{$whereAnd} cod_ponto_transporte_escolar = '{$cod_ponto_transporte_escolar}'";
            $whereAnd = ' AND ';
        }

        if (is_string($descricao)) {
            $filtros .= "{$whereAnd} translate(upper(descricao),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$descricao}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
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
        if (is_numeric($this->cod_ponto_transporte_escolar)) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_campos_lista},

              (SELECT l.nome FROM public.logradouro l WHERE l.idlog = ponto_transporte_escolar.idlog) as logradouro,

              (SELECT l.idtlog FROM public.logradouro l WHERE l.idlog = ponto_transporte_escolar.idlog) as idtlog,

              (SELECT b.nome FROM public.bairro b WHERE b.idbai = ponto_transporte_escolar.idbai) as bairro,

              (SELECT b.zona_localizacao FROM public.bairro b WHERE b.idbai = ponto_transporte_escolar.idbai) as zona_localizacao,

              (SELECT m.nome FROM public.municipio m, public.logradouro l WHERE m.idmun = l.idmun AND l.idlog = ponto_transporte_escolar.idlog) as municipio,

              (SELECT m.sigla_uf FROM public.municipio m, public.logradouro l WHERE m.idmun = l.idmun AND l.idlog = ponto_transporte_escolar.idlog) as sigla_uf,

              (SELECT l.idmun FROM public.logradouro l WHERE l.idlog = ponto_transporte_escolar.idlog) as idmun,

              (SELECT bairro.iddis FROM public.bairro
                WHERE idbai = ponto_transporte_escolar.idbai) as iddis,

              (SELECT distrito.nome FROM public.distrito
                INNER JOIN public.bairro ON (bairro.iddis = distrito.iddis)
                WHERE idbai = ponto_transporte_escolar.idbai) as distrito

            FROM {$this->_tabela} WHERE cod_ponto_transporte_escolar = '{$this->cod_ponto_transporte_escolar}'");
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
        if (is_numeric($this->cod_ponto_transporte_escolar)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_ponto_transporte_escolar = '{$this->cod_ponto_transporte_escolar}'");
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
        if (is_numeric($this->cod_ponto_transporte_escolar)) {
            $detalhe = $this->detalhe();

            $sql = "DELETE FROM {$this->_tabela} WHERE cod_ponto_transporte_escolar = '{$this->cod_ponto_transporte_escolar}'";
            $db = new clsBanco();
            $db->Consulta($sql);

            $auditoria = new clsModulesAuditoriaGeral('ponto_transporte_escolar', $this->pessoa_logada, $this->cod_ponto_transporte_escolar);
            $auditoria->exclusao($detalhe);

            return true;
        }

        return false;
    }
}
