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
 * @author    Caroline Salib <caroline@portabillis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
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
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Bloqueio de lançamento de notas e faltas por etapa');
    $this->processoAp = 999848;
    $this->addEstilo("localizacaoSistema");
  }
}

/**
 * indice class.
 *
 * @author    Caroline Salib <caroline@portabillis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsDetalhe
{
  var $titulo;

  var $cod_bloqueio;

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = 'Bloqueio de lan&ccedil;amento de notas e faltas por etapa - Detalhe';
    $this->addBanner('imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg',
      'Intranet');

    $this->cod_bloqueio = $_GET['cod_bloqueio'];

    $tmp_obj = new clsPmieducarBloqueioLancamentoFaltasNotas($this->cod_bloqueio);

    $registro = $tmp_obj->detalhe();

    if (!$registro) {
      header('Location: educar_bloqueio_lancamento_faltas_notas_lst.php');
      die();
    }

    //Nome da etapa
    $obj_etapa = new clsPmieducarAnoLetivoModulo($registro['ano'], $registro['ref_cod_escola'], $registro['etapa']);
    $registro['etapa'] = $obj_etapa->getNomeModulo();

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

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "i-Educar - Escola",
         ""                                  => "Detalhe de bloqueio de lan&ccedil;amento de notas e faltas por etapa"
    ));
    $this->enviaLocalizacao($localizacao->montar());
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();