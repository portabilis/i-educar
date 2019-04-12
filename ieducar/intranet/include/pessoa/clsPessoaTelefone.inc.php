<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*                                                                        *
*   @author Prefeitura Municipal de Itajaï¿½                                 *
*   @updated 29/03/2007                                                  *
*   Pacote: i-PLB Software Pï¿½blico Livre e Brasileiro                  *
*                                                                        *
*   Copyright (C) 2006  PMI - Prefeitura Municipal de Itajaï¿½           *
*                       ctima@itajai.sc.gov.br                           *
*                                                                        *
*   Este  programa  ï¿½  software livre, vocï¿½ pode redistribuï¿½-lo e/ou   *
*   modificï¿½-lo sob os termos da Licenï¿½a Pï¿½blica Geral GNU, conforme   *
*   publicada pela Free  Software  Foundation,  tanto  a versï¿½o 2 da   *
*   Licenï¿½a   como  (a  seu  critï¿½rio)  qualquer  versï¿½o  mais  nova.  *
*                                                                        *
*   Este programa  ï¿½ distribuï¿½do na expectativa de ser ï¿½til, mas SEM   *
*   QUALQUER GARANTIA. Sem mesmo a garantia implï¿½cita de COMERCIALI-   *
*   ZAï¿½ï¿½O  ou  de ADEQUAï¿½ï¿½O A QUALQUER PROPï¿½SITO EM PARTICULAR. Con-   *
*   sulte  a  Licenï¿½a  Pï¿½blica  Geral  GNU para obter mais detalhes.     *
*                                                                        *
*   Vocï¿½  deve  ter  recebido uma cï¿½pia da Licenï¿½a Pï¿½blica Geral GNU     *
*   junto  com  este  programa. Se nï¿½o, escreva para a Free Software   *
*   Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA     *
*   02111-1307, USA.                                                     *
*                                                                        *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

use Illuminate\Support\Facades\Session;

require_once ("include/clsBanco.inc.php");

class clsPessoaTelefone
{
    var $idpes;
    var $ddd;
    var $fone;
    var $tipo;
    var $idpes_cad;
    var $idpes_rev;
    
    
    var $banco = 'gestao_homolog';
    var $schema_cadastro = "cadastro";
    var $tabela_telefone = "fone_pessoa";
    
    
    function __construct($int_idpes = false, $int_tipo = false, $str_fone=false, $str_ddd=false, $idpes_cad = false, $idpes_rev = false)
    {
        $this->idpes = $int_idpes;
        $this->ddd   = $str_ddd;
        $this->fone  = $str_fone;
        $this->tipo  = $int_tipo;
        $this->idpes_cad = $idpes_cad ? $idpes_cad : Session::get('id_pessoa');
        $this->idpes_rev = $idpes_rev ? $idpes_rev : Session::get('id_pessoa');
        
    }
    
    function cadastra()
    {
        // Cadastro do telefone da pessoa na tabela fone_pessoa
        if($this->idpes && $this->tipo && $this->idpes_cad )
        {
            $db = new clsBanco();
            $db->Consulta( "SELECT 1 FROM {$this->schema_cadastro}.{$this->tabela_telefone} WHERE idpes = '$this->idpes' AND tipo = '$this->tipo'" );
            // Verifica se ja existe um telefone desse tipo cadastrado para essa pessoa
            if( ! $db->Num_Linhas() )
            {
                // nao tem, cadastra 1 novo
                if( $this->ddd && $this->fone )
                {
                    $db->Consulta("INSERT INTO {$this->schema_cadastro}.{$this->tabela_telefone} (idpes, tipo, ddd, fone,origem_gravacao, idsis_cad, data_cad, operacao, idpes_cad) VALUES ('$this->idpes', '$this->tipo', '$this->ddd', '$this->fone','M', 17, NOW(), 'I', '$this->idpes_cad')");
                    return true;
                }
            }
            else 
            {
                // jah tem, edita
                $this->edita();
                return true;
            }
        }
        
        return false;
    }   
    
    
    function edita()
    {
        // Cadastro do telefone da pessoa na tabela fone_pessoa
        if($this->idpes && $this->tipo && $this->idpes_rev)
        {

            $set = false;
            $gruda = "";
            if($this->ddd)
            {
                $set = "ddd = $this->ddd";
                $gruda = ", ";
            }
            if($this->fone)
            {
                $set .= "$gruda fone = $this->fone";
                $gruda = ", ";
            }elseif ($this->fone == '') {
                $set .= "$gruda fone = NULL";
                $gruda = ", ";
            }
            if($this->idpes_rev)
            {
                $set .= "$gruda idpes_rev = '$this->idpes_rev'";
                $gruda = ", ";
            }
            if($set && $this->ddd != "" && $this->fone != "" )
            {
                $db = new clsBanco();
                $db->Consulta("UPDATE {$this->schema_cadastro}.{$this->tabela_telefone} SET $set WHERE idpes = $this->idpes AND tipo = $this->tipo");
                return true;
            }
            else 
            {
                if( $this->ddd == "" && $this->fone == "" )
                {
                    $this->exclui();
                }
            }
        }
        return false;
    }
    
    
    function exclui()
    {
        if($this->idpes)
        {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM $this->schema_cadastro.$this->tabela_telefone WHERE idpes = $this->idpes AND tipo = $this->tipo");
            return true;
        }
        return false;
    }
    
