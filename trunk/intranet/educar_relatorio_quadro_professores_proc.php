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

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="pt">
<head>
  <title><!-- #&TITULO&# --></title>
  <link rel=stylesheet type='text/css' href='styles/styles.css' />
  <link rel=stylesheet type='text/css' href='styles/novo.css' />
  <link rel=stylesheet type='text/css' href='styles/menu.css' />
  <!-- #&ESTILO&# -->

  <script type='text/javascript' src='scripts/padrao.js'></script>
  <script type='text/javascript' src='scripts/novo.js'></script>
  <script type='text/javascript' src='scripts/dom.js'></script>
  <script type='text/javascript' src='scripts/menu.js'></script>
  <!-- #&SCRIPT&# -->

  <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
  <meta http-equiv='Pragma' content='no-cache' />
  <meta http-equiv='Expires' content='-1' />
  <!-- #&REFRESH&# -->
</head>
<body onload="parent.EscondeDiv('LoadImprimir');">
<?php
require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/relatorio.inc.php';
require_once 'include/pmieducar/geral.inc.php';

$ref_cod_instituicao = $_GET['ref_cod_instituicao'];
$ref_cod_escola      = $_GET['ref_cod_escola'];
$professor           = $_GET['professor'] ? TRUE : NULL;

$config = $coreExt['Config']->app->template->pdf;

