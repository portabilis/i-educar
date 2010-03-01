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
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Imagem
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBanco.inc.php';
require_once 'include/imagem/clsPortalImagemTipo.inc.php';

/**
 * clsPortalImagem class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Imagem
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsPortalImagem
{
  var $cod_imagem;
  var $ref_cod_imagem_tipo;
  var $caminho;
  var $nm_imagem;
  var $extensao;
  var $altura;
  var $largura;
  var $data_cadastro;
  var $ref_cod_pessoa_cad;
  var $data_exclusao;
  var $ref_cod_pessoa_exc;
  var $imagem_antiga;

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
  function clsPortalImagem($cod_imagem = NULL, $ref_cod_imagem_tipo = NULL,
    $fileIndex = NULL, $nm_imagem = NULL, $extensao = NULL, $altura = NULL,
    $largura = NULL, $data_cadastro = NULL, $ref_cod_pessoa_cad = NULL,
    $data_exclusao = NULL, $ref_cod_pessoa_exc = NULL)
  {
    global $_FILES;

    if (!is_null($fileIndex) && !empty($_FILES[$fileIndex]['name'])) {
      $this->caminho = $_FILES[$fileIndex]['name'];

      if (file_exists($this->caminho)) {
        unlink($this->caminho);
      }

      copy($_FILES['caminho']['tmp_name'], 'imagens/banco_imagens/' . $this->caminho);
      list ($imagewidth, $imageheight, $img_type) = @GetImageSize('imagens/banco_imagens/' . $this->caminho);

      $src_img_original = '';
      $this->largura    = $imagewidth;
      $this->altura     = $imageheight;
      $this->extensao   = substr($this->caminho, -3);
    }

    $db = new clsBanco();
    $this->_schema = 'portal.';
    $this->_tabela = $this->_schema . 'imagem';

    $this->_campos_lista = $this->_todos_campos = 'cod_imagem, ref_cod_imagem_tipo, caminho, nm_imagem, extensao, altura, largura, data_cadastro, ref_cod_pessoa_cad, data_exclusao, ref_cod_pessoa_exc';

    if (is_numeric($cod_imagem)) {
      $this->cod_imagem = $cod_imagem;
      $db = new clsBanco();
      $db->Consulta("SELECT caminho FROM portal.imagem WHERE cod_imagem = {$this->cod_imagem}" );

      if($db->ProximoRegistro()) {
        list($this->imagem_antiga) = $db->Tupla();
      }
    }

    if (is_numeric($ref_cod_imagem_tipo)) {
      $tmp_obj = new clsPortalImagemTipo($ref_cod_imagem_tipo);

      if ($tmp_obj->detalhe()) {
        $this->ref_cod_imagem_tipo = $ref_cod_imagem_tipo;
      }
    }

    if (is_numeric($ref_cod_pessoa_cad)) {
      $tmp_obj = new clsFuncionario($ref_cod_pessoa_cad);

      if ($tmp_obj->detalhe()) {
        $this->ref_cod_pessoa_cad = $ref_cod_pessoa_cad;
      }
    }

    if (is_numeric($ref_cod_pessoa_exc)) {
      $tmp_obj = new clsFuncionario($ref_cod_pessoa_exc);

      if ($tmp_obj->detalhe()) {
        $this->ref_cod_pessoa_exc = $ref_cod_pessoa_exc;
      }
    }

    if (is_string($nm_imagem)) {
      $this->nm_imagem = $nm_imagem;
    }

    if (is_string($data_cadastro)) {
      $this->data_cadastro = $data_cadastro;
    }

    if (is_string($data_exclusao)) {
      $this->data_exclusao = $data_exclusao;
    }
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->ref_cod_imagem_tipo) &&
        is_numeric($this->ref_cod_pessoa_cad) && is_string($this->caminho)
    ) {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';

      if (is_numeric($this->cod_imagem)) {
        $campos .= "{$gruda}cod_imagem";
        $valores .= "{$gruda}'{$this->cod_imagem}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_imagem_tipo)) {
        $campos .= "{$gruda}ref_cod_imagem_tipo";
        $valores .= "{$gruda}'{$this->ref_cod_imagem_tipo}'";
        $gruda = ", ";
      }

      if (is_numeric($this->altura)) {
        $campos .= "{$gruda}altura";
        $valores .= "{$gruda}'{$this->altura}'";
        $gruda = ", ";
      }

      if (is_numeric($this->largura)) {
        $campos .= "{$gruda}largura";
        $valores .= "{$gruda}'{$this->largura}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_pessoa_cad)) {
        $campos .= "{$gruda}ref_cod_pessoa_cad";
        $valores .= "{$gruda}'{$this->ref_cod_pessoa_cad}'";
        $gruda = ", ";
      }

      if (is_string($this->caminho)) {
        $campos .= "{$gruda}caminho";
        $valores .= "{$gruda}'{$this->caminho}'";
        $gruda = ", ";
      }

      if (is_string($this->nm_imagem)) {
        $campos .= "{$gruda}nm_imagem";
        $valores .= "{$gruda}'{$this->nm_imagem}'";
        $gruda = ", ";
      }

      if (is_string($this->extensao)) {
        $campos .= "{$gruda}extensao";
        $valores .= "{$gruda}'{$this->extensao}'";
        $gruda = ", ";
      }

      $campos .= "{$gruda}data_cadastro";
      $valores .= "{$gruda}NOW()";
      $gruda = ", ";

      $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");
      return $db->InsertId("{$this->_tabela}_cod_imagem_seq");
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_file("imagens/banco_imagens/" . $this->imagem_antiga) && $this->caminho) {
      unlink("imagens/banco_imagens/" . $this->imagem_antiga);
    }

    if (is_numeric($this->cod_imagem)) {
      $db = new clsBanco();
      $set = "";

      if (is_numeric($this->ref_cod_imagem_tipo)) {
        $set .= "{$gruda}ref_cod_imagem_tipo = '{$this->ref_cod_imagem_tipo}'";
        $gruda = ", ";
      }

      if (is_numeric($this->altura)) {
        $set .= "{$gruda}altura = '{$this->altura}'";
        $gruda = ", ";
      }

      if (is_numeric($this->largura)) {
        $set .= "{$gruda}largura = '{$this->largura}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_pessoa_exc)) {
        $set .= "{$gruda}ref_cod_pessoa_exc = '{$this->ref_cod_pessoa_exc}'";
        $gruda = ", ";
      }

      if (is_string($this->caminho)) {
        $set .= "{$gruda}caminho = '{$this->caminho}'";
        $gruda = ", ";
      }

      if (is_string($this->nm_imagem)) {
        $set .= "{$gruda}nm_imagem = '{$this->nm_imagem}'";
        $gruda = ", ";
      }

      if (is_string($this->extensao)) {
        $set .= "{$gruda}extensao = '{$this->extensao}'";
        $gruda = ", ";
      }

      $set .= "{$gruda}data_exclusao = NOW()";
      $gruda = ", ";

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_imagem = '{$this->cod_imagem}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista( $int_cod_imagem = NULL, $int_ref_cod_imagem_tipo = NULL,
    $int_altura = NULL, $int_largura = NULL, $int_ref_cod_pessoa_cad = NULL,
    $int_ref_cod_pessoa_exc = NULL, $str_caminho = NULL, $str_nm_imagem = NULL,
    $str_extensao = NULL, $str_data_cadastro_inicio = NULL,
    $str_data_cadastro_fim = NULL, $str_data_exclusao_inicio = NULL,
    $str_data_exclusao_fim = NULL
  ) {
    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
    $filtros = "";

    $whereAnd = " WHERE ";

    if (is_numeric($int_cod_imagem)) {
      $filtros .= "{$whereAnd} cod_imagem = '{$int_cod_imagem}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_imagem_tipo)) {
      $filtros .= "{$whereAnd} ref_cod_imagem_tipo = '{$int_ref_cod_imagem_tipo}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_altura)) {
      $filtros .= "{$whereAnd} altura = '{$int_altura}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_largura)) {
      $filtros .= "{$whereAnd} largura = '{$int_largura}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_pessoa_cad)) {
      $filtros .= "{$whereAnd} ref_cod_pessoa_cad = '{$int_ref_cod_pessoa_cad}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_pessoa_exc)) {
      $filtros .= "{$whereAnd} ref_cod_pessoa_exc = '{$int_ref_cod_pessoa_exc}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_caminho)) {
      $filtros .= "{$whereAnd} caminho ILIKE '%{$str_caminho}%'";
      $whereAnd = " AND ";
    }

    if (is_string($str_nm_imagem)) {
      $filtros .= "{$whereAnd} nm_imagem ILIKE '%{$str_nm_imagem}%'";
      $whereAnd = " AND ";
    }

    if (is_string($str_extensao)) {
      $filtros .= "{$whereAnd} extensao ILIKE '%{$str_extensao}%'";
      $whereAnd = " AND ";
    }

    if (is_string($str_data_cadastro_inicio)) {
      $filtros .= "{$whereAnd} data_cadastro >= '{$str_data_cadastro_inicio}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} data_cadastro <= '{$str_data_cadastro_fim}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_data_exclusao_inicio)) {
      $filtros .= "{$whereAnd} data_exclusao >= '{$str_data_exclusao_inicio}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_data_exclusao_fim)) {
      $filtros .= "{$whereAnd} data_exclusao <= '{$str_data_exclusao_fim}'";
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

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function detalhe()
  {
    if (is_numeric($this->cod_imagem)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_imagem = '{$this->cod_imagem}'");
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
    if (is_file("imagens/banco_imagens/".$this->imagem_antiga)) {
      unlink("imagens/banco_imagens/".$this->imagem_antiga);
    }

    if (is_numeric($this->cod_imagem)) {
      $db = new clsBanco();
      $db->Consulta("DELETE FROM {$this->_tabela} WHERE cod_imagem = '{$this->cod_imagem}'");
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