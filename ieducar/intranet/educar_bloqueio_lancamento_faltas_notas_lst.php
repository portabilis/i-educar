<?php
//error_reporting(E_ERROR);
//ini_set("display_errors", 1);
/**
 * i-Educar - Sistema de gestÃ£o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de ItajaÃ­
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa Ã© software livre; vocÃª pode redistribuÃ­-lo e/ou modificÃ¡-lo
 * sob os termos da LicenÃ§a PÃºblica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versÃ£o 2 da LicenÃ§a, como (a seu critÃ©rio)
 * qualquer versÃ£o posterior.
 *
 * Este programa Ã© distribuÃ­Â­do na expectativa de que seja Ãºtil, porÃ©m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implÃ­Â­cita de COMERCIABILIDADE OU
 * ADEQUAÃÃO A UMA FINALIDADE ESPECÃFICA. Consulte a LicenÃ§a PÃºblica Geral
 * do GNU para mais detalhes.
 *
 * VocÃª deve ter recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral do GNU junto
 * com este programa; se nÃ£o, escreva para a Free Software Foundation, Inc., no
 * endereÃ§o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Caroline Salib <caroline@portabillis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     ?
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'CoreExt/View/Helper/UrlHelper.php';

/**
 * clsIndexBase class.
 *
 * @author    Caroline Salib <caroline@portabillis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     ?
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Bloqueio de lanÃ§amento de notas e faltas por etapa');
    $this->processoAp = 999848;
  }
}

/**
 * indice class.
 *
 * @author    Caroline Salib <caroline@portabillis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     ?
 * @version   @@package_version@@
 */
class indice extends clsListagem
{
  var $pessoa_logada;
  var $titulo;
  var $limite;
  var $offset;

  var $ano;
  var $ref_cod_escola;
  var $etapa;
  var $data_inicio;
  var $data_fim;

  function Gerar()
  {

    // Helper para url
    $urlHelper = CoreExt_View_Helper_UrlHelper::getInstance();

    $this->titulo = 'Bloqueio de lan&ccedil;amento de notas e faltas por etapa - Listagem';

    // passa todos os valores obtidos no GET para atributos do objeto
    foreach ($_GET as $var => $val) {
      $this->$var = ($val === '') ? NULL : $val;
    }

    $this->addBanner('imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg',
      'Intranet');

    $this->addCabecalhos(array(
      'Escola', 'Ano', 'Etapa', 'Data in&iacute;cio', 'Data fim'
    ));

    $this->inputsHelper()->dynamic(array('ano'), array('required' => FALSE));

    $obj_permissao = new clsPermissoes();
    $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);

    $get_escola      = true;
    $obrigatorio     = false;
    $exibe_nm_escola = true;

    include("include/pmieducar/educar_campo_lista.php");

    // Paginador
    $this->limite = 20;
    $this->offset = $_GET['pagina_' . $this->nome] ?
      $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

    $obj_bloqueio_lancamento_fn = new clsPmieducarBloqueioLancamentoFaltasNotas();
    $obj_bloqueio_lancamento_fn->setLimite($this->limite, $this->offset);

    $lista = $obj_bloqueio_lancamento_fn->lista(
      $this->ano,
      $this->ref_cod_escola
    );

     $total = $obj_bloqueio_lancamento_fn->_total;

    // monta a lista
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {

        $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
        $nm_escola = $det_ref_cod_escola['nome'];

        $etapas = array(
          1 => '1ª Etapa',
          2 => '2ª Etapa',
          3 => '3ª Etapa',
          4 => '4ª Etapa'
        );
        $nm_etapa = $etapas[$registro['etapa']];

        // Dados para a url
        $url     = 'educar_bloqueio_lancamento_faltas_notas_det.php';
        $options = array('query' => array(
          'cod_bloqueio'  => $registro['cod_bloqueio']
        ));

        $this->addLinhas(array(
          $urlHelper->l($nm_escola, $url, $options),
          $urlHelper->l($registro['ano'], $url, $options),
          $urlHelper->l($nm_etapa, $url, $options),
          $urlHelper->l(dataToBrasil($registro['data_inicio']), $url, $options),
          $urlHelper->l(dataToBrasil($registro['data_fim']), $url, $options)
        ));
      }
    }

    $this->addPaginador2('educar_bloqueio_lancamento_faltas_notas_lst.php', $total, $_GET,
      $this->nome, $this->limite);

    $obj_permissoes = new clsPermissoes();

    if ($obj_permissoes->permissao_cadastra(999848, $this->pessoa_logada, 7)) {
      $this->array_botao_url[] = 'educar_bloqueio_lancamento_faltas_notas_cad.php';
      $this->array_botao[]     = 'Novo';
    }

    $this->largura = '100%';

    $this->breadcrumb('Listagem de bloqueio de lan&ccedil;amento de notas e faltas por etapa', [
        url('intranet/educar_index.php') => 'Escola',
    ]);
  }
}

// Instancia objeto de pÃ¡gina
$pagina = new clsIndexBase();

// Instancia objeto de conteÃºdo
$miolo = new indice();

// Atribui o conteÃºdo Ã   pÃ¡gina
$pagina->addForm($miolo);

// Gera o cÃ³digo HTML
$pagina->MakeAll();
