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
require_once 'include/clsPDF.inc.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';

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
    $this->SetTitulo( "{$this->_instituicao} i-Educar - Quadro Hor&aacute;rios" );
    $this->processoAp = "835";
    $this->renderMenu = false;
    $this->renderMenuSuspenso = false;
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
  var $ref_cod_serie;
  var $ref_cod_turma;
  var $ref_cod_curso;

  var $ano;
  var $mes;

  var $nm_escola;
  var $nm_instituicao;
  var $nm_professor;
  var $nm_turma;
  var $nm_serie;
  var $nm_disciplina;

  var $get_link = FALSE;
  var $pdf      = FALSE;
  var $page_y   = 15;

  var $array_dias_semana = array(
    1 => 'Domingo',
    2 => 'Segunda',
    3 => 'Terça',
    4 => 'Quarta',
    5 => 'Quinta',
    6 => 'Sexta',
    7 => 'Sábado'
  );

  function renderHTML()
  {
    if ($_POST){
      foreach ($_POST as $key => $value) {
        $this->$key = $value;
      }
    }

    if ($this->ref_ref_cod_serie) {
      $this->ref_cod_serie = $this->ref_ref_cod_serie;
    }

    if ($this->ref_cod_escola) {
      $obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
      $det_escola = $obj_escola->detalhe();
      $this->nm_escola = $det_escola['nome'];
    }

    $obj_instituicao = new clsPmieducarInstituicao($this->ref_cod_instituicao);
    $det_instituicao = $obj_instituicao->detalhe();
    $this->nm_instituicao = $det_instituicao['nm_instituicao'];

    $fonte = 'arial';
    $corTexto = '#000000';

    $obj_curso = new clsPmieducarCurso();
    $obj_curso->setOrderby('nm_curso');
    $lst_curso = $obj_curso->lista($this->ref_cod_curso, NULL, NULL, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, 1, NULL, $this->ref_cod_instituicao);

    if ($lst_curso) {
      foreach ($lst_curso as $curso) {
        $obj_serie = new clsPmieducarSerie();
        $obj_serie->setOrderby('nm_serie');
        $lst_serie = $obj_serie->lista($this->ref_cod_serie, NULL, NULL,
          $curso['cod_curso'], NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
          1, $this->ref_cod_instituicao, NULL, NULL, NULL, $this->ref_cod_escola);

        $quadro_horario = 0;
        if ($lst_serie) {
          foreach ($lst_serie as $serie) {
            $obj_turma = new clsPmieducarTurma();
            $obj_turma->setOrderby('nm_turma');
            $lst_turma = $obj_turma->lista($this->ref_cod_turma, NULL, NULL,
              $serie['cod_serie'], $this->ref_cod_escola, NULL, NULL, NULL, NULL,
              NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL,
              NULL, NULL, NULL, NULL, $curso['cod_curso'], $this->ref_cod_instituicao);

            if ($lst_turma) {
              foreach ($lst_turma as $turma) {
                $obj_quadro = new clsPmieducarQuadroHorario(NULL, NULL, NULL,
                  $turma['cod_turma'],  NULL,  NULL, 1);
                $det_quadro = $obj_quadro->detalhe();

                if ($det_quadro) {
                  if (!$this->pdf) {
                    $this->pdf = new clsPDF('Quadro Horarios', 'Quadro Horarios',
                      'A4', '', FALSE, FALSE);
                  }

                  if ($quadro_horario % 3 == 0) {
                    $this->page_y = 15;
                    $this->pdf->OpenPage();
                    $this->addCabecalho();
                    $quadro_horario = 0;
                  }

                  $this->pdf->escreve_relativo($turma['nm_turma'] . ' -  ' . $serie['nm_serie'],
                    20, $this->page_y - 7, 550, 20, $fonte, 11, $corTexto, 'center');

                  $this->page_y +=10;

                  $this->pdf->quadrado_relativo(35, $this->page_y, 525, 20, 0.3,
                    '#777777', '#777777');
                  $inicio_x = 35;

                  for ($dia_semana = 1; $dia_semana <= 7; $dia_semana++) {
                    $this->pdf->linha_relativa($inicio_x, $this->page_y, 0, 20);
                    $this->pdf->escreve_relativo($this->array_dias_semana[$dia_semana],
                      $inicio_x, $this->page_y + 3, 75, 20, $fonte, 11, $corTexto,
                      'center');

                    $inicio_x += 75;
                  }

                  $this->page_y += 20;

                  $inicio_y = $this->page_y;

                  $inicio_x = 35;
                  $this->pdf->quadrado_relativo($inicio_x, $this->page_y, 525, 200, 0.3);
                  for ($dia_semana = 1; $dia_semana <= 7; $dia_semana++) {
                    $obj_horarios = new clsPmieducarQuadroHorarioHorarios();
                    $resultado    = $obj_horarios->retornaHorario(
                      $this->ref_cod_instituicao, $this->ref_cod_escola,
                      $serie['cod_serie'], $turma['cod_turma'], $dia_semana);

                    if (is_array($resultado)) {
                      foreach ($resultado as $registro) {
                        $this->pdf->quadrado_relativo($inicio_x, $this->page_y,
                          75, 50, 0.3);

                        $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();
                        $componente = $componenteMapper->find($registro['ref_cod_disciplina']);

                        $obj_servidor = new clsPmieducarServidor();
                        $det_servidor = array_shift($obj_servidor->lista(
                          $registro['ref_servidor'], NULL, NULL, NULL, NULL,
                          NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
                          NULL, NULL, TRUE));

                        $det_servidor['nome'] = array_shift(explode(' ', $det_servidor['nome']));

                        $texto = sprintf("%s - %s\n%s\n%s",
                          substr($registro['hora_inicial'], 0, 5),
                          substr($registro["hora_final"], 0, 5),
                          $componente->abreviatura,
                          $det_servidor['nome']
                        );

                        $this->pdf->escreve_relativo(
                          $texto, $inicio_x, $this->page_y + 12, 75, 50, $fonte,
                          10, $corTexto, 'center');

                        $this->page_y += 50;
                      }
                    }

                    $inicio_x+=75;
                    $this->page_y = $inicio_y;
                  }

                  $this->page_y += 220;
                }

                $quadro_horario++;
              }
            }
          }
        }
      }
    }

    if ($this->pdf) {
      $this->pdf->CloseFile();
      $this->get_link = $this->pdf->GetLink();
    }
    else {
      echo '
        <script>
          alert("As turmas não possuem matrículas no ano selecionado");
          window.parent.fechaExpansivel("div_dinamico_" + (window.parent.DOM_divs.length-1));
        </script>';

       return;
    }

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

  function addCabecalho()
  {
    /**
     * Variável global com objetos do CoreExt.
     * @see includes/bootstrap.php
     */
    global $coreExt;

    // Namespace de configuração do template PDF
    $config = $coreExt['Config']->app->template->pdf;

    // Variável que controla a altura atual das caixas
    $altura   = 10;
    $fonte    = 'arial';
    $corTexto = '#000000';
    $espessura_linha = 0.5;

    // Cabeçalho
    $logo = $config->get($config->logo, 'imagens/brasao.gif');

    $this->pdf->quadrado_relativo( 30, $this->page_y, 535, 65,$espessura_linha );
    $this->pdf->insertImageScaled('gif', $logo, 50, $this->page_y + 52, 41);

    // Título principal
    $titulo = $config->get($config->titulo, 'i-Educar');
    $this->pdf->escreve_relativo($titulo, 30, $this->page_y + 2, 535, 80,
      $fonte, 18, $corTexto, 'center');

    // Dados escola
    $this->pdf->escreve_relativo('Instituição:  ' . $this->nm_instituicao, 110,
      $this->page_y + 38, 400, 80, $fonte, 10, $corTexto, 'left');

    $this->nm_escola ?
      $this->pdf->escreve_relativo('Escola: ' . $this->nm_escola, 127, $this->page_y + 48, 300, 80, $fonte, 10, $corTexto, 'left')
      :
      NULL;

    // Título
    $this->pdf->escreve_relativo('Quadro de Horários - ' . $this->ano, 30,
      $this->page_y + 23, 535, 80, $fonte, 12, $corTexto, 'center');

    $this->pdf->escreve_relativo('Data de Emissão: ' . date('d/m/Y'), 700,
      $this->page_y + 50, 535, 80, $fonte, 8, $corTexto, 'left');

    $this->page_y += 80;
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

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();