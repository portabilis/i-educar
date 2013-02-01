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
 * @author      Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  Relatorio
 * @subpackage  ReservaVaga
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
  function Formular() {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Diário de Classe - Avalia&ccedil;&otilde;es');
    $this->processoAp = '670';
    $this->renderMenu = FALSE;
    $this->renderMenuSuspenso = FALSE;
  }
}

/**
 * Cria um documento PDF com o atesto de reserva de vaga.
 *
 * @author      Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  Relatorio
 * @subpackage  ReservaVaga
 * @since       Classe disponível desde a versão 1.0.0
 * @version     $Id$
 */
class indice extends clsCadastro
{
  /**
   * Referência a usuário da sessão
   * @var int
   */
  var $pessoa_logada;

  // Atributos para referências a tabelas relacionadas.
  var
    $ref_cod_instituicao,
    $ref_cod_escola,
    $ref_cod_serie,
    $ref_cod_turma,
    $ref_cod_matricula;

  // Atributos utilizados na criação do documento.
  var
    $nm_escola,
    $nm_instituicao,
    $ref_cod_curso,
    $pdf,
    $nm_turma,
    $nm_serie,
    $nm_aluno,
    $nm_ensino,
    $nm_curso,
    $data_solicitacao,
    $escola_municipio;

  /**
   * Distância horizontal da página (eixo y).
   * @var int
   */
  var $page_y = 139;

  /**
   * Caminho para o download do arquivo.
   * @var string
   */
  var $get_link;

  /**
   * Array associativo com os meses do ano.
   * @var array
   */
  var $meses_do_ano = array(
    '1'  => 'JANEIRO',
    '2'  => 'FEVEREIRO',
    '3'  => 'MAR&Ccedil;O',
    '4'  => 'ABRIL',
    '5'  => 'MAIO',
    '6'  => 'JUNHO',
    '7'  => 'JULHO',
    '8'  => 'AGOSTO',
    '9'  => 'SETEMBRO',
    '10' => 'OUTUBRO',
    '11' => 'NOVEMBRO',
    '12' => 'DEZEMBRO'
  );

