<?php

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
 * @since     Arquivo disponÃ­vel desde a versÃ£o 1.0.0
 * @version   $Id$
 */

require_once "include/clsBase.inc.php";
require_once "include/clsDetalhe.inc.php";
require_once "include/clsBanco.inc.php";
require_once "include/pmieducar/geral.inc.php";

/**
 * clsIndexBase class.
 *
 * @author    Caroline Salib <caroline@portabillis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponÃ­vel desde a versÃ£o 1.0.0
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
 * @since     Classe disponÃ­vel desde a versÃ£o 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsDetalhe
{
  var $titulo;

  var $cod_bloqueio;

  function Gerar()
  {
    $this->titulo = 'Bloqueio de lan&ccedil;amento de notas e faltas por etapa - Detalhe';
    $this->addBanner('imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg',
      'Intranet');

    $this->cod_bloqueio = $_GET['cod_bloqueio'];

    $tmp_obj = new clsPmieducarBloqueioLancamentoFaltasNotas($this->cod_bloqueio);

    $registro = $tmp_obj->detalhe();

    if (!$registro) {
        $this->simpleRedirect('educar_bloqueio_lancamento_faltas_notas_lst.php');
    }

    //Nome da etapa
    $etapas = array(
      1 => '1ª Etapa',
      2 => '2ª Etapa',
      3 => '3ª Etapa',
      4 => '4ª Etapa'
    );
    $registro['etapa'] = $etapas[$registro['etapa']];

    // Dados da escola
    $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
    $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
    $registro['ref_cod_escola'] = $det_ref_cod_escola['nome'];


    if ($registro['ano']) {
      $this->addDetalhe(array('Ano', $registro['ano']));
    }

    if ($registro['ref_cod_escola']) {
      $this->addDetalhe(array('Escola', $registro['ref_cod_escola']));
    }

    if ($registro['etapa']) {
      $this->addDetalhe(array('Etapa', $registro['etapa']));
    }

    if ($registro['data_inicio']) {
      $this->addDetalhe(array('Data in&iacute;cio', dataToBrasil($registro['data_inicio'])));
    }

    if ($registro['data_fim']) {
      $this->addDetalhe(array('Data final', dataToBrasil($registro['data_fim'])));
    }

    $obj_permissoes = new clsPermissoes();

    if ($obj_permissoes->permissao_cadastra(999848, $this->pessoa_logada, 7)) {
      $this->url_novo   = 'educar_bloqueio_lancamento_faltas_notas_cad.php';
      $this->url_editar = sprintf('educar_bloqueio_lancamento_faltas_notas_cad.php?cod_bloqueio=%d', $this->cod_bloqueio);
    }

    $this->url_cancelar = 'educar_bloqueio_lancamento_faltas_notas_lst.php';
    $this->largura      = '100%';

    $this->breadcrumb('Detalhe de bloqueio de lançamento de notas e faltas por etapa', [
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
