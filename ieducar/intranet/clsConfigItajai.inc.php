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
 * @package   iEd
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/constants.inc.php';

/**
 * clsConfig class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsConfig
{

  /**
   * Tempo em segundos para relatar uma query SQL.
   */
  const CLS_CONFIG_SQL_TEMPO    = 3;

  /**
   * Tempo em segundos para relatar um carregamento de página.
   */
  const CLS_CONFIG_PAGINA_TEMPO = 5;

  /**
   * Array com os parâmetros de configuração
   */
  public $arrayConfig = array();

  /**
   * Switch para habilitar depuração.
   * @var  int
   */
  public $depurar = 0;


  /**
   * Construtor.
   *
   * Atribui os valores padrões das diretivas de configuração.
   */
  public function __construct() {
    $this->setArrayConfig();
  }

  /**
   * Configura o array $arrayConfig com as diretivas passadas pelo ieducar.ini.
   */
  private function setArrayConfig()
  {
    global $coreExt;
    $config = $coreExt['Config'];

    $config = $coreExt['Config']->app;

    // Nome da instituição
    $this->_instituicao = $config->template->vars->instituicao . ' - ';

    // E-mails dos administradores para envio de relatórios de performance
    $emails = $config->admin->reports->emails->toArray();
    $this->arrayConfig['ArrStrEmailsAdministradores'] = $emails;

    // Diretório dos templates de e-mail
    $this->arrayConfig['strDirTemplates'] = "templates/";

    // Quantidade de segundos para relatar uma query SQL
    $segundosSQL = $config->get($config->admin->reports->sql_tempo,
      self::CLS_CONFIG_SQL_TEMPO);
    $segundosSQL = $segundosSQL > 0 ?
      $segundosSQL : self::CLS_CONFIG_SQL_TEMPO;

    $this->arrayConfig['intSegundosQuerySQL'] = $segundosSQL;

    // Quantidade de segundos para relatar o tempo de carregamento de página
    $segundosPagina = $config->get($config->admin->reports->pagina_tempo,
      self::CLS_CONFIG_PAGINA_TEMPO);
    $segundosPagina = $segundosPagina > 0 ?
      $segundosPagina : self::CLS_CONFIG_PAGINA_TEMPO;

    $this->arrayConfig['intSegundosProcessaPagina'] = $segundosPagina;
  }


  /**
   * Reliza um var_dump da variável passada.
   * @see  http://php.net/var_dump
   * @param  mixed  $msg
   */
  protected function Depurar($msg)
  {
    if ($this->depurar)
    {
      if ($this->depurar == 1)
        echo "\n\n<!--";

      echo "<pre>";

      if (is_array($msg))
        var_dump($msg);
      else
        echo $msg;

      echo "</pre>";

      if ($this->depurar == 1)
        echo "-->\n\n";
    }
  }
}