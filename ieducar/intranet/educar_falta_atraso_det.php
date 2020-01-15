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

use App\Models\LegacySchool;

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
    $this->SetTitulo($this->_instituicao . ' Servidores - Falta Atraso');
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
    $this->titulo = 'Falta Atraso - Detalhe';


    $this->ref_cod_servidor        = $_GET['ref_cod_servidor'];
    $this->ref_cod_escola          = $_GET['ref_cod_escola'];
    $this->ref_ref_cod_instituicao = $_GET['ref_cod_instituicao'];

    $tmp_obj = new clsPmieducarFaltaAtraso();
    $tmp_obj->setOrderby('data_falta_atraso DESC');
    $this->cod_falta_atraso = $_GET['cod_falta_atraso'];
    $registro = $tmp_obj->lista($this->cod_falta_atraso);

    if (!$registro) {
        $this->simpleRedirect(sprintf(
            'educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
            $this->ref_cod_servidor, $this->ref_ref_cod_instituicao));
    }
    else {
      $tabela = '<table>
                 <tr align=center>
                     <td bgcolor="#ccdce6"><b>Dia</b></td>
                     <td bgcolor="#ccdce6"><b>Tipo</b></td>
                     <td bgcolor="#ccdce6"><b>Qtd. Horas</b></td>
                     <td bgcolor="#ccdce6"><b>Qtd. Minutos</b></td>
                     <td bgcolor="#ccdce6"><b>Escola</b></td>
                     <td bgcolor="#ccdce6"><b>Instituição</b></td>
                 </tr>';

      $cont  = 0;
      $total = 0;

      foreach ($registro as $falta) {
        if (($cont % 2) == 0) {
          $color = ' bgcolor="#f5f9fd" ';
        }
        else {
          $color = ' bgcolor="#FFFFFF" ';
        }

        $school = LegacySchool::query()->with('person')->find($falta['ref_cod_escola']);

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
          $color, $school->person->name ?? null,
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
      $this->url_novo     = sprintf(
        'educar_falta_atraso_compensado_cad.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d',
        $this->ref_cod_servidor, $this->ref_cod_escola, $this->ref_ref_cod_instituicao
      );
      $this->url_editar   = sprintf(
        'educar_falta_atraso_cad.php?ref_cod_servidor=%d&ref_cod_escola=%d&ref_cod_instituicao=%d&cod_falta_atraso=%d',
        $this->ref_cod_servidor, $this->ref_cod_escola, $this->ref_ref_cod_instituicao, $this->cod_falta_atraso
      );
    }

    $this->url_cancelar = sprintf(
      "educar_falta_atraso_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d",
      $this->ref_cod_servidor, $this->ref_ref_cod_instituicao
    );

    $this->largura = '100%';

    $this->breadcrumb('Detalhe da falta/atraso do servidor', [
        url('intranet/educar_servidores_index.php') => 'Servidores',
    ]);
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
