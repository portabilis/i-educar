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

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesMotorista.inc.php';

require_once 'Portabilis/Date/Utils.php';
require_once 'Portabilis/View/Helper/Application.php';


/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Motoristas');
    $this->processoAp = 21236;
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsDetalhe
{
  var $titulo;

  function Gerar()
  {
    // Verificação de permissão para cadastro.
    $this->obj_permissao = new clsPermissoes();

    $this->nivel_usuario = $this->obj_permissao->nivel_acesso($this->pessoa_logada);

    $this->titulo = 'Motorista - Detalhe';


    $cod_motorista = $_GET['cod_motorista'];

    $tmp_obj = new clsModulesMotorista($cod_motorista);
    $registro = $tmp_obj->detalhe();

    if (! $registro) {
        $this->simpleRedirect('transporte_motorista_lst.php');
    }

    $this->addDetalhe( array("Código do motorista", $cod_motorista));
    $this->addDetalhe( array("Nome", $registro['nome_motorista'].'<br/> <a target=\'_blank\' style=\' text-decoration: underline;\' href=\'atendidos_det.php?cod_pessoa='.$registro['ref_idpes'].'\'>Visualizar pessoa</a>') );
    $this->addDetalhe( array("CNH", $registro['cnh']) );
    $this->addDetalhe( array("Categoria", $registro['tipo_cnh']) );
    if (trim($registro['dt_habilitacao'])!='')
      $this->addDetalhe( array("Data da habilitação", Portabilis_Date_Utils::pgSQLToBr($registro['dt_habilitacao']) ));
    if (trim($registro['vencimento_cnh'])!='')
      $this->addDetalhe( array("Vencimento da habilitação", Portabilis_Date_Utils::pgSQLToBr($registro['vencimento_cnh']) ) );

    $this->addDetalhe( array("Observa&ccedil;&atilde;o", $registro['observacao']));
    $this->url_cancelar = "transporte_motorista_lst.php";

    $obj_permissao = new clsPermissoes();

    if($obj_permissao->permissao_cadastra(21236, $this->pessoa_logada,7,null,true))
    {
      $this->url_novo = "../module/TransporteEscolar/Motorista";
      $this->url_editar = "../module/TransporteEscolar/motorista?id={$cod_motorista}";
    }

    $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_transporte_escolar_index.php"                  => "Transporte escolar",
         ""                                  => "Detalhe do motorista"
    ));
    $this->enviaLocalizacao($localizacao->montar());
  }
}

// Instancia o objeto da página
$pagina = new clsIndexBase();

// Instancia o objeto de conteúdo
$miolo = new indice();

// Passa o conteúdo para a página
$pagina->addForm($miolo);

// Gera o HTML
$pagina->MakeAll();
