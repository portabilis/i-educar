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
require_once 'App/Model/MatriculaSituacao.php';
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Diário de Classe');
    $this->processoAp = 664;
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

  var $numero_registros;
  var $em_branco;

  var $page_y = 125;

  var $get_link;

  var $total;

  var $ref_cod_modulo;
  var $data_ini, $data_fim;

  /**
   * Define se será gerado uma lista com uma quantidade de dias padrão
   * (report.diario_classe.dias_temporario) caso o componente curricular não
   * esteja atribuído no quadro de horário da turma.
   *
   * @var bool
   */
  var $temporario = FALSE;

  function renderHTML()
  {
    global $coreExt;

    if ($_POST) {
      foreach ($_POST as $key => $value) {
        $this->$key = $value;
      }
    }

    $this->temporario = isset($_POST['temporario']) ? TRUE : FALSE;

    if ($this->ref_ref_cod_serie) {
      $this->ref_cod_serie = $this->ref_ref_cod_serie;
    }

    $fonte    = 'arial';
    $corTexto = '#000000';

    if (empty($this->ref_cod_turma)) {
      echo '
        <script>
          alert("Erro ao gerar relatório!\nNenhuma turma selecionada!");
          window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
        </script>';

      return TRUE;
    }

    $modulo_sequencial    = explode('-', $this->ref_cod_modulo);
    $this->ref_cod_modulo = $modulo_sequencial[0];
    $this->sequencial     = $modulo_sequencial[1];

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
    $regraId        = $det_serie['regra_avaliacao_id'];

    $obj_pessoa = new clsPessoa_($det_turma['ref_cod_regente']);
    $det = $obj_pessoa->detalhe();
    $this->nm_professor = $det['nome'];

    if (!$lista_calendario) {
      echo '
        <script>
          alert("Escola não possui calendário definido para este ano");
          window.parent.fechaExpansivel(\'div_dinamico_\' + (window.parent.DOM_divs.length - 1));
        </script>';

      return TRUE;
    }

    $altura_linha     = 23;
    $inicio_escrita_y = 175;

    $obj = new clsPmieducarSerie();
    $obj->setOrderby('cod_serie, etapa_curso');
    $lista_serie_curso = $obj->lista(NULL, NULL, NULL,$this->ref_cod_curso, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_instituicao);

    $obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
    $det_curso = $obj_curso->detalhe();

    // Seleciona a regra para verificar se a presença é geral
    $regraMapper   = new RegraAvaliacao_Model_RegraDataMapper();
    $regra         = $regraMapper->find($regraId);
    $presencaGeral = $regra->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL;

    // Seleciona o curso para ver se é padrão e decidir qual ano letivo pesquisar
    $db       = new clsBanco();
    $consulta = sprintf('SELECT padrao_ano_escolar FROM pmieducar.curso WHERE cod_curso = \'%d\'', $this->ref_cod_curso);
    $padrao_ano_escolar = $db->CampoUnico($consulta);

    $total_semanas = 0;

    if ($padrao_ano_escolar) {
      // Seleciona o módulo do ano letivo da escola
      $data = $this->getDatasModulo($this->ref_cod_modulo, $this->sequencial,
        $this->ano, $this->ref_cod_escola);
    }
    else {
      // Seleciona o módulo do ano letivo da turma
      $data = $this->getDatasModulo($this->ref_cod_modulo, $this->sequencial,
        $this->ano, NULL, $this->ref_cod_turma);
    }

    $meses = $data['meses'];
    $dias  = $data['dias'];

    if (!$this->data_ini) {
      $this->data_ini = $data['dataInicial'];
    }

    if (!$this->data_fim) {
      $this->data_fim = $data['dataFinal'];
    }

    $total_semanas = 0;

    for ($mes = $meses[0]; $mes <= $meses[1]; $mes++) {
      $mes_final = FALSE;

      if ($mes == $meses[0]) {
        $dia = $dias[0];
      }
      elseif ($mes == $meses[1]) {
        $dia       = $dias[1];
        $mes_final = TRUE;
      }
      else {
        $dia = 1;
      }

      $total_semanas += $this->getNumeroDiasMes($this->ref_cod_turma, $dia, $mes,
        $this->ano, $mes_final);
      $total_semanas += $ndm;
    }

    $this->pdf = new clsPDF('Diário de Classe - ' . $this->ano,
      sprintf('Diário de Classe - %s até %s de %s', $this->data_ini, $this->data_fim, $this->ano),
      'A4', '', FALSE, FALSE
    );

    $this->mes_inicial  = (int) $meses[0];
    $this->mes_final    = (int) $meses[1];
    $this->pdf->largura = 842.0;
    $this->pdf->altura  = 595.0;

    $this->total = $total_semanas;

    if (!$this->em_branco) {
      $obj_matricula_turma = new clsPmieducarMatriculaTurma();
      $obj_matricula_turma->setOrderby('nome_ascii');

      $matriculaSituacao = array(
        App_Model_MatriculaSituacao::APROVADO,
        App_Model_MatriculaSituacao::REPROVADO,
        App_Model_MatriculaSituacao::EM_ANDAMENTO
      );

      $lista_matricula = $obj_matricula_turma->lista(NULL, $this->ref_cod_turma,
        NULL, NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_serie,
        $this->ref_cod_curso, $this->ref_cod_escola, $this->ref_cod_instituicao,
        NULL, NULL, $matriculaSituacao, NULL, NULL, $this->ano, NULL, TRUE, NULL,
        NULL, TRUE
      );
    }

    if ($this->em_branco) {
      $lista_matricula = array();
      $this->numero_registros = $this->numero_registros ? $this->numero_registros : 20;

      for ($i = 0 ; $i < $this->numero_registros; $i++) {
        $lista_matricula[] = '';
      }
    }

    // Seleciona os componentes da escola/série
    $componentes = App_Model_IedFinder::getEscolaSerieDisciplina(
      $this->ref_cod_serie, $this->ref_cod_escola
    );

    if (0 < count($componentes) && FALSE == $presencaGeral) {
      $this->total = $total_semanas = 0;

      foreach ($componentes as $componente) {
        $this->nm_disciplina = $componente->nome;
        $this->page_y = 125;

        if (FALSE == $presencaGeral) {
          // Número de semanas dos meses
          $obj_quadro = new clsPmieducarQuadroHorario();
          $obj_quadro->setCamposLista('cod_quadro_horario');
          $quadro_horario = $obj_quadro->lista(NULL, NULL, NULL, $this->ref_cod_turma,
            NULL, NULL, NULL, NULL, 1);

          $total_semanas    = 0;
          $this->indefinido = FALSE;

          if (!$quadro_horario) {
            echo '
              <script>
                alert(\'Turma não possui quadro de horários\');
                window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
              </script>';

            die();
          }

          $obj_quadro_horarios = new clsPmieducarQuadroHorarioHorarios();
          $obj_quadro_horarios->setCamposLista('dia_semana');
          $obj_quadro_horarios->setOrderby('1 asc');

          $lista_quadro_horarios = $obj_quadro_horarios->lista($quadro_horario[0],
            $this->ref_cod_serie, $this->ref_cod_escola, $componente->id, NULL, NULL,
            NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

          // Se não for retornado horário e o diário não for temporário, gera
          // a lista para o próximo componente
          if (FALSE == $lista_quadro_horarios && FALSE == $this->temporario) {
            continue;
          }

          // Caso o diário seja temporário, gera lista de alunos para 30 dias
          // por padrão
          if (FALSE == $lista_quadro_horarios && TRUE == $this->temporario) {
            $this->indefinido = TRUE;

            $total_semanas = $coreExt['Config']->get(
              $coreExt['Config']->report->diario_classe->dias_temporarios, 30
            );
          }

          for ($mes_ = $meses[0]; $mes_ <= $meses[1] && FALSE != $lista_quadro_horarios; $mes_++) {
            $mes_final = FALSE;

            foreach ($lista_quadro_horarios as $dia_semana) {
              if($mes_ == $meses[0]) {
                $dia = $dias[0];
              }
              elseif ($mes_ == $meses[1]) {
                $dia = $dias[1];
                $mes_final = TRUE;
              }
              else {
                $dia = 1;
              }

              $total_semanas += $this->getDiasSemanaMes(
                $this->ref_cod_turma, $dia, $mes_, $this->ano, $dia_semana, $mes_final
              );
            }
          }

          $this->total = $total_semanas;
        }

        if (!$this->total) {
          continue;
        }

        $this->gerarListaAlunos($lista_matricula);
      }
    }
    else {
      $this->gerarListaAlunos($lista_matricula);
    }

    if ($this->total) {
      $this->pdf->CloseFile();
      $this->get_link = $this->pdf->GetLink();
    }
    else {
      $this->mensagem = 'Não existem dias letivos cadastrados para esta turma';
      return;
    }

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

  /**
   * Retorna a data inicial e final de um módulo do ano letivo de uma escola ou
   * turma.
   *
   * @param  int      $codModulo
   * @param  int      $sequencial
   * @param  int      $ano
   * @param  int|NULL $codEscola   Opcional. O código da escola para o qual o
   *   módulo do ano letivo será selecionado
   * @param  int|NULL $codTurma    Opcional. O código da turma com o qual o
   *   módulo do ano letivo será selecionado
   * @throws App_Model_Exception
   * @return array
   */
  function getDatasModulo($codModulo, $sequencial, $ano, $codEscola = NULL, $codTurma = NULL)
  {
    if (is_null($codEscola) && is_null($codTurma)) {
      require_once 'App/Model/Exception.php';
      throw new App_Model_Exception('É necessário informar um código de escola ou de turma.');
    }

    if ($codEscola) {
      $sql = sprintf("
        SELECT
          to_char(data_inicio, 'dd/mm') || '-' || to_char(data_fim, 'dd/mm')
        FROM
          pmieducar.ano_letivo_modulo,
          pmieducar.modulo
        WHERE
          modulo.cod_modulo = ano_letivo_modulo.ref_cod_modulo
          AND modulo.ativo = 1
          AND ano_letivo_modulo.ref_cod_modulo = '%d'
          AND ano_letivo_modulo.sequencial = '%d'
          AND ref_ano = '%d'
          AND ref_ref_cod_escola = '%d'
        ORDER BY
          data_inicio,data_fim ASC", $codModulo, $sequencial, $ano, $codEscola);
    }
    else {
      $sql = sprintf("
        SELECT
          to_char(data_inicio, 'mm') || '-' || to_char(data_fim, 'mm')
        FROM
          pmieducar.turma_modulo,
          pmieducar.modulo
        WHERE
          modulo.cod_modulo = turma_modulo.ref_cod_modulo
          AND ref_cod_turma = '%d'
          AND turma_modulo.ref_cod_modulo = '%d'
          AND turma_modulo.sequencial = '%d'
          AND to_char(data_inicio, 'yyyy') = '%d'
        ORDER BY
          data_inicio, data_fim ASC", $codTurma, $codModulo, $sequencial, $ano);
    }

    $db    = new clsBanco();
    $meses = $db->CampoUnico($sql);

    $data = explode('-', $meses);

    $data_ini = explode('/', $data[0]);
    $data_fim = explode('/', $data[1]);

    return array(
      'dataInicial' => $data[0],
      'dataFinal'   => $data[1],
      'meses'       => array($data_ini[1], $data_fim[1]),
      'dias'        => array($data_ini[0], $data_fim[0])
    );
  }

  /**
   * Gera a lista de alunos no documento PDF.
   *
   * @param array  $lista_matricula
   * @param string $fonte
   * @param string $corTexto
   */
  function gerarListaAlunos($lista_matricula = NULL, $fonte = 'arial', $corTexto = '#000000')
  {
    if (!$lista_matricula && !$this->em_branco) {
      echo '
        <script>
          alert("Turma não possui matrículas");
          window.parent.fechaExpansivel(\'div_dinamico_\' + (window.parent.DOM_divs.length - 1));
        </script>';

      return;
    }

    $this->pdf->OpenPage();
    $this->addCabecalho();

    $num = 0;
    foreach ($lista_matricula as $matricula) {
      $num++;

      if ($this->page_y > 500) {
        $this->desenhaLinhasVertical();
        $this->pdf->ClosePage();
        $this->pdf->OpenPage();
        $this->page_y = 125;
        $this->addCabecalho();
      }

      $this->pdf->quadrado_relativo(30, $this->page_y, 782, 19);

      $this->pdf->escreve_relativo($matricula['nome'], 33,
        $this->page_y + 4, 160, 15, $fonte, 7, $corTexto, 'left');

      $this->pdf->escreve_relativo(sprintf('%02d', $num), 757,
        $this->page_y + 4, 30, 30, $fonte, 7, $corTexto, 'left');

      $this->page_y +=19;
    }

    $this->desenhaLinhasVertical();
    $this->pdf->ClosePage();
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
    $altura   = 30;
    $fonte    = 'arial';
    $corTexto = '#000000';

    // Cabeçalho
    $logo = $config->get($config->logo, 'imagens/brasao.gif');

    $this->pdf->quadrado_relativo( 30, $altura, 782, 85 );
    $this->pdf->insertImageScaled('gif', $logo, 50, 95, 41);

    // Título principal
    $titulo = $config->get($config->titulo, 'i-Educar');

    $this->pdf->escreve_relativo($titulo, 30, 30, 782, 80, $fonte, 18, $corTexto, 'center');

    $this->pdf->escreve_relativo(date('d/m/Y'), 25, 30, 782, 80, $fonte, 10, $corTexto, 'right' );

    // Dados escola
    $this->pdf->escreve_relativo('Instituição: ' . $this->nm_instituicao, 120, 52,
      300, 80, $fonte, 7, $corTexto, 'left');

    $this->pdf->escreve_relativo('Escola: ' . $this->nm_escola,132, 64, 300, 80,
      $fonte, 7, $corTexto, 'left');

    $dif = 0;

    if ($this->nm_professor) {
      $this->pdf->escreve_relativo('Prof. Regente: ' . $this->nm_professor,111, 76,
        300, 80, $fonte, 7, $corTexto, 'left');
    }
    else {
      $dif = 12;
    }

    $this->pdf->escreve_relativo('Série: ' . $this->nm_serie,138, 88  - $dif,
      300, 80, $fonte, 7, $corTexto, 'left');

    $this->pdf->escreve_relativo('Turma: ' . $this->nm_turma,134, 100 - $dif, 300,
      80, $fonte, 7, $corTexto, 'left');

    // Título
    $nm_disciplina = '';
    if ($this->nm_disciplina) {
      $nm_disciplina = ' - ' . $this->nm_disciplina;
    }

    $this->pdf->escreve_relativo('Diário de Frequência ' . $nm_disciplina, 30,
      75, 782, 80, $fonte, 12, $corTexto, 'center');

    $obj_modulo = new clsPmieducarModulo($this->ref_cod_modulo);
    $det_modulo = $obj_modulo->detalhe();

    // Data
    $this->pdf->escreve_relativo(
      sprintf('%s até %s de %s', $this->data_ini, $this->data_fim, $this->ano),
      45, 100, 782, 80, $fonte, 10, $corTexto, 'center');

    $this->pdf->linha_relativa(201, 125, 612, 0);

    $this->page_y += 19;

    $label = $this->total;
    $pos   = 715;

    if ($this->indefinido) {
      $label = 'indefinido';
      $pos   = 680;
    }

    $this->pdf->escreve_relativo('Dias de aula: ' . $label, $pos, 100, 535,
      80, $fonte, 10, $corTexto, 'left');
  }

  function desenhaLinhasVertical()
  {
    $corTexto = '#000000';

    $largura_anos = 550;

    if ($this->total >= 1) {
      $incremental = floor($largura_anos / ($this->total +1)) ;
    }
    else {
      $incremental = 1;
    }

    $reta_ano_x = 200 ;

    $resto = $largura_anos - ($incremental * $this->total);

    for ($linha = 0; $linha < $this->total + 1; $linha++) {
      if (($resto > 0) || $linha == 0) {
        $reta_ano_x++;
        $resto--;
      }

      $this->pdf->linha_relativa($reta_ano_x, 125, 0, $this->page_y - 125);

      $reta_ano_x += $incremental;
    }

    $this->pdf->linha_relativa(812, 125, 0, $this->page_y - 125);

    $this->pdf->escreve_relativo('Nº:', 755, 128, 100, 80, $fonte, 7, $corTexto, 'left');

    $this->pdf->linha_relativa(775, 125, 0, $this->page_y - 125);

    $this->pdf->escreve_relativo('Faltas', 783, 128, 100, 80, $fonte, 7, $corTexto, 'left');

    $this->rodape();
    $this->pdf->ClosePage();
    $this->pdf->OpenPage();
    $this->page_y = 125;
    $this->addCabecalho();

    for ($ct = 125; $ct < 500; $ct += 19) {
      $this->pdf->quadrado_relativo(30, $ct , 782, 19);
    }

    $this->pdf->escreve_relativo('Observações', 30, 130, 782, 30, $fonte, 7,
      $corTexto, 'center');

    $this->pdf->linha_relativa(418, 144, 0, 360);
  }

  function rodape()
  {
    $corTexto  = '#000000';
    $fonte     = 'arial';
    $dataAtual = date('d/m/Y');
    $this->pdf->escreve_relativo('Data: ' . $dataAtual, 36,795, 100, 50, $fonte,
      7, $corTexto, 'left');

    $this->pdf->escreve_relativo('Assinatura do Professor(a)', 695, 520, 100, 50,
      $fonte, 7, $corTexto, 'left');

    $this->pdf->linha_relativa(660, 517, 130, 0);
  }

  /**
   * Retorna o número de dias de um mês a partir de certo dia descartando
   * domingos, sábados e dias não letivos.
   *
   * @param   int   $codTurma
   * @param   int   $dia
   * @param   int   $mes
   * @param   int   $ano
   * @param   bool  $mes_final
   * @return  int
   */
  function getNumeroDiasMes($codTurma, $dia, $mes, $ano, $mes_final = FALSE)
  {
    return $this->_getNumeroDias($codTurma, $dia, $mes, $ano,
      array($this, '_counterNumeroDiaMes'), $mes_final);
  }

  /**
   * Retorna o número de dias de um mês contabilizando apenas o dia da semana
   * (domingo, segunda, ... sábado) desejado, descartando dias não letivos
   * enquanto a data do dia da semana não for maior que a data final do
   * período definido pelo módulo escolhido.
   *
   * @param   int   $codTurma
   * @param   int   $dia
   * @param   int   $mes
   * @param   int   $ano
   * @param   int   $dia_semana
   * @param   bool  $mes_final
   * @return  int
   */
  function getDiasSemanaMes($codTurma, $dia, $mes, $ano, $dia_semana,
    $mes_final = FALSE)
  {
    return $this->_getNumeroDias($codTurma, $dia, $mes, $ano,
      array($this, '_counterDiasSemanaMes'), $mes_final, $dia_semana);
  }

  /**
   * @access  protected
   * @param   int   $codTurma
   * @param   int   $dia
   * @param   int   $mes
   * @param   int   $ano
   * @param   array $counter
   * @param   int   $dia_semana
   * @param   bool  $mes_final
   * @return  int
   */
  function _getNumeroDias($codTurma, $dia, $mes, $ano, $counter,
    $mes_final = FALSE, $dia_semana = NULL)
  {
    static $calendarioTurmaMapper = NULL;

    $year  = $ano;
    $month = $mes;

    $date = mktime(1, 1, 1, $month, $dia, $year);

    $first_day_of_month = strtotime('-' . (date('d', $date) - 1) . ' days', $date);
    $last_day_of_month  = strtotime('+' . (date('t', $first_day_of_month) - 1) . ' days', $first_day_of_month);

    $last_day_of_month = date('d', $last_day_of_month);

    $obj_calendario = new clsPmieducarCalendarioAnoLetivo();
    $obj_calendario->setCamposLista('cod_calendario_ano_letivo');
    $lista_calendario = $obj_calendario->lista(NULL, $this->ref_cod_escola,
      NULL, NULL, $this->ano, NULL, NULL, NULL, NULL, 1);

    // Dias não letivos da turma
    $diasNaoLetivosTurma = array();

    if (is_array($lista_calendario)) {
      $lista_calendario = array_shift($lista_calendario);

      $obj_dia = new clsPmieducarCalendarioDia();
      $obj_dia->setCamposLista('dia');
      $dias_nao_letivo = $obj_dia->lista($lista_calendario, $mes, NULL, NULL,
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, "'n'");

      // Instancia o mapper apenas uma vez
      if (is_null($calendarioTurmaMapper)) {
        require_once 'Calendario/Model/TurmaDataMapper.php';
        $calendarioTurmaMapper = new Calendario_Model_TurmaDataMapper();
      }

      // Carrega os dias do mês da turma
      $where = array(
        'calendarioAnoLetivo' => $lista_calendario,
        'ano'                 => $ano,
        'mes'                 => $mes,
        'turma'               => $codTurma
      );

      $diasTurma = $calendarioTurmaMapper->findAll(array(), $where);

      // Separa apenas os dias da turma que forem não-letivos
      foreach ($diasTurma as $diaTurma) {
        if (in_array($diaTurma->dia, $dias_nao_letivo)) {
          $diasNaoLetivosTurma[] = $diaTurma->dia;
        }
      }
    }

    if ($mes_final) {
      $last_day_of_month = $dia;
      $dia = 1;
    }

    // Argumentos para o callback $counter
    $args = array(
      'dia'                 => $dia,
      'mes'                 => $mes,
      'ano'                 => $ano,
      'last_day_of_month'   => $last_day_of_month,
      'diasNaoLetivosTurma' => $diasNaoLetivosTurma,
      'dia_semana'          => $dia_semana
    );

    return call_user_func_array($counter, $args);
  }

  /**
   * @access  protected
   * @param   int    $dia
   * @param   int    $mes
   * @param   int    $ano
   * @param   int    $last_day_of_month
   * @param   array  $diasNaoLetivosTurma
   * @return  int
   */
  function _counterNumeroDiaMes($dia, $mes, $ano, $last_day_of_month,
    $diasNaoLetivosTurma)
  {
    $numero_dias = 0;

    for ($day = $dia; $day <= $last_day_of_month; $day++) {
      $date = mktime(1, 1, 1, $mes, $day, $ano);
      $dia_semana_corrente = getdate($date);
      $dia_semana_corrente = $dia_semana_corrente['wday'] + 1;

      if (($dia_semana_corrente != 1 && $dia_semana_corrente != 7) && !in_array($day, $diasNaoLetivosTurma)) {
        $numero_dias++;
      }
    }

    return $numero_dias;
  }

  /**
   * @access  protected
   * @param   int    $dia
   * @param   int    $mes
   * @param   int    $ano
   * @param   int    $last_day_of_month
   * @param   array  $diasNaoLetivosTurma
   * @return  int
   */
  function _counterDiasSemanaMes($dia, $mes, $ano, $last_day_of_month,
    $diasNaoLetivosTurma, $dia_semana)
  {
    $numero_dias = 0;

    for($day = $dia; $day <= $last_day_of_month; $day++) {
      $date = mktime(1, 1, 1, $mes, $day, $ano);
      $dia_semana_corrente = getdate($date);
      $dia_semana_corrente = $dia_semana_corrente['wday'] + 1;

      $data_atual = sprintf('%s/%s/%s', $day, $mes, $ano);
      $data_final = sprintf('%s/%s', $this->data_fim, $ano);

      if (
        ($dia_semana == $dia_semana_corrente) &&
        !in_array($day, $diasNaoLetivosTurma) &&
        data_maior($data_final, $data_atual)
      ) {
        $numero_dias++;
      }
    }

    return $numero_dias;
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
