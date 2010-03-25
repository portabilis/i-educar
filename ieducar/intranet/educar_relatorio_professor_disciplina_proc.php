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
    $this->SetTitulo('i-Educar - Relação Professores Disciplinas');
    $this->processoAp = 827;
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
  var $ref_cod_curso;
  var $ref_cod_disciplina;

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

    if ($this->ref_ref_cod_serie) {
      $this->ref_cod_serie = $this->ref_ref_cod_serie;
    }

    $fonte = 'arial';
    $corTexto = '#000000';

    $obj_instituicao = new clsPmieducarInstituicao($det_escola['ref_cod_instituicao']);
    $det_instituicao = $obj_instituicao->detalhe();
    $this->nm_instituicao = $det_instituicao['nm_instituicao'];

    if ($this->ref_cod_escola) {
      $obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
      $det_escola = $obj_escola->detalhe();
      $this->nm_escola = $det_escola['nome'];
    }

    $obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
    $det_curso = $obj_curso->detalhe();
    $this->nm_curso = $det_curso['nm_curso'];

    if ($this->ref_cod_disciplina) {
      $where = ' AND mcc.id = ' . $this->ref_cod_disciplina;
    }

    if ($this->ref_cod_escola) {
      $sql = sprintf('
        SELECT
          DISTINCT(cod_servidor_alocacao),
          cod_servidor,
          cp.nome,
          sa.carga_horaria,
          CASE periodo
            WHEN 1 THEN \'Matutino\'
            WHEN 2 THEN \'Vespertino\'
            ELSE \'Noturno\'
          END AS turno,
          mcc.nome as nm_disciplina
        FROM
          pmieducar.servidor s,
          pmieducar.servidor_disciplina sd,
          pmieducar.servidor_alocacao sa,
          modules.componente_curricular mcc,
          cadastro.pessoa cp
        WHERE
          cod_servidor = sd.ref_cod_servidor
          AND cod_servidor = sa.ref_cod_servidor
          AND ref_cod_instituicao = sd.ref_ref_cod_instituicao
          AND ref_cod_instituicao = sa.ref_ref_cod_instituicao
          AND mcc.id = ref_cod_disciplina
          AND cod_servidor = idpes
          AND ref_cod_instituicao = \'%d\'
          AND ref_cod_escola = \'%d\'
          %s
          AND sd.ref_cod_curso = \'%d\'
          AND sa.ativo = 1
          AND s.ativo  = 1
        ORDER BY
          nome, nm_disciplina', $this->ref_cod_instituicao, $this->ref_cod_escola, $where, $this->ref_cod_curso);
    }
    else {
      $sql = sprintf('
        SELECT
          DISTINCT(cod_servidor_alocacao),
          cod_servidor,
          cp.nome,
          CAST(s.carga_horaria || \' hour\' AS interval) AS carga_horaria,
          mcc.nome as nm_disciplina,
          CASE periodo
            WHEN 1 THEN \'Matutino\'
            WHEN 2 THEN \'Vespertino\'
            WHEN 3 THEN \'Noturno\'
          END as turno
        FROM
          pmieducar.servidor s,
          pmieducar.servidor_disciplina sd,
          modules.componente_curricular mcc,
          cadastro.pessoa cp,
          pmieducar.servidor_alocacao sa
       WHERE
         cod_servidor = sd.ref_cod_servidor
         AND cod_servidor = idpes
         AND ref_cod_instituicao = sd.ref_ref_cod_instituicao
         AND mcc.id = ref_cod_disciplina
         AND ref_cod_instituicao = \'%d\'
         %s
         AND sd.ref_cod_curso = \'%d\'
         AND s.ativo = 1
         AND cod_servidor = sa.ref_cod_servidor
       ORDER BY
         nome, nm_disciplina', $this->ref_cod_instituicao, $where, $this->ref_cod_curso);
    }

    $db = new clsBanco();
    $db->Consulta($sql);

    $nm_disciplina = NULL;

    if ($db->Num_Linhas()) {
      $relatorio = new relatorios('Professores por Disciplina', 210, FALSE,
        'Professores por Disciplina', 'A4',
        $config->get($config->titulo, 'i-Educar') . "\n{$this->nm_escola}\n{$this->nm_curso}");

      $relatorio->setMargem(20, 20, 50, 50);
      $relatorio->exibe_produzido_por = FALSE;

      while ($db->ProximoRegistro()) {
        $registro = $db->Tupla();
        if ($registro['nm_disciplina'] != $nm_disciplina) {
          $relatorio->novalinha(array($registro['nm_disciplina']), 0, 16, TRUE,
            'arial', array(75, 330, 100), '#515151', '#d3d3d3', '#FFFFFF', FALSE, FALSE);

          if ($this->ref_cod_escola) {
            $relatorio->novalinha(array('Matrícula', 'Nome', 'Carga Horária', 'Turno'),
              0, 16, TRUE, 'arial', array(75, 320, 100), '#515151', '#d3d3d3',
              '#FFFFFF', FALSE, TRUE);
          }
          else {
            $relatorio->novalinha(array('Matrícula', 'Nome', 'Carga Horária', 'Turno'),
              0, 16, TRUE, 'arial', array(75, 320, 100), '#515151', '#d3d3d3',
              '#FFFFFF', FALSE, TRUE);
          }

          $nm_disciplina = $registro['nm_disciplina'];
        }

        $relatorio->novalinha(array($registro['cod_servidor'], $registro['nome'],
          $registro['carga_horaria'], $registro['turno']), 0, 16, FALSE, 'arial',
          array(75, 330, 80), '#515151', '#d3d3d3', '#FFFFFF', FALSE, FALSE);
      }

      $this->get_link = $relatorio->fechaPdf();

      echo sprintf('
        <script>
          window.onload=function()
          {
            parent.EscondeDiv("LoadImprimir");
            window.location="download.php?filename=%s"
          }
        </script>', $this->get_link);

      echo sprintf('
        <html>
          <center>Se o download não iniciar automaticamente <br />
          <a target="blank" href="%s" style="font-size: 16px; color: #000000; text-decoration: underline;">clique aqui!</a><br /><br />
          <span style="font-size: 10px;">Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br />
            Clique na Imagem para Baixar o instalador<br /><br />
            <a href="http://www.adobe.com.br/products/acrobat/readstep2.html" target="new"><br><img src="imagens/acrobat.gif" width="88" height="31" border="0"></a>
          </span>
        </center>', $this->get_link);
    }
    else {
      if ($this->ref_cod_escola) {
        $msg = 'Nenhum professor ministra a disciplina ou está alocado na escola selecionada.';
      }
      else {
        $msg = 'Nenhum professor ministra a disciplina selecionada.';
      }
      echo
        '<script>
           window.onload=function()
           {
             parent.EscondeDiv("LoadImprimir");
           }
         </script>' .
        '<center>' . $msg . '</center>';
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