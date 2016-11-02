<?php

/*
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
 */

/**
 * CoreExt_Config_Ini class.
 *
 * Essa classe torna possível o uso de um arquivo .ini como meio de configuração
 * da aplicação. O parsing do arquivo é feito através da função PHP nativa
 * parse_ini_file. Possibilita o uso de herança simples e separação de
 * namespaces no arquivo ini, tornando simples a criação de diferentes espaços
 * de configuração para o uso em ambientes diversos como produção,
 * desenvolvimento, testes e outros.
 *
 * Para o uso dessa classe, é necessário que o arquivo ini tenha no mínimo uma
 * seção de configuração. A seção padrão a ser usada é a production mas isso
 * não impede que você a nomeie como desejar.
 *
 * Essa classe foi fortemente baseada na classe Zend_Config_Ini do Zend
 * Framework.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     CoreExt
 * @subpackage  Config
 * @see         lib/CoreExt/Config.class.php
 * @since       Classe disponível desde a versão 1.1.0
 * @version     $Id$
 */
class CoreExt_Config_Ini extends CoreExt_Config
{

  /**
   * Caractere de herança das seções do arquivo ini.
   */
  const COREEXT_CONFIG_INI_INHERITANCE_SEP = ':';

  /**
   * Caractere de namespace das diretivas do arquivo ini.
   */
  const COREEXT_CONFIG_INI_NAMESPACE_SEP   = '.';

  /**
   * Array contendo as diretivas de configuração separadas por namespace.
   * @var array
   */
  protected $iniArr = array();

  /**
   * Construtor.
   *
   * @param  $filename  Caminho para o arquivo ini
   * @param  $section   Seção desejada para o carregamento das configurações
   */
  public function __construct($filename, $section = 'production')
  {
    require_once 'CoreExt/Config.class.php';

    $this->iniArr = $this->loadFile($filename);
    parent::__construct($this->iniArr[$section]);
  }

  /**
   * Carrega as configurações para o ambiente desejado (seção do arquivo ini)
   * @param  string  $section
   */
  public function changeEnviroment($section = 'production') {
    $this->changeSection($section);
  }

  /**
   * Verifica se possui a seção desejada.
   * @param  string  $section
   */
  function hasEnviromentSection($section) {
    return is_array($this->iniArr[$section]);
  }

  /**
   * Carrega as configuração da seção desejada.
   * @param  string  $section
   */
  protected function changeSection($section = 'production') {
    parent::__construct($this->iniArr[$section]);
  }

  /**
   * Parsing do arquivo ini.
   *
   * Realiza o parsing do arquivo ini, separando as seções, criando as relações
   * de herança e separando cada diretiva do arquivo em um array
   * multidimensional.
   *
   * @link    http://php.net/parse_ini_file  Documentação da função parse_ini_file
   * @param   string  $filename
   * @throws  Exception
   * @return  array
   */
  private function loadFile($filename)
  {
    $iniArr = array();

    // Faz o parsing separando as seções (parâmetro TRUE)
    // Supressão simples dificulta os unit test. Altera o error handler para
    // que use um método da classe CoreExt_Config.
    set_error_handler(array($this, 'configErrorHandler'), E_ALL);
    $config = parse_ini_file($filename, TRUE);
    restore_error_handler();

    /*
     * No PHP 5.2.7 o array vem FALSE quando existe um erro de sintaxe. Antes
     * o retorno vinha como array vazio.
     * @link  http://php.net/parse_ini_file#function.parse-ini-file.changelog  Changelog da função parse_ini_file
     */
    if (count($this->errors) > 0) {
      throw new Exception('Arquivo ini com problemas de sintaxe. Verifique a sintaxe arquivo \''. $filename .'\'.');
    }

    foreach ($config as $section => $data) {
      $index = $section;

      if (FALSE !== strpos($section, self::COREEXT_CONFIG_INI_INHERITANCE_SEP)) {
        $sections = explode(self::COREEXT_CONFIG_INI_INHERITANCE_SEP, $section);
        // Apenas uma herança por seção é permitida
        if (count($sections) > 2) {
          throw new Exception('Não é possível herdar mais que uma seção.');
        }

        // Armazena seção atual e seção de herança
        $section = trim($sections[0]);
        $extends = trim($sections[1]);
      }

      // Processa as diretivas da seção atual para separarar os namespaces
      $iniArr[$section] = $this->processSection($config[$index]);

      /*
       * Verifica se a seção atual herda de alguma outra seção. Se a seção de
       * herança não existir, lança uma exceção.
       */
      if (isset($extends)) {
        if (!array_key_exists($extends, $iniArr)) {
          $message = sprintf('Não foi possível estender %s, seção %s não existe',
            $section, $extends);
          throw new Exception($message);
        }

        // Mescla recursivamente os dois arrays. Os valores definidos na seção
        // atual não são sobrescritos
        $iniArr[$section] = $this->arrayMergeRecursiveDistinct($iniArr[$extends], $iniArr[$section]);
        unset($extends);
      }
    }

    return $iniArr;
  }

  /**
   * Processa uma seção de um array de arquivo ini.
   *
   * Processa a seção, inclusive as diretivas da seção, separando-as em
   * um array em namespace. Dessa forma, uma diretiva que era, por exemplo,
   * app.database.dbname = ieducardb irá se tornar:
   * <code>
   * app => array(database => array(dbname => ieducardb))
   * </code>
   *
   * Diretivas no mesmo namespace viram novas chaves no mesmo array:
   * app.database.hostname => localhost
   * <code>
   * app => array(
   *   database => array(
   *     dbname   => ieducardb
   *     hostname => localhost
   *   )
   * )
   * </code>
   *
   * @param   array  $data  Array contendo as diretivas de uma seção do arquivo ini
   * @return  array
   */
  private function processSection(array $data)
  {
    $entries = $data;
    $config  = array();

    foreach ($entries as $key => $value) {
      if (FALSE !== strpos($key, self::COREEXT_CONFIG_INI_NAMESPACE_SEP)) {
        $keys = explode(self::COREEXT_CONFIG_INI_NAMESPACE_SEP, $key);
      }
      else {
        $keys = (array) $key;
      }

      $config = $this->processDirectives($value, $keys, $config);
    }

    return $config;
  }

  /**
   * Cria recursivamente um array aninhado (namespaces) usando os índices
   * progressivamente.
   *
   * Exemplo:
   * <code>
   * $value  = ieducardb
   * $keys   = array('app', 'database', 'dbname');
   *
   * $config['app'] => array(
   *   'database' => array(
   *     'dbname' => 'ieducardb'
   *   )
   * );
   * </code>
   *
   * @param   mixed  $value   O valor da diretiva parseada por parse_ini_file
   * @param   array  $keys    O array contendo as chaves das diretivas (0 => app, 1 => database, 2 => dbname)
   * @param   array  $config  O array contêiner com as chaves em suas respectivas dimensões
   * @return  array
   */
  private function processDirectives($value, $keys, $config = array())
  {
    $key = array_shift($keys);

    if (count($keys) > 0) {
      if (!array_key_exists($key, $config)) {
        $config[$key] = array();
      }

      $config[$key] = $this->processDirectives($value, $keys, $config[$key]);
    }
    else {
      $config[$key] = $value;
    }

    return $config;
  }

}