  /**
   * Sobrescreve clsCadastro::renderHTML().
   * @see clsCadastro::renderHTML()
   */
  function renderHTML()
  {
    $ok = FALSE;
    $obj_reserva_vaga = new clsPmieducarReservaVaga();
    $this->cod_reserva_vaga = $_GET['cod_reserva_vaga'];
    $lst_reserva_vaga = $obj_reserva_vaga->lista($this->cod_reserva_vaga);
    $registro = array_shift($lst_reserva_vaga);

    if (is_numeric($_GET['cod_reserva_vaga']) && is_array($registro)) {
      $this->data_solicitacao = $registro['data_cadastro'];
      $ok = TRUE;
    }

    if (!$ok) {
      echo "<script>alert('Não é possível gerar documento para reserva de vaga para esta matrícula');window.location='educar_index.php';</script>";
      die('Não é possível gerar documento para reserva de vaga para esta matrícula');
    }

    // Nome do aluno
    if ($registro['nm_aluno']) {
      $this->nm_aluno = $registro['nm_aluno'];
    }
    elseif ($registro['ref_cod_aluno']) {
      $obj_aluno = new clsPmieducarAluno();
      $det_aluno = array_shift($obj_aluno->lista($registro['ref_cod_aluno']));
      $this->nm_aluno = $det_aluno['nome_aluno'];
    }

    // Nome da escola
    $obj_escola = new clsPmieducarEscola($registro['ref_ref_cod_escola']);
    $det_escola = $obj_escola->detalhe();
    $this->nm_escola = $det_escola['nome'];

    // Cidade da escola
    $escolaComplemento = new clsPmieducarEscolaComplemento($registro['ref_ref_cod_escola']);
    $escolaComplemento = $escolaComplemento->detalhe();
    $this->escola_municipio = $escolaComplemento['municipio'];

    // Nome da série
    $obj_serie = new clsPmieducarSerie($registro['ref_ref_cod_serie']);
    $det_serie = $obj_serie->detalhe();
    $this->nm_serie = $det_serie['nm_serie'];

    // Nome do curso
    $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
    $det_curso = $obj_curso->detalhe();
    $this->nm_curso = $det_curso['nm_curso'];

    $fonte    = 'arial';
    $corTexto = '#000000';

    $this->pdf = new clsPDF('Diário de Classe - '. $this->ano,
      "Diário de Classe - {$this->meses_do_ano[$this->mes]} e {$this->meses_do_ano[$prox_mes]} de {$this->ano}",
      'A4', '', FALSE, FALSE);

    $this->pdf->OpenPage();
    $this->addCabecalho();

    // Título
    $this->pdf->escreve_relativo('Reserva de Vaga', 30, 220, 535, 80, $fonte, 16,
      $corTexto, 'justify');

    $texto = "Atesto para os devidos fins que o aluno {$this->nm_aluno}, solicitou reserva de vaga na escola {$this->nm_escola}, para o curso {$this->nm_curso}, na série {$this->nm_serie} e que a mesma possui a validade de 48 horas a partir da data de solicitação da mesma, " . dataFromPgToBr($this->data_solicitacao) . ".";
    $this->pdf->escreve_relativo($texto, 30, 350, 535, 80, $fonte, 14, $corTexto, 'center');

    $mes  = date('n');
    $mes  = strtolower($this->meses_do_ano["{$mes}"]);
    $data = date('d') . " de $mes de " . date('Y');
    $this->pdf->escreve_relativo($this->escola_municipio . ', ' . $data, 30, 600, 535, 80, $fonte, 14, $corTexto, 'center');
    $this->rodape();
    $this->pdf->CloseFile();

    $this->get_link = $this->pdf->GetLink();

    echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=".$this->get_link."'}</script>";

    echo "
      <center><a target='blank' href='" . $this->get_link  . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>Clique aqui para visualizar o arquivo!</a><br><br>
        <span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>
          Clique na Imagem para Baixar o instalador<br><br>
          <a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
        </span>
      </center>";

    return;
  }

  /**
   * Sobrescreve clsCadastro::Novo().
   * @see clsCadastro::Novo()
   */
  function Novo()
  {
    return TRUE;
  }

  /**
   * Adiciona um cabeçalho ao documento.
   */
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

    $this->pdf->quadrado_relativo(30, $altura, 535, 85);
    $this->pdf->insertImageScaled('gif', $logo, 50, 95, 41);

    // Título principal
    $titulo = $config->get($config->titulo, 'i-Educar');
    $this->pdf->escreve_relativo($titulo, 30, 45, 535, 80, $fonte, 18,
      $corTexto, 'center');
    $this->pdf->escreve_relativo("Secretaria Municipal da Educação", 30, 65,
      535, 80, $fonte, 12, $corTexto, 'center');

    $obj = new clsPmieducarSerie();
    $obj->setOrderby('cod_serie,etapa_curso');
    $lista_serie_curso = $obj->lista(NULL, NULL, NULL, $this->ref_cod_curso,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_instituicao);

    $dataAtual = date('d/m/Y');
    $this->pdf->escreve_relativo('Data: ' . $dataAtual, 480, 100, 535, 80,
      $fonte, 10, $corTexto, 'left');
  }

  /**
   * Adiciona uma linha para assinatura do documento.
   */
  function rodape()
  {
    $corTexto = '#000000';
    $this->pdf->escreve_relativo('Assinatura do(a) secretário(a)', 398, 715,
      150, 50, $fonte, 9, $corTexto, 'left');
    $this->pdf->linha_relativa(385, 710, 140, 0);
  }

  /**
   * Sobrescreve clsCadastro::Editar().
   * @see clsCadastro::Editar()
   */
  function Editar()
  {
    return FALSE;
  }

  /**
   * Sobrescreve clsCadastro::Excluir().
   * @see clsCadastro::Excluir()
   */
  function Excluir()
  {
    return FALSE;
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();