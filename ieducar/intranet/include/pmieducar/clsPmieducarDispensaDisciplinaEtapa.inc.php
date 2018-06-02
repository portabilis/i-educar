<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*                                      *
* @author Prefeitura Municipal de Itajaí                 *
* @updated 29/03/2007                          *
*   Pacote: i-PLB Software Público Livre e Brasileiro          *
*                                    *
* Copyright (C) 2006  PMI - Prefeitura Municipal de Itajaí       *
*           ctima@itajai.sc.gov.br                 *
*                                    *
* Este  programa  é  software livre, você pode redistribuí-lo e/ou   *
* modificá-lo sob os termos da Licença Pública Geral GNU, conforme   *
* publicada pela Free  Software  Foundation,  tanto  a versão 2 da   *
* Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.  *
*                                    *
* Este programa  é distribuído na expectativa de ser útil, mas SEM   *
* QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-   *
* ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-   *
* sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.   *
*                                    *
* Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU   *
* junto  com  este  programa. Se não, escreva para a Free Software   *
* Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA   *
* 02111-1307, USA.                           *
*                                    *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/**
* @author Prefeitura Municipal de Itajaí
*
* Criado em 02/08/2006 14:41 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarDispensaDisciplinaEtapa
{
  var $ref_cod_dispensa;
  var $etapa;

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
   * Construtor (PHP 4)
   *
   * @return object
   */
  function __construct($ref_cod_dispensa = null,
                                               $etapa = null)
  {
    $db = new clsBanco();
    $this->_schema = "pmieducar.";
    $this->_tabela = "{$this->_schema}dispensa_etapa";

    $this->_campos_lista = $this->_todos_campos = "ref_cod_dispensa,
                                                   etapa";

    if(is_numeric($ref_cod_dispensa))
    {
      $this->ref_cod_dispensa = $ref_cod_dispensa;
    }

    if(is_numeric($etapa))
    {
      $this->etapa = $etapa;
    }

  }

  /**
   * Cria um novo registro
   *
   * @return bool
   */
  function cadastra()
  {
    if( is_numeric($this->ref_cod_dispensa) && is_numeric($this->etapa))
    {
      $db = new clsBanco();

      $campos = "";
      $valores = "";
      $gruda = "";

      if(is_numeric($this->ref_cod_dispensa))
      {
        $campos .= "{$gruda}ref_cod_dispensa";
        $valores .= "{$gruda}'{$this->ref_cod_dispensa}'";
        $gruda = ", ";
      }
      if( is_numeric( $this->etapa ) )
      {
        $campos .= "{$gruda}etapa";
        $valores .= "{$gruda}'{$this->etapa}'";
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
    if( is_numeric($this->ref_cod_dispensa) && is_numeric($this->etapa))
    {

      $db = new clsBanco();
      $set = "";

      if( is_numeric( $this->etapa ) )
      {
        $set .= "{$gruda}etapa = '{$this->etapa}'";
        $gruda = ", ";
      }


      if( $set )
      {
        $db->Consulta( "UPDATE {$this->_tabela}
                           SET $set
                         WHERE ref_cod_dispensa = '{$this->ref_cod_dispensa}'");
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
  function lista($ref_cod_dispensa = null,
                 $etapa = null)
  {

    $sql = "SELECT {$this->_campos_lista}
              FROM {$this->_tabela}";

    $filtros = "";

    $whereAnd = " WHERE ";

    if( is_numeric( $ref_cod_dispensa ) )
    {
      $filtros .= "{$whereAnd} ref_cod_dispensa = '{$ref_cod_dispensa}'";
      $whereAnd = " AND ";
    }
    if( is_numeric( $etapa ) )
    {
      $filtros .= "{$whereAnd} etapa = '{$etapa}'";
      $whereAnd = " AND ";
    }

    $db = new clsBanco();
    $countCampos = count( explode( ",", $this->_campos_lista ) );
    $resultado = array();

    $sql .= $filtros;
    // echo $sql; die;


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
  function existe()
  {
    if( is_numeric($this->ref_cod_dispensa) && is_numeric($this->etapa))
    {
      $db = new clsBanco();
      $db->Consulta( "SELECT 1
                        FROM {$this->_tabela}
                       WHERE ref_cod_dispensa = '{$this->ref_cod_dispensa}'
                         AND etapa = '{$this->etapa}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }
    return false;
  }

  /**
   * Exclui todos os registros referentes a uma dispensa
   */
  function  excluirTodos($ref_cod_dispensa = null)
  {
    if ( is_numeric( $ref_cod_dispensa ) ) {
      $db = new clsBanco();
      $db->Consulta( "DELETE FROM {$this->_tabela}
                            WHERE ref_cod_dispensa = '{$ref_cod_dispensa}'" );
      return true;
    }
    return false;
  }
}
?>