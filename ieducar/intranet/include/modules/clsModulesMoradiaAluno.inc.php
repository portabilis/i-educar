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
 * @package   Module
 * @since     07/2013
 * @version   $Id$
 */

require_once 'include/pmieducar/geral.inc.php';

/**
 * clsModulesMoradiaAluno class.
 * 
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     07/2013
 * @version   @@package_version@@
 */
class clsModulesMoradiaAluno
{
  var $ref_cod_aluno;
  var $moradia;
  var $material;
  var $casa_outra;
  var $moradia_situacao;
  var $quartos;
  var $sala;
  var $copa;
  var $banheiro;
  var $garagem;
  var $empregada_domestica;
  var $automovel;
  var $motocicleta;
  var $computador;
  var $geladeira;
  var $fogao;
  var $maquina_lavar;
  var $microondas;
  var $video_dvd;
  var $televisao;
  var $celular;
  var $telefone;
  var $quant_pessoas;
  var $renda;
  var $agua_encanada;
  var $poco;
  var $energia;
  var $esgoto;
  var $fossa;
  var $lixo;

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
  function clsModulesMoradiaAluno($ref_cod_aluno = NULL,
     $moradia = NULL, $material = NULL,
     $casa_outra = NULL, $moradia_situacao = NULL,
     $quartos = NULL, $sala = NULL,
     $copa = NULL, $banheiro = NULL,
     $garagem = NULL, $empregada_domestica = NULL,
     $automovel = NULL, $motocicleta = NULL,
     $computador = NULL, $geladeira = NULL,
     $fogao = NULL, $maquina_lavar = NULL, $microondas = NULL, $video_dvd = NULL,
     $televisao = NULL, $celular = NULL, $telefone = NULL, $quant_pessoas = NULL, $renda = NULL, $agua_encanada = NULL, $poco = NULL,
     $energia = NULL, $esgoto = NULL, $fossa = NULL, $lixo = NULL) {

    $db = new clsBanco();
    $this->_schema = "modules.";
    $this->_tabela = "{$this->_schema}moradia_aluno";

    $this->_campos_lista = $this->_todos_campos = "ref_cod_aluno,
        moradia, material, casa_outra, moradia_situacao,
        quartos, sala, copa, banheiro, garagem, empregada_domestica,
      automovel, motocicleta, computador, geladeira, fogao, maquina_lavar, microondas, video_dvd,televisao, celular, telefone, quant_pessoas, renda, agua_encanada, poco, energia, esgoto, fossa, lixo"; 

    if (is_numeric($ref_cod_aluno)) {
      $this->ref_cod_aluno = $ref_cod_aluno;
    }

    if (is_string($moradia)) {
      $this->moradia = $moradia;
    }

    if (is_string($material)) {
      $this->material = $material;
    }

    if (is_string($casa_outra)) {
      $this->casa_outra = $casa_outra;
    }

    if (is_numeric($moradia_situacao)) {
      $this->moradia_situacao = $moradia_situacao;
    }

    if (is_numeric($quartos)) {
      $this->quartos = $quartos;
    }

   if (is_numeric($sala)) {
      $this->sala = $sala;
    }

    if (is_numeric($copa)) {
      $this->copa = $copa;
    }

    if (is_numeric($banheiro)) {
      $this->banheiro = $banheiro;
    }


    if (is_numeric($garagem)) {
      $this->garagem = $garagem;
    }

    if (is_string($empregada_domestica)) {
      $this->empregada_domestica = $empregada_domestica;
    }

    if (is_string($motocicleta)) {
      $this->motocicleta = $motocicleta;
    }

    if (is_string($computador)) {
      $this->computador = $computador;
    }

    if (is_string($geladeira)) {
      $this->geladeira = $geladeira;
    }

    if (is_string($fogao)) {
      $this->fogao = $fogao;
    }

    if (is_string($maquina_lavar)) {
      $this->maquina_lavar = $maquina_lavar;
    }
        
    if (is_string($microondas)) {
      $this->microondas = $microondas;
    }

    if (is_string($video_dvd)) {
      $this->video_dvd = $video_dvd;
    }

    if (is_string($televisao)) {
      $this->televisao = $televisao;
    }

    if (is_string($celular)) {
      $this->celular = $celular;
    }        

    if (is_string($telefone)) {
      $this->telefone = $telefone;
    } 

    if (is_string($quant_pessoas)) {
      $this->quant_pessoas = $quant_pessoas;
    }           

    if (is_numeric($renda)) {
      $this->renda = $renda;
    }           

    if (is_numeric($agua_encanada)) {
      $this->agua_encanada = $agua_encanada;
    }         

    if (is_string($poco)) {
      $this->poco = $poco;
    }         

    if (is_string($energia)) {
      $this->energia = $energia;
    }         

    if (is_string($esgoto)) {
      $this->esgoto = $esgoto;
    }         

    if (is_string($fossa)) {
      $this->fossa = $fossa;
    }         

    if (is_string($lixo)) {
      $this->lixo = $lixo;
    }                               


  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {


    if (is_numeric($this->ref_cod_aluno))
    {

      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';


    if (is_numeric($this->ref_cod_aluno)) {
        $campos .= "{$gruda}ref_cod_aluno";
        $valores .= "{$gruda}'{$this->ref_cod_aluno}'";
        $gruda = ", ";
    }

    if (is_string($this->moradia)) {
        $campos .= "{$gruda}moradia";
        $valores .= "{$gruda}'{$this->moradia}'";
        $gruda = ", ";
    }

    if (is_string($this->material)) {
        $campos .= "{$gruda}material";
        $valores .= "{$gruda}'{$this->material}'";
        $gruda = ", ";
    }

    if (is_string($this->casa_outra)) {
        $campos .= "{$gruda}casa_outra";
        $valores .= "{$gruda}'{$this->casa_outra}'";
        $gruda = ", ";
    }

    if (is_numeric($this->moradia_situacao)) {
        $campos .= "{$gruda}moradia_situacao";
        $valores .= "{$gruda}'{$this->moradia_situacao}'";
        $gruda = ", ";
    }

    if (is_numeric($this->quartos)) {
        $campos .= "{$gruda}quartos";
        $valores .= "{$gruda}'{$this->quartos}'";
        $gruda = ", ";
    }    

    if (is_numeric($this->sala)) {
        $campos .= "{$gruda}sala";
        $valores .= "{$gruda}'{$this->sala}'";
        $gruda = ", ";
    }

   if (is_numeric($this->copa)) {
        $campos .= "{$gruda}copa";
        $valores .= "{$gruda}'{$this->copa}'";
        $gruda = ", ";
    }

    if (is_numeric($this->banheiro)) {
        $campos .= "{$gruda}banheiro";
        $valores .= "{$gruda}'{$this->banheiro}'";
        $gruda = ", ";
    }

    if (is_numeric($this->garagem)) {
        $campos .= "{$gruda}garagem";
        $valores .= "{$gruda}'{$this->garagem}'";
        $gruda = ", ";
    }


    if (is_string($this->empregada_domestica)) {
        $campos .= "{$gruda}empregada_domestica";
        $valores .= "{$gruda}'{$this->empregada_domestica}'";
        $gruda = ", ";
    }

    if (is_string($this->automovel)) {
        $campos .= "{$gruda}automovel";
        $valores .= "{$gruda}'{$this->automovel}'";
        $gruda = ", ";
    }

    if (is_string($this->motocicleta)) {
        $campos .= "{$gruda}motocicleta";
        $valores .= "{$gruda}'{$this->motocicleta}'";
        $gruda = ", ";
    }

    if (is_string($this->geladeira)) {
        $campos .= "{$gruda}geladeira";
        $valores .= "{$gruda}'{$this->geladeira}'";
        $gruda = ", ";
    }

    if (is_string($this->fogao)) {
        $campos .= "{$gruda}fogao";
        $valores .= "{$gruda}'{$this->fogao}'";
        $gruda = ", ";
    }

    if (is_string($this->maquina_lavar)) {
        $campos .= "{$gruda}maquina_lavar";
        $valores .= "{$gruda}'{$this->maquina_lavar}'";
        $gruda = ", ";
    }

    if (is_string($this->microondas)) {
        $campos .= "{$gruda}microondas";
        $valores .= "{$gruda}'{$this->microondas}'";
        $gruda = ", ";
    }

    if (is_string($this->video_dvd)) {
        $campos .= "{$gruda}video_dvd";
        $valores .= "{$gruda}'{$this->video_dvd}'";
        $gruda = ", ";
    }

    if (is_string($this->televisao)) {
        $campos .= "{$gruda}televisao";
        $valores .= "{$gruda}'{$this->televisao}'";
        $gruda = ", ";
    }

    if (is_string($this->celular)) {
        $campos .= "{$gruda}celular";
        $valores .= "{$gruda}'{$this->celular}'";
        $gruda = ", ";
    }    

    if (is_string($this->telefone)) {
        $campos .= "{$gruda}telefone";
        $valores .= "{$gruda}'{$this->telefone}'";
        $gruda = ", ";
    }
        
    if (is_numeric($this->quant_pessoas)) {
        $campos .= "{$gruda}quant_pessoas";
        $valores .= "{$gruda}'{$this->quant_pessoas}'";
        $gruda = ", ";
    }
    
    if (is_numeric($this->renda)) {
        $campos .= "{$gruda}renda";
        $valores .= "{$gruda}'{$this->renda}'";
        $gruda = ", ";
    }   

    if (is_string($this->agua_encanada)) {
        $campos .= "{$gruda}agua_encanada";
        $valores .= "{$gruda}'{$this->agua_encanada}'";
        $gruda = ", ";
    }

    if (is_string($this->poco)) {
        $campos .= "{$gruda}poco";
        $valores .= "{$gruda}'{$this->poco}'";
        $gruda = ", ";
    }

    if (is_string($this->energia)) {
        $campos .= "{$gruda}energia";
        $valores .= "{$gruda}'{$this->energia}'";
        $gruda = ", ";
    }

    if (is_string($this->esgoto)) {
        $campos .= "{$gruda}esgoto";
        $valores .= "{$gruda}'{$this->esgoto}'";
        $gruda = ", ";
    }

    if (is_string($this->fossa)) {
        $campos .= "{$gruda}fossa";
        $valores .= "{$gruda}'{$this->fossa}'";
        $gruda = ", ";
    }

    if (is_string($this->lixo)) {
        $campos .= "{$gruda}lixo";
        $valores .= "{$gruda}'{$this->lixo}'";
        $gruda = ", ";
    }

    $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");
    return $this->ref_cod_aluno;
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    
    if (is_numeric($this->ref_cod_aluno)) {
      $db  = new clsBanco();
      $set = '';

    if (is_numeric($this->ref_cod_aluno)) {
        $set .= "{$gruda}ref_cod_aluno = '{$this->ref_cod_aluno}'";
        $gruda = ", ";
    }

    if (is_string($this->moradia)) {
        $set .= "{$gruda}moradia = '{$this->moradia}'";
        $gruda = ", ";
    }

    if (is_string($this->material)) {
        $set .= "{$gruda}material = '{$this->material}'";
        $gruda = ", ";
    }

    if (is_string($this->casa_outra)) {
        $set .= "{$gruda}casa_outra = '{$this->casa_outra}'";
        $gruda = ", ";
    }

    if (is_numeric($this->moradia_situacao)) {
        $set .= "{$gruda}moradia_situacao = '{$this->moradia_situacao}'";
        $gruda = ", ";
    }

    if (is_numeric($this->quartos)) {
        $set .= "{$gruda}quartos = '{$this->quartos}'";
        $gruda = ", ";
    }

   if (is_numeric($this->sala)) {
        $set .= "{$gruda}sala = '{$this->sala}'";
        $gruda = ", ";
    }

    if (is_numeric($this->copa)) {
        $set .= "{$gruda}copa = '{$this->copa}'";
        $gruda = ", ";
    }

    if (is_numeric($this->banheiro)) {
        $set .= "{$gruda}banheiro = '{$this->banheiro}'";
        $gruda = ", ";
    }

    if (is_numeric($this->garagem)) {
        $set .= "{$gruda}garagem = '{$this->garagem}'";
        $gruda = ", ";
    }

    if (is_string($this->empregada_domestica)) {
        $set .= "{$gruda}empregada_domestica = '{$this->empregada_domestica}'";
        $gruda = ", ";
    }

    if (is_string($this->automovel)) {
        $set .= "{$gruda}automovel = '{$this->automovel}'";
        $gruda = ", ";
    }

    if (is_string($this->motocicleta)) {
        $set .= "{$gruda}motocicleta = '{$this->motocicleta}'";
        $gruda = ", ";
    }

    if (is_string($this->computador)) {
        $set .= "{$gruda}computador = '{$this->computador}'";
        $gruda = ", ";
    }

    if (is_string($this->geladeira)) {
        $set .= "{$gruda}geladeira = '{$this->geladeira}'";
        $gruda = ", ";
    }


    if (is_string($this->fogao)) {
        $set .= "{$gruda}fogao = '{$this->fogao}'";
        $gruda = ", ";
    }

    if (is_string($this->maquina_lavar)) {
        $set .= "{$gruda}maquina_lavar = '{$this->maquina_lavar}'";
        $gruda = ", ";
    }

    if (is_string($this->microondas)) {
        $set .= "{$gruda}microondas = '{$this->microondas}'";
        $gruda = ", ";
    }

    if (is_string($this->video_dvd)) {
        $set .= "{$gruda}video_dvd = '{$this->video_dvd}'";
        $gruda = ", ";
    }

    if (is_string($this->televisao)) {
        $set .= "{$gruda}televisao = '{$this->televisao}'";
        $gruda = ", ";
    }

    if (is_string($this->celular)) {
        $set .= "{$gruda}celular = '{$this->celular}'";
        $gruda = ", ";
    }

    if (is_string($this->telefone)) {
        $set .= "{$gruda}telefone = '{$this->telefone}'";
        $gruda = ", ";
    }

    if (is_numeric($this->quant_pessoas)) {
        $set .= "{$gruda}quant_pessoas = '{$this->quant_pessoas}'";
        $gruda = ", ";
    }

    if (is_numeric($this->renda)) {
        $set .= "{$gruda}renda = '{$this->renda}'";
        $gruda = ", ";
    }
        
    if (is_string($this->agua_encanada)) {
        $set .= "{$gruda}agua_encanada = '{$this->agua_encanada}'";
        $gruda = ", ";
    }

    if (is_string($this->poco)) {
        $set .= "{$gruda}poco = '{$this->poco}'";
        $gruda = ", ";
    }

    if (is_string($this->energia)) {
        $set .= "{$gruda}energia = '{$this->energia}'";
        $gruda = ", ";
    }

    if (is_string($this->esgoto)) {
        $set .= "{$gruda}esgoto = '{$this->esgoto}'";
        $gruda = ", ";
    }

    if (is_string($this->fossa)) {
        $set .= "{$gruda}fossa = '{$this->fossa}'";
        $gruda = ", ";
    }

    if (is_string($this->lixo)) {
        $set .= "{$gruda}lixo = '{$this->lixo}'";
        $gruda = ", ";
    }


      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista()
  {
    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
    $filtros = "";    
    $whereAnd = " WHERE ";

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista))+2;
    $resultado = array();

    $sql .= $filtros.$whereNomes.$this->getOrderby() . $this->getLimite();

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
    if (is_numeric($this->ref_cod_aluno)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function existe()
  {
    if (is_numeric($this->ref_cod_aluno)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");
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
    if (is_numeric($this->ref_cod_aluno)) {
      $sql = "DELETE FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'";
      $db = new clsBanco();
      $db->Consulta($sql);
      return true;
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