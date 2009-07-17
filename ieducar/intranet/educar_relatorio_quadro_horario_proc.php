<?php

/*
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
 */

/**
 * Quadro de horários.
 *
 * @author      Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  Relatório
 * @since       Arquivo disponível desde a versão 1.0.0
 * @version     $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/clsPDF.inc.php';


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

class indice extends clsCadastro
{


  /**
   * Referencia pega da session para o idpes do usuario atual
   *
   * @var int
   */
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

  var $get_link = false;

  var $pdf = false;

  var $page_y = 15;

  var $meses_do_ano = array(
               "1" => "JANEIRO"
              ,"2" => "FEVEREIRO"
              ,"3" => "MAR&Ccedil;O"
              ,"4" => "ABRIL"
              ,"5" => "MAIO"
              ,"6" => "JUNHO"
              ,"7" => "JULHO"
              ,"8" => "AGOSTO"
              ,"9" => "SETEMBRO"
              ,"10" => "OUTUBRO"
              ,"11" => "NOVEMBRO"
              ,"12" => "DEZEMBRO"
            );

  var $array_dias_semana = array(
               "1" => "Domingo"
              ,"2" => "Segunda"
              ,"3" => "Terça"
              ,"4" => "Quarta"
              ,"5" => "Quinta"
              ,"6" => "Sexta"
              ,"7" => "Sábado"
              );



  function renderHTML()
  {

    if($_POST){
      foreach ($_POST as $key => $value) {
        $this->$key = $value;

      }
    }

    if($this->ref_ref_cod_serie)
      $this->ref_cod_serie = $this->ref_ref_cod_serie;

    if($this->ref_cod_escola){

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
    $obj_curso->setOrderby("nm_curso");
    $lst_curso = $obj_curso->lista($this->ref_cod_curso,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,1,null,$this->ref_cod_instituicao);


    if($lst_curso)
    {

      foreach ($lst_curso as $curso)
      {
        $obj_serie = new clsPmieducarSerie();
        $obj_serie->setOrderby("nm_serie");
        $lst_serie = $obj_serie->lista($this->ref_cod_serie,null,null,$curso['cod_curso'],null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao,null,null,null,$this->ref_cod_escola);

        $quadro_horario = 0;
        if($lst_serie)
        {
          foreach ($lst_serie as $serie)
          {

            $obj_turma = new clsPmieducarTurma();
            $obj_turma->setOrderby("nm_turma");
            $lst_turma = $obj_turma->lista($this->ref_cod_turma,null,null,$serie['cod_serie'],$this->ref_cod_escola,null,null,null,null,null,null,null,null,null,1,null,null,null,null,null,null,null,null,null,$curso['cod_curso'],$this->ref_cod_instituicao);

            if($lst_turma)
            {
              foreach ($lst_turma as $turma)
              {

                $obj_quadro = new clsPmieducarQuadroHorario( null, null, null, $turma['cod_turma'], null, null, 1 );
                $det_quadro = $obj_quadro->detalhe();

                if($det_quadro)
                {
                  if(!$this->pdf)
                  {
                    $this->pdf = new clsPDF("Quadro Horarios", "Quadro Horarios", "A4", "", false, false);
                  }

                  if($quadro_horario % 3 == 0)
                  {
                    //$this->pdf->largura  = 842.0;
                      //$this->pdf->altura = 595.0;
                      $this->page_y = 15;
                      $this->pdf->OpenPage();
                      $this->addCabecalho();
                      $quadro_horario = 0;
                  }




                  $this->pdf->escreve_relativo("{$turma['nm_turma']} - {$serie['nm_serie']}",20 ,$this->page_y - 7,550,20,$fonte,11,$corTexto,'center');
                  $this->page_y +=10;

                  $this->pdf->quadrado_relativo(35,$this->page_y,525,20,0.3,"#777777","#777777");
                  $inicio_x = 35;

                  for ($dia_semana = 1;$dia_semana <= 7;$dia_semana++)
                  {
                    $this->pdf->linha_relativa($inicio_x,$this->page_y,0,20);
                    $this->pdf->escreve_relativo($this->array_dias_semana[$dia_semana],$inicio_x ,$this->page_y+3,75,20,$fonte,11,$corTexto,'center');
                    $inicio_x+=75;
                  }

                  $this->page_y += 20;

                  $inicio_y = $this->page_y;

                  $inicio_x = 35;
                  $this->pdf->quadrado_relativo($inicio_x,$this->page_y,525,200,0.3);
                  for ($dia_semana = 1;$dia_semana <= 7;$dia_semana++)
                  {

                    $obj_horarios = new clsPmieducarQuadroHorarioHorarios();
                    $resultado    = $obj_horarios->retornaHorario( $this->ref_cod_instituicao, $this->ref_cod_escola, $serie['cod_serie'], $turma['cod_turma'], $dia_semana );

                    if ( is_array( $resultado ) )
                    {
                      foreach ( $resultado as $registro )
                      {
                        $this->pdf->quadrado_relativo($inicio_x,$this->page_y,75,50,0.3);
                        $obj_disciplina = new clsPmieducarDisciplina( $registro["ref_cod_disciplina"] );
                        $det_disciplina = $obj_disciplina->detalhe();
                        $obj_servidor   = new clsPmieducarServidor();
                        $det_servidor   = array_shift($obj_servidor->lista($registro['ref_servidor'],null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,true));
                        $det_servidor['nome'] = array_shift(explode(' ',$det_servidor['nome']));

                        $this->pdf->escreve_relativo(substr( $registro["hora_inicial"], 0, 5 )." - ".substr( $registro["hora_final"], 0, 5 )." \n {$det_disciplina["abreviatura"]} \n {$det_servidor["nome"]}",$inicio_x,$this->page_y+12,75,50,$fonte,10,$corTexto,'center');

                        //substr( $registro["hora_inicial"], 0, 5 )." - ".substr( $registro["hora_final"], 0, 5 )." <br> {$det_disciplina["abreviatura"]} <br> {$det_servidor["nome"]}
                        $this->page_y += 50;
                      }
                    }

                    //$this->pdf->linha_relativa($inicio_x,$this->page_y,0,20);
                    //$this->pdf->escreve_relativo($this->array_dias_semana[$dia_semana],$inicio_x ,$this->page_y+3,75,20,$fonte,11,$corTexto,'center');

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

    if ($this->pdf)
    {

      $this->pdf->CloseFile();
      $this->get_link = $this->pdf->GetLink();

    }
    else
    {

          echo '<script>
              alert("As turmas não possuem matrículas no ano selecionado");
              window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
            </script>';

            return;

    }


    echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=".$this->get_link."'}</script>";

    echo "<html><center>Se o download não iniciar automaticamente <br /><a target='blank' href='" . $this->get_link  . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>clique aqui!</a><br><br>
      <span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>

      Clique na Imagem para Baixar o instalador<br><br>
      <a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
      </span>
      </center>";
  }

  public function addCabecalho()
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
    $this->pdf->escreve_relativo("Instituição:  $this->nm_instituicao", 110,
      $this->page_y + 38, 400, 80, $fonte, 10, $corTexto, 'left');

    $this->nm_escola ?
      $this->pdf->escreve_relativo( "Escola:  {$this->nm_escola}", 127, $this->page_y + 48, 300, 80, $fonte, 10, $corTexto, 'left')
      :
      NULL;

    // Título
    $this->pdf->escreve_relativo("Quadro de Horários - $this->ano", 30,
      $this->page_y + 23, 535, 80, $fonte, 12, $corTexto, 'center');

    $this->pdf->escreve_relativo("Data de Emissão: ".date("d/m/Y"), 700,
      $this->page_y + 50, 535, 80, $fonte, 8, $corTexto, 'left');

    $this->page_y += 80;
  }


  function rodape()
  {
    $corTexto = '#000000';
    $fonte = 'arial';
    $dataAtual = date("d/m/Y");
    $this->pdf->escreve_relativo( "Data: $dataAtual", 36,$this->page_y + 2, 100, 50, $fonte, 7, $corTexto, 'left' );

    $this->pdf->escreve_relativo( "Estou ciente do aproveitamento de ".str2upper($this->nm_aluno).", matrícula nº: $this->ref_cod_matricula.", 68,$this->page_y +12, 600, 50, $fonte, 9, $corTexto, 'left' );
    $this->pdf->escreve_relativo( "Assinatura do Responsável(a)", 677,$this->page_y +18, 200, 50, $fonte, 7, $corTexto, 'left' );
    $this->pdf->linha_relativa(660,$this->page_y+18,130,0,0.4);
  }

  function Editar()
  {
    return false;
  }

  function Excluir()
  {
    return false;
  }
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();


?>
<script>