    function excluiTodos()
    {
        // exclui todos os telefones da pessoa, nao importa o tipo
        if($this->idpes)
        {
            $db = new clsBanco();
            $db->Consulta("DELETE FROM $this->schema_cadastro.$this->tabela_telefone WHERE idpes = $this->idpes");
            return true;
        }
        return false;
    }

    function lista($int_idpes = false, $str_ordenacao = false, $int_inicio_limite = false, $int_qtd_registros = false, $int_ddd = false, $int_fone = false )
    {
        $whereAnd = "WHERE ";
        $where = "";
        if(  is_numeric($int_idpes))
        {
            $where .= "{$whereAnd}idpes = '$int_idpes'";
            $whereAnd = " AND ";
        }
        elseif (is_string($int_idpes))  
        {
            $where .= "{$whereAnd}idpes IN ($int_idpes)";
            $whereAnd = " AND ";
        }
            
        if(isset($str_tipo_pessoa) && is_string($str_tipo_pessoa))
        {
            $where .= "{$whereAnd}tipo = '$str_tipo_pessoa' ";
            $whereAnd = " AND ";
        }
        
        if(is_numeric($int_ddd))
        {
            $where .= "{$whereAnd}ddd = '$int_ddd' ";
            $whereAnd = " AND ";
        }
        
        if(is_numeric($int_fone))
        {
            $where .= "{$whereAnd}fone = '$int_fone' ";
            $whereAnd = " AND ";
        }

        $limite = '';
        if( $int_inicio_limite !== false && $int_qtd_registros)
        {
            $limite = "LIMIT $int_qtd_registros OFFSET $int_inicio_limite ";
        }

        $db = new clsBanco();
        $db->Consulta( "SELECT COUNT(0) AS total FROM $this->schema_cadastro.$this->tabela_telefone $where" );
        $db->ProximoRegistro();
        $total = $db->Campo( "total" );
        
        $db = new clsBanco($this->banco);
        $db = new clsBanco();
        
        $db->Consulta("SELECT idpes, tipo, ddd, fone FROM $this->schema_cadastro.$this->tabela_telefone $where $limite");
        $resultado = array();
        while ($db->ProximoRegistro()) 
        {
            $tupla = $db->Tupla();
            $tupla["total"] = $total;
            $resultado[] = $tupla;
                
        }       
        if(count($resultado) > 0)
        {
            return $resultado;
        }
        return false;
    }
    
    function detalhe()
    {
        if($this->idpes && $this->tipo)
        {
            $db = new clsBanco();
            $db->Consulta("SELECT tipo, ddd, fone FROM cadastro.fone_pessoa WHERE idpes = $this->idpes AND tipo = '$this->tipo' " );
            if($db->ProximoRegistro())
            {
                $tupla = $db->Tupla();
                return $tupla;
            }
        }
        
        elseif($this->idpes && !$this->tipo)
        {
            $db = new clsBanco();
            $db->Consulta("SELECT tipo, ddd, fone FROM cadastro.fone_pessoa WHERE idpes = $this->idpes " );
            if($db->ProximoRegistro())
            {
                $tupla = $db->Tupla();
                return $tupla;
            }
        }
        return false;
    }
    
}
?>