if ($ref_cod_escola) {
  $obj_servidor = new clsPmieducarServidorAlocacao ();
  $obj_servidor->setCamposLista('ref_ref_cod_instituicao, ref_cod_escola, sa.ref_cod_servidor, SUM(carga_horaria) AS carga_horaria');
  $obj_servidor->setOrderby('sa.ref_ref_cod_instituicao, sa.ref_cod_escola, p.nome, sa.ref_cod_servidor');
  $obj_servidor->setGroupBy('ref_ref_cod_instituicao, ref_cod_escola, sa.ref_cod_servidor, p.nome');
  $lst_servidor = $obj_servidor->lista(NULL, $ref_cod_instituicao, NULL, NULL,
    $ref_cod_escola, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, TRUE, $professor);

  if (is_array($lst_servidor)) {
    $total_servidor = count($lst_servidor);

    $relatorio = new relatorios('RELAÇÃO DO QUADRO DE PROFESSORES 1  -                Total de Funcionário/Professores = ' . $total_servidor,
      120, FALSE, 'i-Educar', 'A4', $config->get($config->titulo, 'i-Educar'),
      '#515151');

    $relatorio->exibe_produzido_por = FALSE;

    $get_nome_escola = new clsPmieducarEscola($ref_cod_escola);
    $det_nome_escola = $get_nome_escola->detalhe();

    if (is_array($det_nome_escola)) {
      $relatorio->novalinha(array($det_nome_escola['nome']), 0, 16, TRUE,
        'arial', array(), '#515151', '#D3D3D3', '#FFFFFF', FALSE, TRUE);
    }

    $relatorio->novalinha(array('Nome', 'Matrícula', 'Turno', 'Carga Horária Disponível'),
      0, 16, TRUE, 'arial', array(210, 90, 100), '#515151', '#D3D3D3', '#FFFFFF',
      FALSE, TRUE);

    $array_turnos = array(
      '1' => 'M',
      '2' => 'V',
      '3' => 'N'
    );

    $cor = '#FFFFFF';

    foreach ($lst_servidor as $servidor) {
      $get_turnos = new clsPmieducarServidorAlocacao();
      $get_turnos->setCamposLista('periodo');
      $get_turnos->setGroupBy('periodo, p.nome');

      $turnos = $get_turnos->lista(NULL, $ref_cod_instituicao, NULL, NULL,
        $ref_cod_escola, $servidor['ref_cod_servidor'], NULL, NULL, NULL,
        NULL, 1, NULL, NULL, TRUE);

      $turnos_txt = '';

      if (is_array($turnos)) {
        $completar = '';
        foreach ($turnos as $turno) {
          $turnos_txt .= $completar . $array_turnos[$turno['periodo']];
          $completar   = '/';
        }
      }

      $sql = '
        SELECT
          nm_funcao
        FROM
          pmieducar.servidor_funcao,
          pmieducar.funcao
        WHERE
          ref_cod_funcao = cod_funcao AND
          ref_cod_servidor = ' . $servidor['ref_cod_servidor'];
      $db = new clsBanco();

      $nm_funcao = $db->CampoUnico($sql);
      $cor = $cor == '#FFFFFF' ? '#D3D3D3' : '#FFFFFF';

      $relatorio->novalinha(array(minimiza_capitaliza($servidor['nome']),
        $servidor['ref_cod_servidor'], $turnos_txt, $servidor['carga_horaria']),
        5, 17, FALSE, 'arial', array(215, 90, 100));

      if (!empty($nm_funcao)) {
        $relatorio->novalinha(array('Função: ' . $nm_funcao) , 20, 17, FALSE,
          'arial', array(300));
      }
    }

    // Pega o link e exibe ele ao usuário
    $link = $relatorio->fechaPdf();

    echo sprintf('
      <script>
        window.onload = function()
        {
          parent.EscondeDiv("LoadImprimir");
          window.location="download.php?filename=%s"
        }
      </script>', $link);

    echo sprintf('
      <html>
        <center>
          Se o download não iniciar automaticamente <br /><a target="blank" href="%s" style="font-size: 16px; color: #000000; text-decoration: underline;">clique aqui!</a><br><br>
          <span style="font-size: 10px;">Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>
            Clique na Imagem para Baixar o instalador<br><br>
            <a href="http://www.adobe.com.br/products/acrobat/readstep2.html" target="new"><br><img src="imagens/acrobat.gif" width="88" height="31" border="0"></a>
          </span>
        </center>
      </html>', $link);
  }
  else {
    echo '<center>Não existem servidores alocados na escola selecionada!</center>';
  }
}
else {
  $entrou = FALSE;

  $get_escolas = new clsPmieducarServidorAlocacao();
  $lst_escolas = $get_escolas->listaEscolas($ref_cod_instituicao);

  if (is_array($lst_escolas)) {
    $relatorio = new relatorios('RELAÇÃO DO QUADRO DE PROFESSORES', 120, FALSE,
      'i-Educar', 'A4', $config->get($config->titulo, 'i-Educar'),
      '#515151');

    $relatorio->exibe_produzido_por = FALSE;

    foreach ($lst_escolas as $escolas) {
      $obj_servidor = new clsPmieducarServidorAlocacao ();
      $obj_servidor->setCamposLista('ref_ref_cod_instituicao, ref_cod_escola, sa.ref_cod_servidor, SUM(carga_horaria) AS carga_horaria');
      $obj_servidor->setOrderby('sa.ref_ref_cod_instituicao, sa.ref_cod_escola, p.nome, sa.ref_cod_servidor');
      $obj_servidor->setGroupBy('ref_ref_cod_instituicao, ref_cod_escola, sa.ref_cod_servidor, p.nome');
      $lst_servidor = $obj_servidor->lista(NULL, $ref_cod_instituicao, NULL,
        NULL, $escolas['ref_cod_escola'], NULL, NULL, NULL, NULL, NULL, 1,
        NULL, NULL, TRUE);

      if (is_array($lst_servidor)) {
        $get_nome_escola = new clsPmieducarEscola($escolas['ref_cod_escola']);
        $det_nome_escola = $get_nome_escola->detalhe();

        if (is_array($det_nome_escola)) {
          $total_servidor = count($lst_servidor);
          $relatorio->novalinha (array($det_nome_escola['nome'] . ' - Total de Professores: ' . $total_servidor),
            0, 16, TRUE, 'arial', array(), '#515151', '#d3d3d3', '#FFFFFF',
            FALSE, TRUE);
        }

        $relatorio->novalinha(array('Nome', 'Matrícula', 'Turno', 'Carga Horária Disponível'),
          0, 16, TRUE, 'arial', array(210, 90, 100), '#515151', '#d3d3d3',
          '#FFFFFF', FALSE, TRUE);

        $array_turnos = array(
          '1' => 'M',
          '2' => 'V',
          '3' => 'N'
        );

        foreach ($lst_servidor as $servidor) {
          $get_turnos = new clsPmieducarServidorAlocacao();
          $get_turnos->setCamposLista('periodo');
          $get_turnos->setGroupBy('periodo, p.nome');
          $turnos = $get_turnos->lista (NULL, $ref_cod_instituicao, NULL,
            NULL, $escolas['ref_cod_escola'], $servidor['ref_cod_servidor'],
            NULL, NULL, NULL, NULL, 1, NULL, NULL, TRUE);

          $turnos_txt = '';

          if (is_array($turnos)) {
            $completar = '';
            foreach ($turnos as $turno) {
              $turnos_txt .= $completar . $array_turnos[$turno['periodo']];
              $completar = '/';
            }
          }

          $relatorio->novalinha(
            array(
              minimiza_capitaliza($servidor['nome']),
              $servidor['ref_cod_servidor'],
              $turnos_txt, $servidor['carga_horaria']
            ),
            5, 17, FALSE, 'arial', array(215, 90, 100));
        }

        $entrou = TRUE;
      }
    }
  }

  // Pega o link e exibe ele ao usuário
  $link = $relatorio->fechaPdf();

  if ($entrou) {
    echo sprintf('
      <script>
        window.onload = function()
        {
          parent.EscondeDiv("LoadImprimir");
          window.location="download.php?filename=%s"
        }
      </script>', $link);

    echo sprintf('
      <html>
        <center>
          Se o download não iniciar automaticamente <br /><a target="blank" href="%s" style="font-size: 16px; color: #000000; text-decoration: underline;">clique aqui!</a><br><br>
          <span style="font-size: 10px;">Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>
            Clique na Imagem para Baixar o instalador<br><br>
            <a href="http://www.adobe.com.br/products/acrobat/readstep2.html" target="new"><br><img src="imagens/acrobat.gif" width="88" height="31" border="0"></a>
          </span>
        </center>
      </html>', $link);
  }
  else {
    echo '<center>Não existem servidores cadastrados.</center>';
  }
}
?>
</body>
</html>