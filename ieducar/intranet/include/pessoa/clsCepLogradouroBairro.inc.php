<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*                                                                        *
*   @author Prefeitura Municipal de Itajaí                               *
*   @updated 29/03/2007                                                  *
*   Pacote: i-PLB Software Público Livre e Brasileiro                    *
*                                                                        *
*   Copyright (C) 2006  PMI - Prefeitura Municipal de Itajaí             *
*                       ctima@itajai.sc.gov.br                           *
*                                                                        *
*   Este  programa  é  software livre, você pode redistribuí-lo e/ou     *
*   modificá-lo sob os termos da Licença Pública Geral GNU, conforme     *
*   publicada pela Free  Software  Foundation,  tanto  a versão 2 da     *
*   Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.    *
*                                                                        *
*   Este programa  é distribuído na expectativa de ser útil, mas SEM     *
*   QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-     *
*   ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-     *
*   sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.     *
*                                                                        *
*   Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU     *
*   junto  com  este  programa. Se não, escreva para a Free Software     *
*   Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA     *
*   02111-1307, USA.                                                     *
*                                                                        *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBanco.inc.php");
require_once ("include/Geral.inc.php");

class clsCepLogradouroBairro
{
    var $idlog;
    var $cep;
    var $idbai;

    var $tabela;
    var $schema;

    /**
     * Construtor
     *
     * @return Object:clsCepLogradouroBairro
     */
    function __construct( $idlog=false, $cep=false, $idbai=false)
    {
        $objLogradouro = new clsLogradouro($idlog);
        if ($objLogradouro->detalhe())
        {
            $this->idlog = $idlog;
        }

        $objCepLogradouro = new clsCepLogradouro($cep,$idlog);
        if ($objCepLogradouro->detalhe())
        {
            $this->cep   = $cep;
        }

        $objBairro = new clsBairro($idbai);
        if ($objBairro->detalhe())
        {
            $this->idbai = $idbai;
        }
        
        $this->tabela = "cep_logradouro_bairro";
        $this->schema = "urbano";
    }
    
    /**
     * Funcao que cadastra um novo registro com os valores atuais
     *
     * @return bool
     */
    function cadastra()
    {
        $db = new clsBanco();
        // verificacoes de campos obrigatorios para insercao
        if( is_numeric($this->idlog) &&  is_numeric($this->cep)   && is_numeric($this->idbai))
        {
            $db->Consulta( "INSERT INTO {$this->schema}.{$this->tabela} (cep,idlog,idbai, origem_gravacao, data_cad, operacao, idsis_cad ) VALUES ('{$this->cep}', '{$this->idlog}', '{$this->idbai}', 'U', NOW(), 'I', '9' )" );

        }
        return false;
    }
    
    /**
     * Edita o registro atual
     *
     * @return bool
     */
    function edita()
    {
        // verifica campos obrigatorios para edicao
        if(is_numeric($this->cep)   &&  is_numeric($this->idlog) && is_numeric($this->idbai))
        {
            /*$db = new clsBanco();
            $db->Consulta( "UPDATE {$this->schema}.{$this->tabela} $set WHERE cep = '$this->cep' " );*/
            return true;
        }
        return false;
    }
    
    /**
     * Remove o registro atual
     *
     * @return bool
     */
    function exclui( )
    {
        if( is_numeric($this->cep)   &&   is_numeric($this->idlog) && is_numeric($this->idbai))
        {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE cep = {$this->cep} AND idlog = {$this->idlog} AND idbai = {$this->idbai}");
            return true;
        }
        return false;
    }
    
    /**
     * Exibe uma lista baseada nos parametros de filtragem passados
     *
     * @return Array
     */
    function lista( $int_idlog=false, $int_cep=false, $int_idbai=false, $str_ordenacao="idlog", $int_limite_ini=0, $int_limite_qtd=20 )
    {
        // verificacoes de filtros a serem usados
        $whereAnd = "WHERE ";
        if(is_numeric($int_idlog))
        {
            $where .= "{$whereAnd}idlog = '$int_idlog'";
            $whereAnd = " AND ";
        }
        if(is_numeric($int_cep))
        {
            $where .= "{$whereAnd}cep::varchar LIKE '%$int_cep%'";
            $whereAnd = " AND ";
        }
        if(is_numeric($int_idbai))
        {
            $where .= "{$whereAnd}idbai =  '$int_idbai'";
        }
        
        $orderBy = "";
        if(is_string($str_ordenacao))
        {
            $orderBy = "ORDER BY $str_ordenacao";
        }
        $limit = "";
        if(is_numeric($int_limite_ini) &&  is_numeric($int_limite_qtd))
        {
            $limit = " LIMIT $int_limite_ini,$int_limite_qtd";
        }
        
        $db = new clsBanco();
        $db->Consulta( "SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where" );
        $db->ProximoRegistro();
        $total = $db->Campo( "total" );
        $db->Consulta( "SELECT idlog, cep, idbai FROM {$this->schema}.{$this->tabela} $where $orderBy $limit" );
        $resultado = array();
        while ( $db->ProximoRegistro() ) 
        {
            $tupla = $db->Tupla();
            $idlog = $tupla["idlog"];
            $tupla["idlog"] = new clsCepLogradouro( $tupla["cep"],$tupla["idlog"]);
            $tupla["cep"] = new clsCepLogradouro( $tupla["cep"],$idlog);
            $tupla["idbai"] = new clsBairro( $tupla["idbai"]);
            $tupla["total"] = $total;
            $resultado[] = $tupla;
        }
        if( count( $resultado ) )
        {
            return $resultado;
        }
        return false;
    } 
    
    /**
     * Retorna um array com os detalhes do objeto
     *
     * @return Array
     */
    function detalhe()
    {
        if($this->cep && $this->idbai && $this->idlog)
        {
            $db = new clsBanco();
            $db->Consulta("SELECT idlog, cep, idbai FROM {$this->schema}.{$this->tabela} WHERE idlog = {$this->idlog} AND cep = {$this->cep} AND idbai = {$this->idbai}");
            if( $db->ProximoRegistro() )
            {
                $tupla = $db->Tupla();
    
                $idlog = $tupla["idlog"];
                $tupla["idlog"] = new clsCepLogradouro( $tupla["cep"],$tupla["idlog"]);
                $tupla["cep"] = new clsCepLogradouro( $tupla["cep"],$idlog);
                $tupla["idbai"] = new clsBairro( $tupla["idbai"]);
    
                return $tupla;
            }
        }
        return false;
    }

    /**
    * Retorna um array com os dados de um registro.
    * @return array
    */
    function existe()
    {
        if (is_numeric($this->cep) && is_numeric($this->idlog) && is_numeric($this->idbai)) {
            $db = new clsBanco();
            $db->Consulta("SELECT 1 FROM {$this->schema}.{$this->tabela} WHERE cep = '{$this->cep}' AND idlog = '{$this->idlog}' AND idbai = '{$this->idbai}' ");
            $db->ProximoRegistro();
            return $db->Tupla();
        }

        return FALSE;
    }   
}
?>