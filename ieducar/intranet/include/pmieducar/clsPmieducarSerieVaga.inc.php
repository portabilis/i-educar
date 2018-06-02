<?php
/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     ?
 * @version   $Id$
 */

require_once 'include/pmieducar/geral.inc.php';

/**
 * clsPmieducarSerieVaga class.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     ?
 * @version   @@package_version@@
 */
class clsPmieducarSerieVaga
{
  var $cod_serie_vaga;
  var $ano;
  var $ref_cod_instituicao;
  var $ref_cod_escola;
  var $ref_cod_curso;
  var $ref_cod_serie;
  var $turno;
  var $vagas;
  var $codUsuario;

  /**
   * Armazena o total de resultados obtidos na última chamada ao método lista().
   * @var int
   */
  var $_total;

  /**
   * Nome do schema.
   * @var string
   */
  var $_schema;

  /**
   * Nome da tabela.
   * @var string
   */
  var $_tabela;

  /**
   * Lista separada por vírgula, com os campos que devem ser selecionados na
   * próxima chamado ao método lista().
   * @var string
   */
  var $_campos_lista;

  /**
   * Lista com todos os campos da tabela separados por vírgula, padrão para
   * seleção no método lista.
   * @var string
   */
  var $_todos_campos;

  /**
   * Valor que define a quantidade de registros a ser retornada pelo método lista().
   * @var int
   */
  var $_limite_quantidade;

  /**
   * Define o valor de offset no retorno dos registros no método lista().
   * @var int
   */
  var $_limite_offset;

  /**
   * Define o campo para ser usado como padrão de ordenação no método lista().
   * @var string
   */
  var $_campo_order_by;

  /**
   * Construtor.
   */
  function __construct($cod_serie_vaga = NULL, $ano = NULL, $ref_cod_instituicao = NULL,
                                  $ref_cod_escola = NULL, $ref_cod_curso = NULL, $ref_cod_serie = NULL, $turno = NULL, $vagas = NULL)
  {
    $db = new clsBanco();
    $this->_schema = 'pmieducar.';
    $this->_tabela = $this->_schema . 'serie_vaga';

    $this->_campos_lista = $this->_todos_campos = ' cod_serie_vaga, ano, ref_cod_instituicao, ref_cod_escola, ref_cod_curso, ref_cod_serie, turno, vagas ';

    if (is_numeric($cod_serie_vaga)){
      $this->cod_serie_vaga = $cod_serie_vaga;
    }
    if (is_numeric($ano)){
      $this->ano = $ano;
    }
    if (is_numeric($ref_cod_instituicao)){
      $this->ref_cod_instituicao = $ref_cod_instituicao;
    }
    if (is_numeric($ref_cod_escola)){
      $this->ref_cod_escola = $ref_cod_escola;
    }
    if (is_numeric($ref_cod_curso)){
      $this->ref_cod_curso = $ref_cod_curso;
    }
    if (is_numeric($ref_cod_serie)){
      $this->ref_cod_serie = $ref_cod_serie;
    }
    if (is_numeric($turno)){
      $this->turno = $turno;
    }
    if (is_numeric($vagas)){
      $this->vagas = $vagas;
    }
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if ( is_numeric($this->cod_serie_vaga) && is_numeric($this->ano) && is_numeric($this->ref_cod_instituicao) &&
          is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_curso) && is_numeric($this->ref_cod_serie) &&
            is_numeric($this->turno) && is_numeric($this->vagas) )
    {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';

      if (is_numeric($this->cod_serie_vaga)) {
        $campos  .= "{$gruda}cod_serie_vaga";
        $valores .= "{$gruda}'{$this->cod_serie_vaga}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->ano)) {
        $campos  .= "{$gruda}ano";
        $valores .= "{$gruda}'{$this->ano}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->ref_cod_instituicao)) {
        $campos  .= "{$gruda}ref_cod_instituicao";
        $valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->ref_cod_escola)) {
        $campos  .= "{$gruda}ref_cod_escola";
        $valores .= "{$gruda}'{$this->ref_cod_escola}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->ref_cod_curso)) {
        $campos  .= "{$gruda}ref_cod_curso";
        $valores .= "{$gruda}'{$this->ref_cod_curso}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->ref_cod_serie)) {
        $campos  .= "{$gruda}ref_cod_serie";
        $valores .= "{$gruda}'{$this->ref_cod_serie}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->turno)) {
        $campos  .= "{$gruda}turno";
        $valores .= "{$gruda}'{$this->turno}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->vagas)) {
        $campos  .= "{$gruda}vagas";
        $valores .= "{$gruda}'{$this->vagas}'";
        $gruda    = ', ';
      }

