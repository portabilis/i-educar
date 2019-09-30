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
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesPontoTransporteEscolar.inc.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Pontos');
    $this->processoAp = 21239;
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.scclsModulesPontoTransporteEscolar.gov.br>
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

    $this->titulo = 'Ponto - Detalhe';

    $cod_ponto_transporte_escolar = $_GET['cod_ponto'];
    $tmp_obj = new clsModulesPontoTransporteEscolar($cod_ponto_transporte_escolar);
    $registro = $tmp_obj->detalhe();

    if (! $registro) {
        $this->simpleRedirect('transporte_ponto_lst.php');
    }

    $this->addDetalhe( array("Código do ponto", $cod_ponto_transporte_escolar));
    $this->addDetalhe( array("Descrição", $registro['descricao']) );

    if (is_numeric($registro['cep']) && is_numeric($registro['idlog']) && is_numeric($registro['idbai'])){
      $this->addDetalhe( array("CEP", int2CEP($registro['cep'])) );
      $this->addDetalhe( array("Município - UF", $registro['municipio'] . ' - '. $registro['sigla_uf']) );
      $this->addDetalhe( array("Distrito", $registro['distrito']) );
      $this->addDetalhe( array("Bairro", $registro['bairro']) );
      $this->addDetalhe( array("Zona de localização", $registro['zona_localizacao'] == 1 ? 'Urbana' : 'Rural' ) );
      $this->addDetalhe( array("Endereço", $registro['idtlog'] . ' ' . $registro['logradouro']) );
      $this->addDetalhe( array("Número", $registro['numero']) );
      $this->addDetalhe( array("Complemento", $registro['complemento']) );
    }

    $obj_permissao = new clsPermissoes();

    if($obj_permissao->permissao_cadastra(21239, $this->pessoa_logada,7,null,true))
    {
      $this->url_novo = "../module/TransporteEscolar/Ponto";
      $this->url_editar = "../module/TransporteEscolar/Ponto?id={$cod_ponto_transporte_escolar}";
    }

    $this->url_cancelar = "transporte_ponto_lst.php";

    $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_transporte_escolar_index.php"                  => "Transporte escolar",
         ""                                  => "Detalhe do ponto"
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
