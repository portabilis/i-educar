<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*                                                                        *
*   @author Prefeitura Municipal de ItajaÃ­                              *
*   @updated 29/03/2007                                                  *
*   Pacote: i-PLB Software PÃºblico Livre e Brasileiro                   *
*                                                                        *
*   Copyright (C) 2006  PMI - Prefeitura Municipal de ItajaÃ­            *
*                       ctima@itajai.sc.gov.br                           *
*                                                                        *
*   Este  programa  Ã©  software livre, vocÃª pode redistribuÃ­-lo e/ou  *
*   modificÃ¡-lo sob os termos da LicenÃ§a PÃºblica Geral GNU, conforme  *
*   publicada pela Free  Software  Foundation,  tanto  a versÃ£o 2 da    *
*   LicenÃ§a   como  (a  seu  critÃ©rio)  qualquer  versÃ£o  mais  nova.     *
*                                                                        *
*   Este programa  Ã© distribuÃ­do na expectativa de ser Ãºtil, mas SEM  *
*   QUALQUER GARANTIA. Sem mesmo a garantia implÃ­cita de COMERCIALI-    *
*   ZAÃÃO  ou  de ADEQUAÃÃO A QUALQUER PROPÃSITO EM PARTICULAR. Con-     *
*   sulte  a  LicenÃ§a  PÃºblica  Geral  GNU para obter mais detalhes.   *
*                                                                        *
*   VocÃª  deve  ter  recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral GNU     *
*   junto  com  este  programa. Se nÃ£o, escreva para a Free Software    *
*   Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA     *
*   02111-1307, USA.                                                     *
*                                                                        *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/**
* @author Prefeitura Municipal de ItajaÃ­
*
* Criado em 14/07/2006 09:28 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarCategoriaObra
{
    var $id;
    var $descricao;
    var $observacoes;
    
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
     * Construtor (PHP 4)
     *
     * @return object
     */
    function __construct($id = "", $descricao = "", $observacoes = ""){
        $db = new clsBanco();
        $this->_schema = "pmieducar.";
        $this->_tabela = "{$this->_schema}categoria_obra";

        $this->_campos_lista = $this->_todos_campos = "id, descricao, observacoes";

        if(is_numeric($id)){
            $this->id = $id;
        }
        if(is_string($descricao)){
            $this->descricao = $descricao;
        }
        if(is_string( $observacoes)){
            $this->observacoes = $observacoes;
        }
    }


    function lista($descricao = null){
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = "";
        
        $whereAnd = " WHERE ";
        
        if(is_string($descricao)){
            $filtros .= "{$whereAnd} descricao LIKE '%{$descricao}%'";
            $whereAnd = " AND ";
        }
        
        $db = new clsBanco();
        $countCampos = count(explode(",", $this->_campos_lista));
        $resultado = array();
        
        $sql .= $filtros . $this->getOrderby() . $this->getLimite();
        
        $this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} {$filtros}" );
        
        $db->Consulta($sql);
        
        if($countCampos > 1){
            while ($db->ProximoRegistro()){
                $tupla = $db->Tupla();
            
                $tupla["_total"] = $this->_total;
                $resultado[] = $tupla;
            }
        }
        else{
            while($db->ProximoRegistro()){
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }
        if(count($resultado)){
            return $resultado;
        }
        return false;
    }

    function detalhe(){
        if(is_numeric( $this->id)){
            $db = new clsBanco();
            $db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE id = '{$this->id}'");
            $db->ProximoRegistro();
            return $db->Tupla();
        }
        return false;
    }

    function cadastra(){
        if(is_string($this->descricao)){
            $db = new clsBanco();
            $campos = "";
            $valores = "";
            $gruda = "";
            
            if(is_string($this->descricao)){
                $campos .= "{$gruda}descricao";
                $valores .= "{$gruda}'{$this->descricao}'";
                $gruda = ", ";
            }
            if(is_string($this->observacoes)){
                $campos .= "{$gruda}observacoes";
                $valores .= "{$gruda}'{$this->observacoes}'";
            }
            
            $db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
            return $db->InsertId("{$this->_tabela}_id_seq");
        }
        return false;
    }

    function edita(){
        if(is_numeric($this->id)) {
            $db = new clsBanco();
            $set = "";
            if(is_string($this->descricao)){
                $set .= "{$gruda}descricao = '{$this->descricao}'";
                $gruda = ", ";
            }
            if(is_string( $this->observacoes)){
                $set .= "{$gruda}observacoes = '{$this->observacoes}'";
                $gruda = ", ";
            }
            if($set){
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE id = '{$this->id}'");
                return true;
            }
        }
        return false;
    }

    function excluir(){
        if(is_numeric($this->id)){
            $db = new clsBanco();
            $getVinculoObra = $db->Consulta("SELECT *
                                               FROM relacao_categoria_acervo 
                                              WHERE categoria_id = {$this->id}");
            if(pg_num_rows($getVinculoObra) > 0){
                return false;
            }
            else{
                $db->Consulta("DELETE FROM {$this->_tabela} WHERE id = '{$this->id}'");
                return true;
            }
        }
    }

    function setCamposLista( $str_campos ){
        $this->_campos_lista = $str_campos;
    }

    function resetCamposLista(){
        $this->_campos_lista = $this->_todos_campos;
    }

    function setOrderby($strNomeCampo){
        // limpa a string de possiveis erros (delete, insert, etc)
        //$strNomeCampo = eregi_replace();
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
}