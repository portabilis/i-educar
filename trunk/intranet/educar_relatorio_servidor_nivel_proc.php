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
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/relatorio.inc.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Relatório Servidores por Nível');
    $this->processoAp = 831;
    $this->renderMenu = FALSE;
    $this->renderMenuSuspenso = FALSE;
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
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $ref_cod_instituicao;
  var $ref_cod_escola;

  var $get_link;

  /**
   * @global $coreExt
   */
  function renderHTML()
  {
    global $coreExt;
    $config = $coreExt['Config']->app->template->pdf;

    if ($_POST) {
      foreach ($_POST as $key => $value) {
        $this->$key = $value;
      }
    }

    $fonte    = 'arial';
    $corTexto = '#000000';

    if (empty($this->ref_cod_instituicao)) {
      echo '
      <script>
        alert("Erro ao gerar relatório!\nNenhuma instituição selecionada!");
        window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
      </script>';
      return TRUE;
    }

    $obj_instituicao = new clsPmieducarInstituicao($this->ref_cod_instituicao);
    $det_instituicao = $obj_instituicao->detalhe();
    $this->nm_instituicao = $det_instituicao['nm_instituicao'];

    if ($this->ref_cod_escola) {
      $obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
      $det_escola = $obj_escola->detalhe();
      $this->nm_escola = $det_escola['nome'];
    }

    if ($this->ref_cod_escola) {
      $sql = sprintf('
        SELECT
          s.cod_servidor,
          p.nome,
          sn.nm_subnivel,
          sn.salario,
          n.nm_nivel
        FROM
          pmieducar.servidor s,
          pmieducar.subnivel sn,
          pmieducar.nivel n,
          cadastro.pessoa p,
          pmieducar.servidor_alocacao a
        WHERE
          s.cod_servidor = p.idpes
          AND s.ref_cod_subnivel IS NOT NULL
          AND s.ref_cod_subnivel = sn.cod_subnivel
          AND sn.ref_cod_nivel = n.cod_nivel
          AND s.ref_cod_instituicao = %d
          AND a.ref_cod_servidor = s.cod_servidor
          AND a.ref_cod_escola = %d
          %s
          AND s.ativo = 1
        ORDER BY
          p.nome', $this->ref_cod_instituicao, $this->ref_cod_escola, $where);
    }
    else {
      $sql = sprintf('
        SELECT
          s.cod_servidor,
          p.nome,
          sn.nm_subnivel,
          sn.salario,
          n.nm_nivel
        FROM
          pmieducar.servidor s,
          pmieducar.subnivel sn,
          pmieducar.nivel n,
          cadastro.pessoa p
        WHERE
          s.cod_servidor = p.idpes
          AND s.ref_cod_subnivel IS NOT NULL
          AND s.ref_cod_subnivel = sn.cod_subnivel
          AND sn.ref_cod_nivel = n.cod_nivel
          AND s.ref_cod_instituicao = %d
          %s
          AND s.ativo  = 1
        ORDER BY
          p.nome', $this->ref_cod_instituicao, $where);
    }

    $db = new clsBanco();
    $db->Consulta($sql);

    if ($db->Num_Linhas()) {
      $relatorio = new relatorios('Servidores por Nível', 210, FALSE,
        'Servidores por Nível', 'A4', $config->get($config->titulo, 'i-Educar') .
        "\n" . $this->nm_escola);

      $relatorio->setMargem(20, 20, 50, 50);
      $relatorio->exibe_produzido_por = FALSE;

      $relatorio->novalinha(array('Nome', 'Nível', 'Salário'), 0, 16, TRUE,
        'arial', array(75, 320, 100), '#515151', '#d3d3d3', '#FFFFFF', FALSE, TRUE);

      while ($db->ProximoRegistro()) {
        list($cod_servidor, $nome, $subnivel, $salario, $nivel) = $db->Tupla();
        $relatorio->novalinha(array($nome, "{$nivel{$subnivel}}",
          $salario), 0, 16, FALSE, 'arial', array(75, 330, 80), '#515151',
          '#d3d3d3', '#FFFFFF', FALSE, FALSE);
      }

      $this->get_link = $relatorio->fechaPdf();

      echo sprintf('
        <script>
          window.onload = function()
          {
            parent.EscondeDiv("LoadImprimir");
            window.location="download.php?filename=%s"
          }
        </script>', $this->get_link);

      echo sprintf('
        <html>
          <center>
            Se o download não iniciar automaticamente <br /><a target="blank" href="%s" style="font-size: 16px; color: #000000; text-decoration: underline;">clique aqui!</a><br><br>
            <span style="font-size: 10px;">Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>
              Clique na Imagem para Baixar o instalador<br><br>
              <a href="http://www.adobe.com.br/products/acrobat/readstep2.html" target="new"><br><img src="imagens/acrobat.gif" width="88" height="31" border="0"></a>
            </span>
          </center>
        </html>', $this->get_link);
    }
    else {
      echo '
        <script>
          window.onload = function()
          {
            parent.EscondeDiv("LoadImprimir");
          }
        </script>
        <center>Nenhum servidor cadastrado ou categorizado em níveis.</center>';
    }
  }

  function Editar()
  {
    return FALSE;
  }

  function Excluir()
  {
    return FALSE;
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