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
/**
* @author Lucas Schmoeller da Silva
*
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarBloqueioAnoLetivo
{
    var $ref_cod_instituicao;
    var $ref_ano;
    var $data_inicio;
    var $data_fim;

    // propriedades padrao

    /**
     * Armazena o total de resultados obtidos na ultima chamada ao metodo lista
     *
     * @var int
     */
    var $_total;

    /**
     * Nome do schema
     *
     * @var string
     */
    var $_schema;

    /**
     * Nome da tabela
     *
     * @var string
     */
    var $_tabela;

    /**
     * Lista separada por virgula, com os campos que devem ser selecionados na proxima chamado ao metodo lista
     *
     * @var string
     */
    var $_campos_lista;

    /**
     * Lista com todos os campos da tabela separados por virgula, padrao para selecao no metodo lista
     *
     * @var string
     */
    var $_todos_campos;

    /**
     * Valor que define a quantidade de registros a ser retornada pelo metodo lista
     *
     * @var int
     */
    var $_limite_quantidade;

    /**
     * Define o valor de offset no retorno dos registros no metodo lista
     *
     * @var int
     */
    var $_limite_offset;

    /**
     * Define o campo padrao para ser usado como padrao de ordenacao no metodo lista
     *
     * @var string
     */
    var $_campo_order_by;


    /**
     * Constructor
     */
    function __construct( $ref_cod_instituicao = null, $ref_ano = null, $data_inicio = null, $data_fim = null)
    {
        $db = new clsBanco();
        $this->_schema = "pmieducar.";
        $this->_tabela = "{$this->_schema}bloqueio_ano_letivo";

        $this->_campos_lista = $this->_todos_campos = "ref_cod_instituicao, ref_ano, data_inicio, data_fim ";

        if( is_numeric( $ref_cod_instituicao ) )
        {
            $this->ref_cod_instituicao = $ref_cod_instituicao;
        }
        if( is_numeric( $ref_ano ) )
        {
            $this->ref_ano = $ref_ano;
        }
        if( is_string( $data_inicio ) )
        {
            $this->data_inicio = $data_inicio;
        }
        if( is_string( $data_fim ) )
        {
            $this->data_fim = $data_fim;
        }
    }

    /**
     * Cria um novo registro
     *
     * @return bool
     */
    function cadastra()
    {
        if( is_numeric( $this->ref_cod_instituicao ) && is_numeric( $this->ref_ano ) && is_string( $this->data_inicio ) && is_string( $this->data_fim ) )
        {
            $db = new clsBanco();

            $campos = "";
            $valores = "";
            $gruda = "";

            if( is_numeric( $this->ref_cod_instituicao ) )
            {
                $campos .= "{$gruda}ref_cod_instituicao";
                $valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
                $gruda = ", ";
            }
            if( is_numeric( $this->ref_ano ) )
            {
                $campos .= "{$gruda}ref_ano";
                $valores .= "{$gruda}'{$this->ref_ano}'";
                $gruda = ", ";
            }
            if( is_string( $this->data_inicio ) )
            {
                $campos .= "{$gruda}data_inicio";
                $valores .= "{$gruda}'{$this->data_inicio}'";
                $gruda = ", ";
            }
            if( is_string( $this->data_fim ) )
            {
                $campos .= "{$gruda}data_fim";
                $valores .= "{$gruda}'{$this->data_fim}'";
                $gruda = ", ";
            }

            $db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
            return true;
        }
        return false;
    }

    /**
     * Edita os dados de um registro
     *
     * @return bool
     */
    function edita()
    {
        if( is_numeric( $this->ref_cod_instituicao ) && is_numeric( $this->ref_ano ) && is_string( $this->data_inicio ) && is_string( $this->data_fim )  )
        {

            $db = new clsBanco();
            $set = "";

            if( is_string( $this->data_inicio ) )
            {
                $set .= "{$gruda}data_inicio = '{$this->data_inicio}'";
                $gruda = ", ";
            }
            if( is_string( $this->data_fim ) )
            {
                $set .= "{$gruda}data_fim = '{$this->data_fim}'";
                $gruda = ", ";
            }

            if( $set )
            {
                $db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_instituicao = '{$this->ref_cod_instituicao}' AND ref_ano = '{$this->ref_ano}'" );
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
    function lista( $ref_cod_instituicao = null, $ref_ano = null )
    {
        $sql = "SELECT {$this->_campos_lista}, instituicao.nm_instituicao as instituicao FROM {$this->_tabela} INNER JOIN pmieducar.instituicao ON (ref_cod_instituicao = cod_instituicao) ";

        $filtros = "";

        $whereAnd = " WHERE ";

        if( is_numeric( $ref_cod_instituicao ) )
        {
            $filtros .= "{$whereAnd} ref_cod_instituicao = '{$ref_cod_instituicao}'";
            $whereAnd = " AND ";
        }
        if( is_numeric( $ref_ano ) )
        {
            $filtros .= "{$whereAnd} ref_ano = '{$ref_ano}'";
            $whereAnd = " AND ";
        }

        $db = new clsBanco();
        $countCampos = count( explode( ",", $this->_campos_lista ) );
        $resultado = array();

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} {$filtros}" );

        $db->Consulta( $sql );

        if( $countCampos > 1 )
        {
            while ( $db->ProximoRegistro() )
            {
                $tupla = $db->Tupla();

                $tupla["_total"] = $this->_total;
                $resultado[] = $tupla;
            }
        }
        else
        {
            while ( $db->ProximoRegistro() )
            {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }
        if( count( $resultado ) )
        {
            return $resultado;
        }
        return false;
    }

    /**
     * Retorna um array com os dados de um registro
     *
     * @return array
     */
    function detalhe()
    {
        if(is_numeric( $this->ref_cod_instituicao ) && is_numeric( $this->ref_ano ) )
        {

        $db = new clsBanco();
        $db->Consulta( "SELECT {$this->_campos_lista}, instituicao.nm_instituicao as instituicao FROM {$this->_tabela} INNER JOIN pmieducar.instituicao ON (ref_cod_instituicao = cod_instituicao)  WHERE ref_cod_instituicao = '{$this->ref_cod_instituicao}' AND ref_ano = '{$this->ref_ano}'" );
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
    function existe()
    {
        if(is_numeric( $this->ref_cod_instituicao ) && is_numeric( $this->ref_ano ) )
        {

        $db = new clsBanco();
        $db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_instituicao = '{$this->ref_cod_instituicao}' AND ref_ano = '{$this->ref_ano}'" );
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
    function excluir()
    {
        if(is_numeric( $this->ref_cod_instituicao ) && is_numeric( $this->ref_ano ) )
        {
            $db = new clsBanco();
            $db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_instituicao = '{$this->ref_cod_instituicao}' AND ref_ano = '{$this->ref_ano}'" );
            return true;
        }
        return false;
    }

    /**
     * Define quais campos da tabela serao selecionados na invocacao do metodo lista
     *
     * @return null
     */
    function setCamposLista( $str_campos )
    {
        $this->_campos_lista = $str_campos;
    }

    /**
     * Define que o metodo Lista devera retornoar todos os campos da tabela
     *
     * @return null
     */
    function resetCamposLista()
    {
        $this->_campos_lista = $this->_todos_campos;
    }

    /**
     * Define limites de retorno para o metodo lista
     *
     * @return null
     */
    function setLimite( $intLimiteQtd, $intLimiteOffset = null )
    {
        $this->_limite_quantidade = $intLimiteQtd;
        $this->_limite_offset = $intLimiteOffset;
    }

    /**
     * Retorna a string com o trecho da query resposavel pelo Limite de registros
     *
     * @return string
     */
    function getLimite()
    {
        if( is_numeric( $this->_limite_quantidade ) )
        {
            $retorno = " LIMIT {$this->_limite_quantidade}";
            if( is_numeric( $this->_limite_offset ) )
            {
                $retorno .= " OFFSET {$this->_limite_offset} ";
            }
            return $retorno;
        }
        return "";
    }

    /**
     * Define campo para ser utilizado como ordenacao no metolo lista
     *
     * @return null
     */
    function setOrderby( $strNomeCampo )
    {
        // limpa a string de possiveis erros (delete, insert, etc)
        //$strNomeCampo = eregi_replace();

        if( is_string( $strNomeCampo ) && $strNomeCampo )
        {
            $this->_campo_order_by = $strNomeCampo;
        }
    }

    /**
     * Retorna a string com o trecho da query resposavel pela Ordenacao dos registros
     *
     * @return string
     */
    function getOrderby()
    {
        if( is_string( $this->_campo_order_by ) )
        {
            return " ORDER BY {$this->_campo_order_by} ";
        }
        return "";
    }
}
?>
