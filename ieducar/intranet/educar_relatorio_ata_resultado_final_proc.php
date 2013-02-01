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
require_once 'Avaliacao/Service/Boletim.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Ata de Resultado Final');
    $this->processoAp         = 807;
    $this->renderMenu         = FALSE;
    $this->renderMenuSuspenso = FALSE;
  }
}

/**
 * index class.
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

  /**
   * @var RegraAvaliacao_Model_Regra
   */
  var $regra = NULL;

  var $ref_cod_instituicao;
  var $ref_cod_escola;
  var $ref_cod_serie;
  var $ref_ref_cod_serie;
  var $ref_cod_curso;
  var $ref_cod_turma;

  var $componentes = array();

  var $ano;
  var $nm_escola;
  var $nm_instituicao;
  var $nm_curso;
  var $nm_serie;
  var $nm_turma;
  var $nm_turno;

  var $pdf;
  var $page_y = 139;
  var $get_link;
  var $campo_assinatura;
  var $total = 0;

  var $dias_letivos;
  var $presencaGeral;

  function renderHTML()
  {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    foreach ($_POST as $key => $value) {
      $this->$key = $value;
    }

    $this->ref_cod_serie = $this->ref_ref_cod_serie;

    $fonte    = 'arial';
    $corTexto = '#000000';

    if (!is_numeric($this->ref_cod_instituicao) || !is_numeric($this->ref_cod_escola) ||
      !is_numeric($this->ref_cod_curso) || !is_numeric($this->ref_cod_serie) ||
      !is_numeric($this->ref_cod_turma) || !is_numeric($this->ano))
    {
      echo '
        <script>
          alert("A turma não possui nenhum aluno com situação final definida");
          window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
        </script>';

      return TRUE;
    }

    $sql = sprintf("
      SELECT
        cod_matricula,
        aprovado,
        ref_ref_cod_serie,
        (SELECT
           nome
         FROM
           pmieducar.aluno a,
           cadastro.pessoa p
         WHERE
           a.cod_aluno = m.ref_cod_aluno
           AND p.idpes = a.ref_idpes
        ) AS nome
      FROM
        pmieducar.matricula m,
        pmieducar.matricula_turma mt
      WHERE
        mt.ref_cod_turma = %d
        AND mt.ref_cod_matricula = m.cod_matricula
        AND m.ativo = 1
        AND mt.ativo = 1
        AND aprovado IN (1, 2)
        AND ano = %d
      ORDER BY
        ref_ref_cod_serie, nome", $this->ref_cod_turma, $this->ano);

    //verificar se a turma é multiseriada
    $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
    $det_turma = $obj_turma->detalhe();
    $ref_ref_cod_serie_mult = $det_turma['ref_ref_cod_serie_mult'];

    $db = new clsBanco();
    $db->Consulta($sql);

    if (!$db->numLinhas()) {
      echo '
        <script>
          alert("A turma não possui nenhum aluno com situação final definida");
          window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length - 1));
        </script>';

      return TRUE;
    }

    $numAlunos = $db->numLinhas();

    // Nome da instituição
    $obj_instituicao = new clsPmieducarInstituicao($this->ref_cod_instituicao);
    $det_instituicao = $obj_instituicao->detalhe();
    $this->nm_instituicao = $det_instituicao['nm_instituicao'];

    // Nome da escola
    $obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
    $det_escola = $obj_escola->detalhe();
    $this->nm_escola = $det_escola['nome'];

    // Nome do curso
    $obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
    $det_curso = $obj_curso->detalhe();
    $this->nm_curso = $det_curso['nm_curso'];

    // Série
    $obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
    $obj_serie->setOrderby('nm_serie');
    $det_serie = $obj_serie->detalhe();
    $this->nm_serie = $det_serie['nm_serie'];

    // Seleciona a regra de avaliação da série
    $regraMapper = new RegraAvaliacao_Model_RegraDataMapper();
    $this->regra = $regraMapper->find($det_serie['regra_avaliacao_id']);

    // Carrega as definições de disciplina
    $componentes = App_Model_IedFinder::getComponentesTurma(
      $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_turma
    );

    foreach ($componentes as $id => $componente) {
      $this->componentes[$id] = $componente;
    }

    $this->presencaGeral = ($this->regra->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL);

    $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
    $obj_turma->setCamposLista('nm_turma, hora_inicial');
    $det_turma = $obj_turma->detalhe();

    if ($det_turma['hora_inicial'] < '12:00') {
      $this->nm_turno = 'Matutino';
    }
    elseif ($det_turma['hora_inicial'] < '18:00') {
      $this->nm_turno = 'Vespertino';
    }
    else {
      $this->nm_turno = 'Noturno';
    }

    $this->nm_turma = $det_turma["nm_turma"];
    $this->buscaDiasLetivos();

    asort($this->componentes);

    $this->pdf = new clsPDF('Ata de Resultado Final - ' . $this->ano,
      'Ata de Resultado Final', 'A4', '', FALSE, FALSE);

    $this->pdf->largura = 842.0;
    $this->pdf->altura  = 595.0;

    $this->pdf->OpenPage();
    $this->addCabecalho();

    $esquerda  = 3;
    $direita   = 834;
    $tam_texto = 10;
    $altura    = 130;

    $altura         += 50;
    $espessura_linha = 0.3;

    $alunos_matriculados = array();

    while ($db->ProximoRegistro()) {
      list($cod_matricula, $aprovado, $ref_cod_serie, $nome) = $db->Tupla();
      $alunos_matriculados[$cod_matricula] =
        array(
          'aprovado'      => $aprovado,
          'nome'          => $nome,
          'ref_cod_serie' => $ref_cod_serie
        );
      }

      if (is_array($alunos_matriculados) && count($alunos_matriculados)) {
        $this->getAlunoNotasFaltasTable($alunos_matriculados, $det_curso, $curso_conceitual);
      }

      $this->rodape();

      $this->pdf->CloseFile();
      $this->get_link = $this->pdf->GetLink();

      echo sprintf("
        <script>
          window.onload=function() {
            parent.EscondeDiv('LoadImprimir');
            window.location='download.php?filename=%s'
          }
        </script>", $this->get_link);

      echo sprintf("
        <html>
          <center>
            Se o download não iniciar automaticamente <br /><a target='_blank' href='%s' style='font-size: 16px; color: #000000; text-decoration: underline;'>clique aqui!</a><br><br>
            <span style='font-size: 10px;'>
              Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>
              Clique na Imagem para Baixar o instalador<br><br>
              <a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br>
                <img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\">
              </a>
            </span>
          </center>
        </html>", $this->get_link);
  }

  function getComponentesTableHeader($esquerda, $espacoComponente)
  {
    $tam_texto = 9;
    $altura    = 20;
    $tam_texto = 9;
    $fonte     = 'arial';
    $corTexto  = '#000000';
    $carga_global = 0;

    $espacoDiv = $this->presencaGeral ? 1 : 2;

    $componentesTotal  = count($this->componentes) + ($this->presencaGeral ? 1 : 0);
    $espacoComponentes = ceil($espacoComponente / $componentesTotal);

    foreach ($this->componentes as $componente) {
      $carga_global += $componente->cargaHoraria;

      $this->pdf->escreve_relativo($componente->abreviatura, $esquerda,
        $this->page_y - $altura, $espacoComponentes, 100, $fonte, $tam_texto, $corTexto, 'center');

      $this->pdf->escreve_relativo($componente->cargaHoraria . ' hrs', $esquerda,
        $this->page_y + 10 - $altura, $espacoComponentes, 50, $fonte,
        $tam_texto - 2, $corTexto, 'center');

      $this->pdf->escreve_relativo('Nota / Conceito', $esquerda, $this->page_y + 3,
        $espacoComponentes / $espacoDiv, 50, $fonte, $tam_texto - 2, $corTexto, 'center');

      if (!$this->presencaGeral) {
        $this->pdf->escreve_relativo('Falta', $esquerda + $espacoComponentes / 2,
          $this->page_y + 3, $espacoComponentes / $espacoDiv, 50, $fonte, $tam_texto - 2,
          $corTexto, 'center');
      }

      $this->pdf->linha_relativa($esquerda, $this->page_y - $altura, 0, $altura * 2);

      $esquerda += $espacoComponentes;
      $this->pdf->linha_relativa($esquerda + $espacoComponentes / $espacoDiv,
        $this->page_y, 0, $altura);
    }

    if ($this->presencaGeral) {
      $this->pdf->linha_relativa($esquerda, $this->page_y - $altura, 0, $altura * 2);

      $this->pdf->escreve_relativo('Faltas', $esquerda, $this->page_y, $espacoComponentes,
        50, $fonte, $tam_texto, $corTexto, 'center');

      $this->pdf->linha_relativa($esquerda += $espacoComponentes,
      $this->page_y - $altura, 0, $altura * 2);
    }

    $this->page_y += $altura;

    return $carga_global;
  }

  function getAlunoNotasFaltasTable($alunos_matriculados)
  {
    $fonte             = 'arial';
    $corTexto          = '#000000';
    $esquerda_original = 3;
    $espessura_linha   = 0.3;
    $tam_texto         = 9;
    $direita           = 834;
    $altura            = 20;

    $obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
    $det_serie = $obj_serie->detalhe();

    $espacoDiv = $this->presencaGeral ? 1 : 2;

    foreach ($alunos_matriculados as $matricula => $aluno) {
      $boletim = new Avaliacao_Service_Boletim(array('matricula' => $matricula));
      $medias  = $boletim->getMediasComponentes();

      if ($this->presencaGeral) {
        // Soma as faltas do aluno
        $faltas = array_sum(CoreExt_Entity::entityFilterAttr(
          $boletim->getFaltasGerais(), 'etapa', 'quantidade')
        );
      }
      else {
        $faltas = $boletim->getFaltasComponentes();
      }

      $esquerda = $esquerda_original;

      // Matrícula
      $this->pdf->quadrado_relativo($esquerda, $this->page_y, $direita, $altura);

      $this->pdf->escreve_relativo($matricula, $esquerda, $this->page_y + 2, 45,
        45, $fonte, $tam_texto, $corTexto, 'center');

      $this->pdf->linha_relativa($esquerda += 45, $this->page_y, 0, $altura);

      // Nome do aluno
      $espaco_nome = 150;

      $this->pdf->escreve_relativo($aluno['nome'], $esquerda, $this->page_y + 2,
        $espaco_nome, 45, $fonte, $tam_texto, $corTexto, 'center');

      $this->pdf->linha_relativa($esquerda += $espaco_nome, $this->page_y, 0, $altura);

      // Situação da matrícula
      $this->pdf->escreve_relativo(App_Model_MatriculaSituacao::getInstance()->getValue($aluno['aprovado']),
        $esquerda, $this->page_y + 4, 45, 45, $fonte, $tam_texto, $corTexto, 'center');

      $this->pdf->linha_relativa($esquerda += 45, $this->page_y, 0, $altura);

      $espacoComponentes = ceil(
        ($direita - $esquerda) / (count($this->componentes) + ($this->presencaGeral ? 1 : 0))
      );

      // Exibe as médias e faltas do aluno
      foreach ($this->componentes as $id => $componente) {
        // Se não tem média, foi dispensado do componente
        if (!isset($medias[$id])) {
          $media  = 'D';
          $faltas = 'D';
        }
        else {
          $media = $medias[$id][0];
          $media = $media->mediaArredondada;

          if (!$this->presencaGeral) {
            if (isset($faltas[$id])) {
              $faltas = array_sum(CoreExt_Entity::entityFilterAttr(
                $faltas[$id], 'id', 'quantidade')
              );
            }
          }
        }

        // Média
        $this->pdf->escreve_relativo($media, $esquerda, $this->page_y + 4,
          $espacoComponentes / $espacoDiv, 50, $fonte, $tam_texto + 1, $corTexto, 'center');

        $this->pdf->linha_relativa($esquerda + $espacoComponentes / $espacoDiv,
          $this->page_y, 0, $altura);

        // Exibe as faltas no componente curricular, caso a presença não seja geral
        if (!$this->presencaGeral) {
          $this->pdf->escreve_relativo($faltas, $esquerda + $espacoComponentes / $espacoDiv,
            $this->page_y + 4, $espacoComponentes / $espacoDiv, 100, $fonte, $tam_texto + 1,
            $corTexto, 'center');
        }

        $esquerda += $espacoComponentes;
        $this->pdf->linha_relativa($esquerda, $this->page_y, 0, $altura);
      }

      // Exibe as faltas no total, caso a presença seja geral
      if ($this->presencaGeral) {
        $this->pdf->escreve_relativo($faltas, $esquerda,
          $this->page_y + 4, $espacoComponentes, 50, $fonte, $tam_texto + 1,
          $corTexto, 'center');

        $esquerda += $espacoComponentes;

        $this->pdf->linha_relativa($esquerda, $this->page_y, 0, $altura);
      }

      $this->page_y += $altura;

      if ($this->page_y > $this->pdf->altura - $altura * 2) {
        $this->pdf->ClosePage();
        $this->pdf->OpenPage();
        $this->addCabecalho();
      }
    }
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
    $this->page_y    = 30;
    $fonte           = 'arial';
    $corTexto        = '#000000';
    $esquerda        = $esquerda_original = 3;
    $espessura_linha = 0.3;
    $tam_texto       = 9;
    $direita         = 834;
    $altura          = 20;

    // Cabeçalho
    $logo = $config->get($config->logo, 'imagens/brasao.gif');

    $this->pdf->quadrado_relativo($esquerda, $this->page_y, 834, 85);
    $this->pdf->insertImageScaled('gif', $logo, 50, 95, 41);

    // Título principal
    $titulo = $config->get($config->titulo, 'i-Educar');
    $this->pdf->escreve_relativo($titulo, 30, 30, 782, 80,
      $fonte, 18, $corTexto, 'center');
    $this->pdf->escreve_relativo(date('d/m/Y'), 745, 30, 100, 80, $fonte, 12,
      $corTexto, 'left');

    // Dados escola
    $this->pdf->escreve_relativo('Instituição: ' . $this->nm_instituicao, 120, 52,
      300, 80, $fonte, 9, $corTexto, 'left' );
    $this->pdf->escreve_relativo('Escola: ' . $this->nm_escola,132, 64, 300, 80,
      $fonte, 9, $corTexto, 'left' );

    $this->pdf->escreve_relativo('Ata de Resultado Final - ' . $this->ano, 30,
      78, $direita, 80, $fonte, 12, $corTexto, 'center');

    $this->pdf->quadrado_relativo($esquerda, $this->page_y += 100, $direita, $altura);

    $this->pdf->escreve_relativo('Disciplina', $esquerda + 30, $this->page_y + 1,
      150, 50, $fonte, 9, $corTexto, 'center');

    $this->pdf->escreve_relativo('Carga Horária', $esquerda + 35, $this->page_y + 10,
      150, 50, $fonte, 7, $corTexto, 'center');

    $this->page_y += $altura;

    $this->pdf->quadrado_relativo($esquerda, $this->page_y, $direita, $altura);

    $this->pdf->escreve_relativo('Matrícula', $esquerda, $this->page_y + 2, 45,
      45, $fonte, $tam_texto, $corTexto, 'center');

    $this->pdf->linha_relativa($esquerda += 45, $this->page_y, 0, $altura);

    $espaco_nome = 150;

    $this->pdf->escreve_relativo('Nome do Aluno', $esquerda, $this->page_y + 2,
      $espaco_nome, 45, $fonte, $tam_texto, $corTexto, 'center');

    $this->pdf->linha_relativa($esquerda += $espaco_nome, $this->page_y, 0, $altura);

    $this->pdf->escreve_relativo('Situação', $esquerda, $this->page_y + 2, 45,
      45, $fonte, $tam_texto, $corTexto, 'center');

    $this->pdf->linha_relativa($esquerda += 45, $this->page_y, 0, $altura);

    // Gera o header da tabela e calcula a carga horária global
    $carga_global = $this->getComponentesTableHeader($esquerda, $direita - $esquerda);

    $nm_curso = sprintf('Curso: %s                    Série: %s     Turma: %s     Dias Letivos: %d          Carga Global: %s             Turno: %s',
      $this->nm_curso, $this->nm_serie, $this->nm_turma, $this->dias_letivos, $carga_global, $this->nm_turno);

    $this->pdf->quadrado_relativo($esquerda_original, $this->page_y, $direita, $altura);

    $this->pdf->escreve_relativo($nm_curso, $esquerda_original + 10, $this->page_y + 5,
      $direita, 50, $fonte, $tam_texto);

    $this->page_y += $altura;
  }

  function buscaDiasLetivos()
  {
    $obj_calendario = new clsPmieducarEscolaAnoLetivo();
    $lista_calendario = $obj_calendario->lista($this->ref_cod_escola, $this->ano,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL);

    $totalDiasUteis = 0;
    $total_semanas  = 0;

    $obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
    $obj_ano_letivo_modulo->setOrderby('data_inicio asc');

    $lst_ano_letivo_modulo = $obj_ano_letivo_modulo->lista($this->ano,
      $this->ref_cod_escola, NULL, NULL);

    if ($lst_ano_letivo_modulo) {
      $inicio = $lst_ano_letivo_modulo['0'];
      $fim    = $lst_ano_letivo_modulo[count($lst_ano_letivo_modulo) - 1];

      $mes_inicial = explode('-', $inicio['data_inicio']);
      $mes_inicial = $mes_inicial[1];
      $dia_inicial = $mes_inicial[2];

      $mes_final = explode('-', $fim['data_fim']);
      $mes_final = $mes_final[1];
      $dia_final = $mes_final[2];
    }

    for ($mes = $mes_inicial;$mes <= $mes_final; $mes++) {
      $obj_calendario_dia = new clsPmieducarCalendarioDia();

      $lista_dias = $obj_calendario_dia->lista($calendario['cod_calendario_ano_letivo'],
        $mes, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

      $dias_mes = array();

      if ($lista_dias) {
        foreach ($lista_dias as $dia) {
          $obj_motivo = new clsPmieducarCalendarioDiaMotivo($dia['ref_cod_calendario_dia_motivo']);

          $det_motivo = $obj_motivo->detalhe();
          $dias_mes[$dia['dia']] = strtolower($det_motivo['tipo']);
        }
      }

      //Dias previstos do mes

      // Qual o primeiro dia do mes
      $primeiroDiaDoMes = mktime(0, 0, 0, $mes, 1, $this->ano);

      // Quantos dias tem o mes
      $NumeroDiasMes = date('t', $primeiroDiaDoMes);

      //informacoes primeiro dia do mes
      $dateComponents = getdate($primeiroDiaDoMes);

      // What is the name of the month in question?
      $NomeMes = $mesesDoAno[$dateComponents['mon']];

      // What is the index value (0-6) of the first day of the
      // month in question.
      $DiaSemana = $dateComponents['wday'];

      //total de dias uteis + dias extra-letivos - dia nao letivo - fim de semana
      $DiaSemana = 0;

      if ($mes == $mes_inicial) {
        $dia_ini = $dia_inicial;
      }
      elseif ($mes == $mes_final) {
        $dia_ini = $dia_final;
      }
      else {
        $dia_ini = 1;
      }

      for ($dia = $dia_ini; $dia <= $NumeroDiasMes; $dia++) {
        if($DiaSemana >= 7) {
          $DiaSemana = 0;
          $total_semanas++;
        }

        if ($DiaSemana != 0 && $DiaSemana != 6) {
          if (!(key_exists($dia, $dias_mes) && $dias_mes[$dia] == strtolower('n'))) {
            $totalDiasUteis++;
          }
        }
        elseif (key_exists($dia, $dias_mes) && $dias_mes[$dia] == strtolower('e')) {
          $totalDiasUteis++;
        }

        $DiaSemana++;
      }
    }

    $this->dias_letivos = $totalDiasUteis;
  }

  function rodape()
  {
    $texto     = '';
    $esquerda  = 3;
    $altura    = 22;
    $direita   = 834;
    $tam_texto = 10;
    $this->pdf->escreve_relativo('Legenda', $esquerda + 2, $this->page_y += 30,
      150, 100, $fonte, $tam_texto);

    if (count($this->componentes) > 10) {
      $legenda_por_linha = TRUE;
    } else {
      $legenda_por_linha = FALSE;
    }

    foreach ($this->componentes as $componente) {
      $texto .= $componente->abreviatura . ': ' . $componente->nome . '        ';

      if ($legenda_por_linha) {
        $this->pdf->escreve_relativo($texto, $esquerda + 2, $this->page_y += $altura,
          $direita, 200, $fonte, $tam_texto + 1);

        $texto = '';

        if ($this->page_y > $this->pdf->altura - 50) {
          $texto     = '';
          $esquerda  = 3;
          $altura    = 18;
          $direita   = 834;
          $tam_texto = 10;
          $this->pdf->ClosePage();
          $this->pdf->OpenPage();
          $this->addCabecalho();
        }
      }
    }

    if (! $legenda_por_linha) {
      $this->pdf->escreve_relativo($texto, $esquerda + 2, $this->page_y += $altura,
        $direita, 200, $fonte, $tam_texto + 1);
    }

    if ($this->page_y + $altura * 2 > $this->pdf->altura - 50) {
      $texto     = '';
      $esquerda  = 3;
      $altura    = 18;
      $direita   = 834;
      $tam_texto = 10;
      $this->pdf->ClosePage();
      $this->pdf->OpenPage();
      $this->addCabecalho();
    }

    $this->pdf->quadrado_relativo($esquerda, $this->page_y += $altura * 2, $direita, 60);

    $this->pdf->escreve_relativo('Observações:', $esquerda + 1, $this->page_y + 1, 150, 200, $fonte, $tam_texto);

    $this->pdf->linha_relativa($esquerda + 200, $this->page_y += 120, 150, 0);

    $this->pdf->escreve_relativo('Assinatura do(a) Secretário(a)', $esquerda + 220,
      $this->page_y + 2, 150, 200, $fonte, 7);

    $this->pdf->linha_relativa($esquerda + 450, $this->page_y, 150, 0);

    $this->pdf->escreve_relativo('Assinatura do(a) Diretor(a)', $esquerda + 480,
      $this->page_y + 2, 150, 200, $fonte, 7);
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