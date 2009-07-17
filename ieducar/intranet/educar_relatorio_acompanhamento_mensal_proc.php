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
 * Relatório de acompanhamento mensal.
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
    $this->SetTitulo( "{$this->_instituicao} i-Educar - Acompanhamento Mensal" );
    $this->processoAp = "824";
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

  var $ano;
  var $mes;
  var $mes_inicial;
  var $mes_final;

  var $nm_escola;
  var $nm_instituicao;
  var $ref_cod_curso;
  var $sequencial;
  var $pdf;
  var $pagina_atual = 1;
  var $total_paginas = 1;
  var $nm_professor;
  var $nm_turma;
  var $nm_serie;
  var $nm_disciplina;

  var $page_y = 125;

  var $get_file;

  var $cursos = array();

  var $get_link;

  var $total;

  var $ref_cod_modulo;
  var $data_ini,$data_fim;

  var $is_padrao;
  var $semestre;

  var $meses_do_ano = array(
               "1" => "JANEIRO"
              ,"2" => "FEVEREIRO"
              ,"3" => "MARÇO"
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



  function renderHTML()
  {

    if($_POST){
      foreach ($_POST as $key => $value) {
        $this->$key = $value;

      }
    }
    if($this->ref_ref_cod_serie)
      $this->ref_cod_serie = $this->ref_ref_cod_serie;

    $fonte = 'arial';
    $corTexto = '#000000';

    if(empty($this->ref_cod_turma))
    {
        echo '<script>
            alert("Erro ao gerar relatório!\nNenhuma turma selecionada!");
            window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
          </script>';
        return true;
    }

    $obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
    $det_escola = $obj_escola->detalhe();
    $this->nm_escola = $det_escola['nome'];

    $obj_instituicao = new clsPmieducarInstituicao($det_escola['ref_cod_instituicao']);
    $det_instituicao = $obj_instituicao->detalhe();
    $this->nm_instituicao = $det_instituicao['nm_instituicao'];


    $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
    $det_turma = $obj_turma->detalhe();
    $this->nm_turma = $det_turma['nm_turma'];

    $obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
    $det_serie = $obj_serie->detalhe();
    $this->nm_serie = $det_serie['nm_serie'];

       $obj_calendario = new clsPmieducarEscolaAnoLetivo();
       $lista_calendario = $obj_calendario->lista($this->ref_cod_escola,$this->ano,null,null,null,null,null,null,null,1,null);

       if(!$lista_calendario)
       {
        echo '<script>
            alert("Escola não possui calendário definido para este ano");
            window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
          </script>';
        return true;
       }

    $calendario = array_shift($lista_calendario);

     //Dias previstos do mes
       // Qual o primeiro dia do mes
       $primeiroDiaDoMes = mktime(0,0,0,$this->mes,1,$this->ano);
       // Quantos dias tem o mes
       $NumeroDiasMes = date('t',$primeiroDiaDoMes);

    $qtd_dias = $NumeroDiasMes;

    $this->pdf = new clsPDF("Acompanhamento Mensal - {$this->ano}", "Acompanhamento Mensal - {$this->meses_do_ano[$this->mes]}", "A4", "", false, false);
    $this->pdf->largura  = 842.0;
      $this->pdf->altura = 595.0;

    $this->pdf->OpenPage();

    $this->addCabecalho();

    $this->pdf->linha_relativa(30, 140, 780, 0);
    $this->pdf->linha_relativa(30, 140, 0, 180);


    $imagem = girarTextoImagem("Total",8);
    $this->pdf->InsertJpng('png',$imagem,754,270,1);

    $imagem = girarTextoImagem("Casa não feito",8);
    $this->pdf->InsertJpng('png',$imagem,769,270,1);
    $imagem = girarTextoImagem("Justificadas",8);
    $this->pdf->InsertJpng('png',$imagem,784,270,1);

    $imagem = girarTextoImagem(("Não justificadas"),8);
    $this->pdf->InsertJpng('png',$imagem,798,270,1);

    $this->pdf->escreve_relativo("Faltas", 773, 142, 45, 15);
    $this->pdf->escreve_relativo("Dia do mês", 40, 265, 60, 15, null, 8);

    for($i=0; $i<31; $i++)
    {
      $this->pdf->linha_relativa(285+($i*15), 140, 0, 180);
      if($i+1 <= $qtd_dias)
      {
        $this->pdf->escreve_relativo($i+1, 287+($i*15), 265, 15, 15, null, 8);
      }
    }
    $this->pdf->linha_relativa(750, 140, 0, 180);//total

    $this->pdf->linha_relativa(765, 140, 0, 180);
    $this->pdf->linha_relativa(780, 155, 0, 165);
    $this->pdf->linha_relativa(795, 155, 0, 165);

    $this->pdf->linha_relativa(810, 140, 0, 180);//fim

    $this->pdf->escreve_relativo("Nº do dia letivo", 40, 280, 60, 15, null, 8);
    $this->pdf->escreve_relativo("Nº da aula", 40, 295, 60, 15, null, 8);
    $this->pdf->escreve_relativo("Ord", 40, 310, 60, 15, null, 8);
    $this->pdf->escreve_relativo("Nome do aluno", 65, 310, 60, 15, null, 8);

    $this->pdf->linha_relativa(765, 155, 45, 0);

    $this->pdf->linha_relativa(30, 275, 780, 0);

    $this->pdf->linha_relativa(30, 290, 780, 0);
    $this->pdf->linha_relativa(30, 305, 780, 0);
    $this->pdf->linha_relativa(30, 320, 780, 0);

    $this->pdf->linha_relativa(60, 305, 0, 15);

    if ($this->is_padrao || $this->ano == 2007) {
      $this->semestre = null;
    }

    $obj_matricula = new clsPmieducarMatriculaTurma();
    $obj_matricula->setOrderby('nome_ascii');
    $lst_matricula = $obj_matricula->lista(null,$this->ref_cod_turma,null,null,null,null,null,null,1,$this->ref_cod_serie,$this->ref_cod_curso,$this->ref_cod_escola,$this->ref_cod_instituicao,null,null,array(1,2,3),null,null,$this->ano,null,true,null,null,true, null, null, null, null, $this->semestre);

    //$total_alunos = 100;
    $qtd_quebra = 17;
    $base = 305;
    $linha = 1;
    if($lst_matricula)
    {
      foreach ($lst_matricula as $ordem => $matricula)
      {
        $ordem++;
        $ordem = sprintf("%02d",$ordem);

        //for ($ordem = 1; $ordem <= $total_alunos; $ordem++)//foreach de alunos
        //{
        if($linha % $qtd_quebra == 0)
        {
          //nova pagina
          $this->pdf->ClosePage();
          $this->pdf->OpenPage();
          $base = 30;
          $linha = 0;
          $this->pdf->linha_relativa(30, 30, 780, 0);
          $qtd_quebra = 35;
        }
        $this->pdf->linha_relativa(30, $base+($linha*15), 0, 15);
        $this->pdf->linha_relativa(60, $base+($linha*15), 0, 15);
        $this->pdf->linha_relativa(30, ($base+15)+($linha*15), 780, 0);

        $this->pdf->escreve_relativo($ordem, 40, ($base+3)+($linha*15), 15, 15, null, 8);
        $this->pdf->escreve_relativo($matricula['nome'], 65, ($base+3)+($linha*15), 215, 15, null, 8);

        for($i=0; $i<31; $i++)
        {
          $this->pdf->linha_relativa(285+($i*15), $base+($linha*15), 0, 15);
        }
        $this->pdf->linha_relativa(285+($i*15), $base+($linha*15), 0, 15);//total

        $this->pdf->linha_relativa(765, $base+($linha*15), 0, 15);
        $this->pdf->linha_relativa(780, $base+($linha*15), 0, 15);
        $this->pdf->linha_relativa(795, $base+($linha*15), 0, 15);

        $this->pdf->linha_relativa(810, $base+($linha*15), 0, 15);//fim
        $linha++;
        //}
      }
    }
    //escrever total
    $this->pdf->linha_relativa(30, $base+($linha*15), 0, 15);
    $this->pdf->linha_relativa(60, $base+($linha*15), 0, 15);

    $this->pdf->escreve_relativo("Total", 35, ($base+3)+($linha*15), 20, 15, null, 8);

    for($i=0; $i<31; $i++)
    {
      $this->pdf->linha_relativa(285+($i*15), $base+($linha*15), 0, 15);
    }
    $this->pdf->linha_relativa(765, $base+($linha*15), 0, 15);
    $this->pdf->linha_relativa(780, $base+($linha*15), 0, 15);
    $this->pdf->linha_relativa(795, $base+($linha*15), 0, 15);
    $this->pdf->linha_relativa(285+($i*15), $base+($linha*15), 0, 15);//total

    $this->pdf->linha_relativa(810, $base+($linha*15), 0, 15);//fim
    $this->pdf->linha_relativa(30, $base+(($linha+1)*15), 780, 0);

    $this->pdf->ClosePage();
    $this->pdf->CloseFile();
    $this->get_link = $this->pdf->GetLink();


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
    $altura   = 30;
    $fonte    = 'arial';
    $corTexto = '#000000';

    // Cabeçalho
    $logo = $config->get($config->logo, 'imagens/brasao.gif');

    $this->pdf->quadrado_relativo(30, $altura, 782, 85);
    $this->pdf->insertImageScaled('gif', $logo, 50, 95, 41);

    // Título principal
    $titulo = $config->get($config->titulo, "i-Educar");
    $this->pdf->escreve_relativo($titulo, 30, 30, 782, 80,
      $fonte, 18, $corTexto, 'center' );
    $this->pdf->escreve_relativo(date("d/m/Y"), 25, 30, 782, 80, $fonte, 10,
      $corTexto, 'right' );

    // Dados escola
    $this->pdf->escreve_relativo("Instituição:$this->nm_instituicao", 119, 52,
      300, 80, $fonte, 7, $corTexto, 'left');
    $this->pdf->escreve_relativo("Escola:{$this->nm_escola}",132, 64, 300, 80,
      $fonte, 7, $corTexto, 'left');

    $this->pdf->escreve_relativo( "Série:{$this->nm_serie}",136, 76, 300, 80,
      $fonte, 7, $corTexto, 'left' );
    $this->pdf->escreve_relativo( "Turma:{$this->nm_turma}",132, 88, 300, 80,
      $fonte, 7, $corTexto, 'left' );

    // Título
    $this->pdf->escreve_relativo( "ACOMPANHAMENTO MENSAL I", 30, 75, 782, 80,
      $fonte, 12, $corTexto, 'center' );

    // Data
    $this->pdf->escreve_relativo("{$this->meses_do_ano[$this->mes]} DE $this->ano",
      45, 100, 782, 80, $fonte, 10, $corTexto, 'center');
    $this->pdf->escreve_relativo("Dias Letivos Previstos:______     Dias Letivos Dados:______   Faltas do Professor:______    Justificadas:______    Não Justificadas:______    Licença (dias):______", 30, 120, 780, 80, $fonte, 10, $corTexto, 'left');

    $this->page_y +=19;
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