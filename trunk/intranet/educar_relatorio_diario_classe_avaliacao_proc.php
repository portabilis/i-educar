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

require_once 'App/Model/IedFinder.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Diário de Classe - Avaliações');
    $this->processoAp = 670;
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
 * @todo      Adicionar no título ou no header do arquivo o módulo atual ao qual
 *   o relatório foi gerado. Pode ser um refactoring da lógica de
 *   educar_modulo_xml.php em App_Model_IedFinder
 * @see       intranet/educar_modulo_xml.php
 * @see       App_Model_IedFinder
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $ref_cod_instituicao;
  var $ref_cod_escola;
  var $ref_cod_serie;
  var $ref_cod_turma;

  var $ano;
  var $mes;

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
  var $avaliacao_globalizada;

  var $page_y = 139;

  var $get_file;

  var $cursos = array();

  var $get_link;

  var $total;

  var $ref_cod_modulo;

  var $numero_registros;
  var $em_branco;

  var $meses_do_ano = array(
    1  => 'JANEIRO',
    2  => 'FEVEREIRO',
    3  => 'MARÇO',
    4  => 'ABRIL',
    5  => 'MAIO',
    6  => 'JUNHO',
    7  => 'JULHO',
    8  => 'AGOSTO',
    9  => 'SETEMBRO',
    10 => 'OUTUBRO',
    11 => 'NOVEMBRO',
    12 => 'DEZEMBRO'
  );

  function renderHTML()
  {
    if ($_POST) {
      foreach ($_POST as $key => $value) {
        $this->$key = $value;
      }
    }

    if ($this->ref_ref_cod_serie) {
      $this->ref_cod_serie = $this->ref_ref_cod_serie;
    }

    $fonte    = 'arial';
    $corTexto = '#000000';

    if (empty($this->ref_cod_turma))
    {
      echo '
        <script>
          alert("Erro ao gerar relatório!\nNenhuma turma selecionada!");
          window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
        </script>';

      return TRUE;
    }

    if ($this->ref_cod_escola) {
      $obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
      $det_escola = $obj_escola->detalhe();
      $this->nm_escola = $det_escola['nome'];

      $obj_instituicao = new clsPmieducarInstituicao($det_escola['ref_cod_instituicao']);
      $det_instituicao = $obj_instituicao->detalhe();
      $this->nm_instituicao = $det_instituicao['nm_instituicao'];
    }

    $obj_calendario = new clsPmieducarEscolaAnoLetivo();
    $lista_calendario = $obj_calendario->lista($this->ref_cod_escola, $this->ano,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL);

    $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
    $det_turma = $obj_turma->detalhe();
    $this->nm_turma = $det_turma['nm_turma'];

    $obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
    $det_serie = $obj_serie->detalhe();
    $this->nm_serie = $det_serie['nm_serie'];

    $obj_pessoa = new clsPessoa_($det_turma['ref_cod_regente']);
    $det = $obj_pessoa->detalhe();
    $this->nm_professor = $det['nome'];

    if (!$lista_calendario) {
      echo '
        <script>
          alert("Escola não possui calendário definido para este ano");
          window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
        </script>';

      return TRUE;
    }

    $titulo = 'Diário de Classe - ' . $this->ano;

    $prox_mes = $this->mes + 1;
    $this->pdf = new clsPDF($titulo, $titulo, 'A4', '', FALSE, FALSE);

    $altura_linha     = 15;
    $inicio_escrita_y = 175;
    $altura_pagina    = 760;

    $obj = new clsPmieducarSerie();
    $obj->setOrderby('cod_serie, etapa_curso');
    $lista_serie_curso = $obj->lista(NULL, NULL, NULL,$this->ref_cod_curso, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_instituicao);

    $obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
    $det_curso = $obj_curso->detalhe();

    $obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
    $det_curso = $obj_curso->detalhe();

    // Recupera a lista de componentes curriculares da escola/série
    $componentes = App_Model_IedFinder::getEscolaSerieDisciplina(
      $this->ref_cod_serie, $this->ref_cod_escola
    );

    if (0 == count($componentes)) {
      echo '
        <script>
          alert("Turma não possui matriculas");
          window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
        </script>';

      return;
    }
    else {
      foreach ($componentes as $id => $componente) {
        $this->nm_disciplina = $componente->nome;
        $this->page_y = 139;

        // Número de semanas dos meses
        $obj_quadro = new clsPmieducarQuadroHorario();
        $obj_quadro->setCamposLista('cod_quadro_horario');
        $quadro_horario = $obj_quadro->lista(NULL, NULL, NULL, $this->ref_cod_turma,
          NULL, NULL, NULL, NULL, 1);

        if (!$quadro_horario && $det_curso['avaliacao_globalizada'] == 't') {
          echo '
            <script>
              alert("Turma não possui quadro de horários");
              window.location = "educar_relatorio_diario_classe.php";
            </script>';
          break;
        }

        $obj_quadro_horarios = new clsPmieducarQuadroHorarioHorarios();
        $obj_quadro_horarios->setCamposLista('dia_semana');
        $obj_quadro_horarios->setOrderby('1 asc');

        $lista_quadro_horarios = $obj_quadro_horarios->lista($quadro_horario,
          $this->ref_cod_serie, $this->ref_cod_escola, $disciplina, NULL, NULL,
          NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

        if (!$this->em_branco) {
          $obj_matricula_turma = new clsPmieducarMatriculaTurma();
          $obj_matricula_turma->setOrderby('nome_ascii');
          $lista_matricula = $obj_matricula_turma->lista( NULL, $this->ref_cod_turma,
            NULL, NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_serie,
            $this->ref_cod_curso, $this->ref_cod_escola, $this->ref_cod_instituicao,
            NULL, NULL, array(1, 2, 3), NULL, NULL, $this->ano, NULL, TRUE,
            NULL, NULL, TRUE);
        }

        $num_aluno = 1;

        if ($lista_matricula || $this->em_branco) {
          $this->pdf->OpenPage();
          $this->addCabecalho();

          if ($this->em_branco) {
            $lista_matricula = array();
            $this->numero_registros = $this->numero_registros ?
              $this->numero_registros : 20;

            for ($i = 0 ; $i < $this->numero_registros; $i++) {
              $lista_matricula[] = '';
            }
          }

          foreach ($lista_matricula as $matricula) {
            if($this->page_y > $altura_pagina) {
              $this->desenhaLinhasVertical();
              $this->pdf->ClosePage();
              $this->pdf->OpenPage();
              $this->page_y = 139;
              $this->addCabecalho();
            }

            $this->pdf->quadrado_relativo(30, $this->page_y , 540, $altura_linha);

            $this->pdf->escreve_relativo($num_aluno, 38 ,$this->page_y + 4,
              30, 15, $fonte, 7, $corTexto, 'left');

            $this->pdf->escreve_relativo($matricula['nome_aluno'] , 55,
              $this->page_y + 4, 160, 15, $fonte, 7, $corTexto, 'left');

            $num_aluno++;
            $this->page_y += $altura_linha;
          }

          $this->desenhaLinhasVertical();
          $this->rodape();
          $this->pdf->ClosePage();
        }
      }

      $this->pdf->CloseFile();
      $this->get_link = $this->pdf->GetLink();
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
        <center>
          Se o download não iniciar automaticamente <br>
          <a target="blank" href="%s" style="font-size: 16px; color: #000000; text-decoration: underline;">clique aqui!</a><br><br>
          <span style="font-size: 10px;">
            Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>
            Clique na Imagem para Baixar o instalador<br><br>
            <a href="http://www.adobe.com.br/products/acrobat/readstep2.html" target="new"><br><img src="imagens/acrobat.gif" width="88" height="31" border="0"></a>
          </span>
        </center>
      </html>', $this->get_link);
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

    $this->pdf->quadrado_relativo(30, $altura, 540, 85);
    $this->pdf->insertImageScaled('gif', $logo, 50, 95, 41);

    // Título principal
    $titulo = $config->get($config->titulo, 'i-Educar');
    $this->pdf->escreve_relativo($titulo, 30, 30, 782, 80, $fonte, 18,
      $corTexto, 'center');

    // Dados escola
    $this->pdf->escreve_relativo('Instituição: ' . $this->nm_instituicao, 120, 52,
      300, 80, $fonte, 7, $corTexto, 'left' );

    $this->pdf->escreve_relativo('Escola: ' . $this->nm_escola,132, 64, 300, 80,
      $fonte, 7, $corTexto, 'left');

    $dif = 0;

    if($this->nm_professor) {
      $this->pdf->escreve_relativo('Prof. Regente: ' . $this->nm_professor,111,
        76, 300, 80, $fonte, 7, $corTexto, 'left');
    }
    else {
      $dif = 12;
    }

    $this->pdf->escreve_relativo('Série: ' . $this->nm_serie, 138, 88  - $dif,
      300, 80, $fonte, 7, $corTexto, 'left');

    $this->pdf->escreve_relativo('Turma: ' . $this->nm_turma, 134, 100 - $dif,
      300, 80, $fonte, 7, $corTexto, 'left');

    $this->pdf->escreve_relativo('Diário de Classe - ' . $this->nm_disciplina,
      30, 75, 782, 80, $fonte, 12, $corTexto, 'center');

    $obj_modulo = new clsPmieducarModulo($this->ref_cod_modulo);
    $det_modulo = $obj_modulo->detalhe();

    $this->pdf->linha_relativa(201, 125, 0, 14);
    $this->pdf->linha_relativa(201, 125, 369, 0);
    $this->pdf->escreve_relativo('Avaliações', 195, 128, 350, 80, $fonte, 7,
      $corTexto, 'center');

    $this->pdf->linha_relativa(543, 125, 0, 14);
    $this->pdf->linha_relativa(30, 139, 0, 20);

    $this->pdf->linha_relativa(30, 139, 513, 0);
    $this->pdf->escreve_relativo('Média', 538, 137, 35, 80, $fonte, 7,
      $corTexto, 'center');

    $this->pdf->escreve_relativo('Nº', 36, 145, 100, 80, $fonte, 7, $corTexto, 'left');
    $this->pdf->escreve_relativo('Nome', 110, 145, 100, 80, $fonte, 7, $corTexto, 'left');

    $this->page_y +=19;

    $this->pdf->escreve_relativo('Dias de aula: ' . $this->total, 715, 100, 535,
      80, $fonte, 10, $corTexto, 'left');
  }

  function desenhaLinhasVertical()
  {
    $corTexto = '#000000';

    $this->total = 10;
    $largura_anos = 380;

    if ($this->total >= 1) {
      $incremental = floor($largura_anos/ ($this->total)) ;
    }
    else {
      $incremental = 1;
    }

    $reta_ano_x = 200 ;

    $resto = $largura_anos - ($incremental * $this->total);

    for ($linha = 0; $linha < $this->total; $linha++) {
      if (($resto > 0) || $linha == 0) {
        $reta_ano_x++;
        $resto--;
      }

      $this->pdf->linha_relativa($reta_ano_x, 139, 0, $this->page_y - 139);
      $reta_ano_x += $incremental;

    }

    $this->pdf->linha_relativa(50, 139, 0, $this->page_y - 139);
    $this->pdf->linha_relativa(812, 125, 0, $this->page_y - 139);
    $this->pdf->linha_relativa(570, 125, 0, $this->page_y - 139);
  }

  function rodape()
  {
    $corTexto  = '#000000';
    $fonte     = 'arial';
    $dataAtual = date('d/m/Y');
    $this->pdf->escreve_relativo('Data: ' . $dataAtual, 36,795, 100, 50, $fonte,
      7, $corTexto, 'left');

    $this->pdf->escreve_relativo('Assinatura do Professor(a)', 677, 520, 100, 50,
      $fonte, 7, $corTexto, 'left');

    $this->pdf->linha_relativa(660, 517, 130, 0);
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
