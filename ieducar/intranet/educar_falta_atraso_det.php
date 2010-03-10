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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Falta Atraso');
    $this->processoAp = 635;
  }
}

/**
 * indice class.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsDetalhe
{
  var $titulo;

  var $cod_falta_atraso;
  var $ref_cod_escola;
  var $ref_ref_cod_instituicao;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_servidor;
  var $tipo;
  var $data_falta_atraso;
  var $qtd_horas;
  var $qtd_min;
  var $justificada;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = 'Falta Atraso - Detalhe';
    $this->addBanner('imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg', 'Intranet');

    $this->ref_cod_servidor        = $_GET['ref_cod_servidor'];
    $this->ref_cod_escola          = $_GET['ref_cod_escola'];
    $this->ref_ref_cod_instituicao = $_GET['ref_cod_instituicao'];

    $tmp_obj = new clsPmieducarFaltaAtraso();
    $tmp_obj->setOrderby('data_falta_atraso DESC');
    $this->cod_falta_atraso = $_GET['cod_falta_atraso'];
    $registro = $tmp_obj->lista($this->cod_falta_atraso);

    if (!$registro) {
      header('Location: ' . sprintf(
        'educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
        $this->ref_cod_servidor, $this->ref_ref_cod_instituicao));
      die();
    }
    else {
      $tabela = '<table>
                 <tr align=center>
                     <td bgcolor="#a1b3bd"><b>Dia</b></td>
                     <td bgcolor="#a1b3bd"><b>Tipo</b></td>
                     <td bgcolor="#a1b3bd"><b>Qtd. Horas</b></td>
                     <td bgcolor="#a1b3bd"><b>Qtd. Minutos</b></td>
                     <td bgcolor="#a1b3bd"><b>Escola</b></td>
                     <td bgcolor="#a1b3bd"><b>Instituição</b></td>
                 </tr>';

      $cont  = 0;
      $total = 0;

      foreach ($registro as $falta) {
        if (($cont % 2) == 0) {
          $color = ' bgcolor="#E4E9ED" ';
        }
        else {
          $color = ' bgcolor="#FFFFFF" ';
        }

        $obj_esc = new clsPmieducarEscolaComplemento($falta['ref_cod_escola']);
        $det_esc = $obj_esc->detalhe();
        $obj_ins = new clsPmieducarInstituicao($falta['ref_ref_cod_instituicao']);
        $det_ins = $obj_ins->detalhe();

        $corpo .= sprintf('
          <tr>
            <td %s align="left">%s</td>
            <td %s align="left">%s</td>
            <td %s align="right">%s</td>
            <td %s align="right">%s</td>
            <td %s align="left">%s</td>
            <td %s align="left">%s</td>
          </tr>',
          $color, dataFromPgToBr($falta['data_falta_atraso']),
          $color, $falta['tipo'] == 1 ? 'Atraso' : 'Falta',
          $color, $falta['qtd_horas'],
          $color, $falta['qtd_min'],
          $color, $det_esc['nm_escola'],
          $color, $det_ins['nm_instituicao']);

        $cont++;
      }

      $tabela .= $corpo;
      $tabela .= "</table>";

      if ($tabela) {
        $this->addDetalhe(array('Faltas/Atrasos', $tabela));
      }
    }

    $obj_permissoes = new clsPermissoes();

    if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
      $this->caption_novo = 'Compensar';
      $this->url_editar   = FALSE;
      $this->url_novo     = sprintf(
        'educar_falta_atraso_compensado_cad.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d',
        $this->ref_cod_servidor, $this->ref_cod_escola, $this->ref_ref_cod_instituicao
      );
    }

    $this->url_cancelar = sprintf(
      "educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d",
      $this->ref_cod_servidor, $this->ref_ref_cod_instituicao
    );

    $this->largura = '100%';
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