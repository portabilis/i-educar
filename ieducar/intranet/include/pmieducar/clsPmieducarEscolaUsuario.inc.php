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
* @author Prefeitura Municipal de Itajaí
*
* Criado em 14/07/2006 09:28 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarEscolaUsuario
{
    var $id;
    var $ref_cod_usuario;
    var $ref_cod_escola;
    var $escola_atual;
    var $_total;
    var $_schema;
    var $_tabela;
    var $_campos_lista;
    var $_todos_campos;
    var $_limite_quantidade;
    var $_limite_offset;
    var $_campo_order_by;

    function __construct($id = 0, $ref_cod_usuario = null, $ref_cod_escola = null, $escola_atual = 0){
        $db = new clsBanco();
        $this->_schema = "pmieducar.";
        $this->_tabela = "{$this->_schema}escola_usuario";

        $this->_campos_lista = $this->_todos_campos = "id, ref_cod_usuario, ref_cod_escola, escola_atual";

        if(is_numeric($id)){
            $this->id = $id;
        }
        if(is_numeric($ref_cod_usuario)){
            $this->ref_cod_usuario = $ref_cod_usuario;
        }
        if(is_numeric($ref_cod_escola)){
            $this->ref_cod_escola = $ref_cod_escola;
        }
        if(is_numeric($escola_atual)){
            $this->escola_atual = $escola_atual;
        }
    }

    function cadastra(){
        $db = new clsBanco();
        $campos = "";
        $valores = "";
        $gruda = "";

        if(is_numeric($this->ref_cod_usuario)){
            $campos .= "{$gruda}ref_cod_usuario";
            $valores .= "{$gruda}'{$this->ref_cod_usuario}'";
            $gruda = ", ";
        }
        if(is_numeric($this->ref_cod_escola)){
            $campos .= "{$gruda}ref_cod_escola";
            $valores .= "{$gruda}'{$this->ref_cod_escola}'";
            $gruda = ", ";
        }
        if(is_numeric($this->ref_cod_escola)){
            $campos .= "{$gruda}escola_atual";
            $valores .= "{$gruda}'0'";
        }

        $db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
        return $db->InsertId("{$this->_tabela}_id_seq");
    }

    function edita(){
        if(is_numeric($this->id)) {
            $db = new clsBanco();
            $set = "";

            if(is_numeric($this->ref_cod_usuario)){
                $set .= "{$gruda}ref_cod_usuario = '{$this->ref_cod_usuario}'";
                $gruda = ", ";
            }
            if(is_numeric($this->ref_cod_escola)){
                $set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
                $gruda = ", ";
            }
            if(is_numeric($this->ref_cod_escola)){
                $set .= "{$gruda}escola_atual = '0'";
            }

            if($set){
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE id = '{$this->id}'");
                return true;
            }
        }
        return false;
    }

    function detalhe()
    {
        if( is_numeric( $this->ref_cod_usuario ) )
        {
            $db = new clsBanco();
            //echo "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_usuario = '{$this->ref_cod_usuario}'"; die();
            $db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_usuario = '{$this->ref_cod_usuario}'" );
            $db->ProximoRegistro();
            return $db->Tupla();
        }
        return false;
    }

    function lista($ref_cod_usuario = NULL, $ref_cod_escola = NULL)
    {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($ref_cod_usuario)) {
          $filtros .= "{$whereAnd} ref_cod_usuario = '{$ref_cod_usuario}'";
          $whereAnd = " AND ";
        }

        if (is_numeric($ref_cod_escola)) {
          $filtros .= "{$whereAnd} ref_cod_escola = '{$ref_cod_escola}'";
          $whereAnd = " AND ";
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = array();

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

        $db->Consulta($sql);
        if ($countCampos > 1) {
          while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();

            $tupla["_total"] = $this->_total;
            $resultado[] = $tupla;
          }
        }
        else {
          while ($db->ProximoRegistro()) {
            $tupla = $db->Tupla();
            $resultado[] = $tupla[$this->_campos_lista];
          }
        }

        if (count($resultado)) {
          return $resultado;
        }

        return FALSE;
    }

    function excluir(){
        if(is_numeric($this->id)){
            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_usuario = '{$this->ref_cod_usuario}'");
            return true;
        }
    }

    function setCamposLista( $str_campos ){
        $this->_campos_lista = $str_campos;
    }

    function resetCamposLista(){
        $this->_campos_lista = $this->_todos_campos;
    }

    function setOrderby($strNomeCampo){
        if(is_string($strNomeCampo) && $strNomeCampo){
            $this->_campo_order_by = $strNomeCampo;
        }
    }

    function getOrderby(){
        if( is_string( $this->_campo_order_by ) ){
            return " ORDER BY {$this->_campo_order_by} ";
        }
        return "";
    }

    function setLimite($intLimiteQtd, $intLimiteOffset = null){
        $this->_limite_quantidade = $intLimiteQtd;
        $this->_limite_offset = $intLimiteOffset;
    }

    function getLimite(){
        if(is_numeric($this->_limite_quantidade)){
            $retorno = " LIMIT {$this->_limite_quantidade}";
            if(is_numeric($this->_limite_offset)){
                $retorno .= " OFFSET {$this->_limite_offset} ";
            }
            return $retorno;
        }
        return "";
    }

    function excluirTodos($codUsuario){
        $db = new clsBanco();
        $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_usuario = '{$codUsuario}'");
        return true;
    }
}