      $sql = "INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)";
      $db->Consulta($sql);
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->cod_serie_vaga) && is_numeric($this->vagas)) {
      $db  = new clsBanco();
      $set = '';

      if (is_string($this->vagas)) {
        $set  .= "{$gruda}vagas = '{$this->vagas}'";
        $gruda = ', ';
      }

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_serie_vaga = '{$this->cod_serie_vaga}' ");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista($ano = NULL, $int_ref_cod_escola = NULL, $int_ref_cod_curso = NULL, $int_ref_cod_serie = NULL,
                  $turno = NULL)
  {
    $sql     = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
    $filtros = '';

    $whereAnd = ' WHERE ';

    if (is_numeric($ano)) {
      $filtros .= "{$whereAnd} ano = '{$ano}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_cod_escola)) {
      $filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
      $whereAnd = ' AND ';
    }elseif ($this->codUsuario) {
      $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                         FROM pmieducar.escola_usuario
                                        WHERE escola_usuario.ref_cod_escola = serie_vaga.ref_cod_escola
                                          AND escola_usuario.ref_cod_usuario = '{$this->codUsuario}')";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_curso)) {
      $filtros .= "{$whereAnd} ref_cod_curso = '{$int_ref_cod_curso}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_cod_serie)) {
      $filtros .= "{$whereAnd} ref_cod_serie = '{$int_ref_cod_serie}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($turno)) {
      $filtros .= "{$whereAnd} turno = '{$turno}'";
      $whereAnd = ' AND ';
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado   = array();

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

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function detalhe()
  {
    if (is_numeric($this->cod_serie_vaga)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_serie_vaga = '{$this->cod_serie_vaga}' ");
      $db->ProximoRegistro();
      return $db->Tupla();
    }
    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro
   *
   * @return array
   */
  function existe()
  {
    if (is_numeric($this->cod_serie_vaga) ) {
      $db = new clsBanco();
      $db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_serie_vaga = '{$this->cod_serie_vaga}' ");
      $db->ProximoRegistro();
      return $db->Tupla();
    }
    return FALSE;
  }

  /**
   * Exclui um registro.
   * @return bool
   */
  function excluir()
  {
    if (is_numeric($this->cod_serie_vaga) ) {
      $db = new clsBanco();
      $db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_serie_vaga = '{$this->cod_serie_vaga}' ");
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Define quais campos da tabela serão selecionados no método Lista().
   */
  function setCamposLista($str_campos)
  {
    $this->_campos_lista = $str_campos;
  }

  /**
   * Define que o método Lista() deverpa retornar todos os campos da tabela.
   */
  function resetCamposLista()
  {
    $this->_campos_lista = $this->_todos_campos;
  }

  /**
   * Define limites de retorno para o método Lista().
   */
  function setLimite($intLimiteQtd, $intLimiteOffset = NULL)
  {
    $this->_limite_quantidade = $intLimiteQtd;
    $this->_limite_offset = $intLimiteOffset;
  }

  /**
   * Retorna a string com o trecho da query responsável pelo limite de
   * registros retornados/afetados.
   *
   * @return string
   */
  function getLimite()
  {
    if (is_numeric($this->_limite_quantidade)) {
      $retorno = " LIMIT {$this->_limite_quantidade}";
      if (is_numeric($this->_limite_offset)) {
        $retorno .= " OFFSET {$this->_limite_offset} ";
      }
      return $retorno;
    }
    return '';
  }

  /**
   * Define o campo para ser utilizado como ordenação no método Lista().
   */
  function setOrderby($strNomeCampo)
  {
    if (is_string($strNomeCampo) && $strNomeCampo ) {
      $this->_campo_order_by = $strNomeCampo;
    }
  }

  /**
   * Retorna a string com o trecho da query responsável pela Ordenação dos
   * registros.
   *
   * @return string
   */
  function getOrderby()
  {
    if (is_string($this->_campo_order_by)) {
      return " ORDER BY {$this->_campo_order_by} ";
    }
    return '';
  }
}