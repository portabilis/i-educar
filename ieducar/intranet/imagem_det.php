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

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/imagem/clsPortalImagemTipo.inc.php';
require_once 'include/imagem/clsPortalImagem.inc.php';

/**
 * clsIndex class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Imagem
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndex extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . 'Banco de Imagens');
    $this->processoAp = '473';
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Imagem
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsDetalhe
{
  function Gerar()
  {
    $this->titulo = 'Detalhe da Imagem';
    $this->addBanner('imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg', 'Intranet');

    $cod_imagem = $_GET['cod_imagem'];

    $objimagem = new clsPortalImagem($cod_imagem);
    $detalheImagem = $objimagem->detalhe();
    $objimagemTipo = new clsPortalImagemTipo($detalheImagem['ref_cod_imagem_tipo']);
    $detalheImagemTipo = $objimagemTipo->detalhe();

    $this->addDetalhe(array('Tipo da Imagem', $detalheImagemTipo['nm_tipo']));
    $this->addDetalhe(array('Nome', $detalheImagem['nm_imagem']));
    $this->addDetalhe(array('Imagem', "<img src='banco_imagens/{$detalheImagem['caminho']}' alt='{$detalheImagem['nm_imagem']}' title='{$detalheImagem['nm_imagem']}'>"));
    $this->addDetalhe(array('Extensão', $detalheImagem['extensao']));
    $this->addDetalhe(array('Largura', $detalheImagem['largura']));
    $this->addDetalhe(array('Altura', $detalheImagem['altura']));
    $this->addDetalhe(array('Data de Cadastro', dataFromPgToBr($detalheImagem['data_cadastro']) ));

    $this->url_novo     = 'imagem_cad.php';
    $this->url_editar   = 'imagem_cad.php?cod_imagem=' . $cod_imagem;
    $this->url_cancelar = 'imagem_lst.php';

    $this->largura = "100%";
  }
}


// Instancia objeto de página
$pagina = new clsIndex();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();