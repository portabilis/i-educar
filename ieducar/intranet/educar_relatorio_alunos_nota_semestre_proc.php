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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Espelho de Notas Anual');
    $this->processoAp = 807;
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
  var $ref_cod_curso;

  var $semestre;
  var $is_padrao;

  var $ano;

  var $cursos = array();

  var $get_link;

  function renderHTML()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    if ($_POST){
      foreach ($_POST as $key => $value) {
        $this->$key = $value;
      }
    }

    if ($this->ref_ref_cod_serie) {
      $this->ref_cod_serie = $this->ref_ref_cod_serie;
    }

    $fonte    = 'arial';
    $corTexto = '#000000';

    if (empty($this->ref_cod_turma)) {
      echo '<script>
             alert("Erro ao gerar relatório!\nNenhuma turma selecionada!");
             window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
           </script>';

      return TRUE;
    }

    if ($this->ref_cod_escola){
      $obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
      $det_escola = $obj_escola->detalhe();
      $this->nm_escola = $det_escola['nome'];

      $obj_instituicao = new clsPmieducarInstituicao($det_escola['ref_cod_instituicao']);
      $det_instituicao = $obj_instituicao->detalhe();
      $this->nm_instituicao = $det_instituicao['nm_instituicao'];
    }

    $obj_calendario   = new clsPmieducarEscolaAnoLetivo();
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
      echo '<script>
             alert("Escola não possui calendário definido para este ano");
             window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
           </script>';

      return TRUE;
    }

    $obj = new clsPmieducarSerie();
    $obj->setOrderby('cod_serie,etapa_curso');
    $lista_serie_curso = $obj->lista(null, null, null, $this->ref_cod_curso,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_instituicao);

    $obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
    $det_curso = $obj_curso->detalhe();
    $this->nm_curso = $det_curso['nm_curso'];

    $obj_tipo_avaliacao = new clsPmieducarTipoAvaliacao($det_curso['ref_cod_tipo_avaliacao']);
    $det_tipo_avaliacao = $obj_tipo_avaliacao->detalhe();
    $conceitual = $det_tipo_avaliacao['conceitual'];

    $obj_matricula_turma = new clsPmieducarMatriculaTurma();
    $obj_matricula_turma->setOrderby('nome_ascii');

    if (!$this->is_padrao && is_numeric($this->semestre) && $this->ano != 2007) {
      $lst_matricula_turma = $obj_matricula_turma->lista($this->ref_cod_matricula,
        $this->ref_cod_turma, NULL, NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_serie,
        $this->ref_cod_curso, $this->ref_cod_escola, $this->ref_cod_instituicao,
        NULL, NULL, array(1,2,3), NULL, NULL, $this->ano, NULL, NULL, NULL, NULL,
        TRUE, NULL, NULL, NULL, NULL, $this->semestre);
    }
    else {
      $lst_matricula_turma = $obj_matricula_turma->lista($this->ref_cod_matricula,
        $this->ref_cod_turma, NULL, NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_serie,
        $this->ref_cod_curso, $this->ref_cod_escola, $this->ref_cod_instituicao,
        NULL, NULL, array(1,2,3), NULL, NULL, $this->ano, NULL, NULL, NULL, NULL, TRUE);
    }

    $obj_disciplinas = new clsPmieducarEscolaSerieDisciplina();
    $lst_disciplinas = $obj_disciplinas->lista($this->ref_cod_serie,
      $this->ref_cod_escola, NULL, 1);

    // Caso o curso siga o padrão da escola
    if ($det_curso["padrao_ano_escolar"]) {
      $obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
      $lst_ano_letivo_modulo = $obj_ano_letivo_modulo->lista($this->ano, $this->ref_cod_escola);

      if (is_array($lst_ano_letivo_modulo)) {
        // Guarda a qtd de módulos a serem cursados
        $qtd_modulos = count($lst_ano_letivo_modulo);
        $segue_padrão = TRUE;
      }
    }
    // Caso o curso não siga o padrão da escola
    else {
      $obj_turma_modulo = new clsPmieducarTurmaModulo();
      $lst_turma_modulo = $obj_turma_modulo->lista($this->ref_cod_turma);

      if (is_array($lst_turma_modulo)) {
        // Guarda a qtd de módulos a serem cursados
        $qtd_modulos = count($lst_turma_modulo);

        $aux_turma_modulo = array_shift($lst_turma_modulo);
        $obj_modulo       = new clsPmieducarModulo($aux_turma_modulo['ref_cod_modulo']);
        $det_modulo       = $obj_modulo->detalhe();
        $nm_modulo        = $det_modulo['nm_tipo'];
        $segue_padrão     = FALSE;
        $mostra_cabecalho = array();
        $nm_modulo        = substr(strtoupper($nm_modulo), 0, 1);

        for ($i = 0; $i < $qtd_modulos; $i++) {
          $mostra_cabecalho[$i] = ($i + 1) . 'º' . $nm_modulo;
        }
      }
    }

    if ($lst_matricula_turma) {
      $relatorio = new relatorios('Espelho de Notas Ano - ' . $this->ano, 210,
        FALSE, 'Espelho de Notas', 'A4',
        "{$this->nm_instituicao}\n{$this->nm_escola}\n{$this->nm_curso}\n{$this->nm_serie} -  Turma: $this->nm_turma         " . date('d/m/Y'));

      $relatorio->setMargem(20, 20, 20, 20);
      $relatorio->exibe_produzido_por = FALSE;

      $db = new clsBanco();

      if (!$conceitual) {
        $campo_nota = 'COALESCE(nota,valor) ';
      }
      else {
        $campo_nota = 'nome ';
      }

      foreach ($lst_disciplinas as $disciplina) {
        $obj_disciplina = new clsPmieducarDisciplina($disciplina['ref_cod_disciplina']);
        $det_disciplina = $obj_disciplina->detalhe();

        $relatorio->novalinha(array($det_disciplina['nm_disciplina']), 0, 16,
          TRUE, 'arial', array(400), '#515151', '#D3D3D3', '#FFFFFF', FALSE, TRUE);

        if ($segue_padrão) {
          if (!$conceitual) {
            $array_val = array('Cód. Aluno', 'Nome do Aluno', '1ºB', '2ºB', '3ºB',
              '4ºB', 'M.Parcial', 'Exame', 'M.Final', 'Faltas');
          }
          else {
            $array_val = array('Cód. Aluno', 'Nome do Aluno', '1ºB', '2ºB', '3ºB',
              '4ºB', '', '', '', 'Faltas');
          }
        }
        else {
          if (!$conceitual) {
            $array_val = array('Cód. Aluno', 'Nome do Aluno', $mostra_cabecalho[0],
              $mostra_cabecalho[1], $mostra_cabecalho[2], $mostra_cabecalho[3],
              'M.Parcial', 'Exame', 'M.Final', 'Faltas');
          }
          else {
            $array_val = array('Cód. Aluno', 'Nome do Aluno', $mostra_cabecalho[0],
              $mostra_cabecalho[1], $mostra_cabecalho[2], $mostra_cabecalho[3],
              '', '', '', 'Faltas');
          }
        }

        $relatorio->novalinha($array_val, 0, 13, TRUE, 'arial',
          array(40, 160, 30, 30, 30, 30, 55, 50, 50, 38), '#515151', '#D3D3D3',
          '#FFFFFF', FALSE, TRUE);

        foreach ($lst_matricula_turma as $matricula) {
          $consulta = "
            SELECT
              ref_cod_disciplina,
              $campo_nota AS nota,
              modulo
            FROM
              pmieducar.nota_aluno
            LEFT OUTER JOIN
              pmieducar.tipo_avaliacao_valores
            ON
              (
                ref_ref_cod_tipo_avaliacao = ref_cod_tipo_avaliacao
                AND ref_sequencial = sequencial
              )
            WHERE
              ref_cod_matricula      = {$matricula['ref_cod_matricula']}
              AND ref_cod_escola     = {$this->ref_cod_escola}
              AND ref_cod_serie      = {$this->ref_cod_serie}
              AND ref_cod_disciplina = {$disciplina['ref_cod_disciplina']}
              AND nota_aluno.ativo   = 1
            GROUP BY
              ref_cod_disciplina,
              modulo,
              $campo_nota
            ORDER BY
              modulo ASC";

          $db->Consulta($consulta);

          $media_parcial = 0;
          $registro      = NULL;
          $nota1         = '';
          $nota2         = '';
          $nota3         = '';
          $nota4         = '';
          $faltas        = '';
          $nota_exame    = '';
          $media_final   = '';

          while ($db->ProximoRegistro()) {
            $registro      = $db->Tupla();
            $variavel      = "nota{$registro['modulo']}";
            $$variavel     = $conceitual ? $registro['nota'] : number_format( $registro['nota'] ,2,'.','');
            $ultimo_modulo = $registro['modulo'];

            if (!$conceitual) {
              $media_parcial = $media_parcial + $registro['nota'];
              /**
               * nota do exame
               */
              if ($registro['modulo'] > $qtd_modulos) {
                $nota_exame = $conceitual ? '' : number_format($registro['nota'] ,2, '.', '');
              }
              else {
                $nota_exame = '';
              }
            }
            else {
              $media_parcial = '';
            }
          }

          if (!$conceitual) {
            $media_parcial =  number_format($media_parcial / $ultimo_modulo, 2, '.', '');
          }

          //exame ou total modulos
          if (($ultimo_modulo == $qtd_modulos || $ultimo_modulo - 1 == $qtd_modulos) && !$conceitual) {
            $objNotaAluno = new clsPmieducarNotaAluno();

            if ($qtd_modulos == $ultimo_modulo) {
              if (!dbBool($det_serie['ultima_nota_define'])) {
                $media_final = $objNotaAluno->getMediaAluno(
                  $matricula['ref_cod_matricula'], $disciplina['ref_cod_disciplina'],
                  $this->ref_cod_serie, $ultimo_modulo, $det_curso['media']
                );
              }
              else {
                $media_final = $objNotaAluno->getUltimaNotaModulo(
                  $matricula['ref_cod_matricula'], $disciplina['ref_cod_disciplina'],
                  $this->ref_cod_serie, $ultimo_modulo
                );
              }
            }
            else {
              $media_final = $objNotaAluno->getMediaAlunoExame(
                $matricula['ref_cod_matricula'], $disciplina['ref_cod_disciplina'],
                $this->ref_cod_serie, $ultimo_modulo - 1
              );
            }

            $media_final = number_format($media_final, 2, '.', '');
          }
          else {
            $media_final = $conceitual ? '' : '-';
          }

          $total_faltas = 0;

          if ($det_curso['falta_ch_globalizada']) {
            $obj_falta = new clsPmieducarFaltas();
            $obj_falta->setOrderby('sequencial ASC');
            $det_falta = $obj_falta->lista($matricula['ref_cod_matricula'], NULL,
              NULL, NULL, NULL, NULL);

            if (is_array($det_falta)) {
              foreach ($det_falta as $key => $value) {
                $total_faltas += $det_falta[$key]['faltas'] = $value['falta'];
              }
            }
          }
          else {
            $obj_falta = new clsPmieducarFaltaAluno();
            $obj_falta->setOrderby("cod_falta_aluno asc");

            if ($det_curso['padrao_ano_escolar'] == 1) {
              $det_falta = $obj_falta->lista(NULL, NULL, NULL, $this->ref_cod_serie,
                $this->ref_cod_escola, $disciplina['ref_cod_disciplina'],
                $matricula['ref_cod_matricula'], NULL, NULL, NULL, NULL, NULL, 1);
            }
            else {
              $det_falta = $obj_falta->lista(NULL, NULL, NULL, $this->ref_cod_serie,
                $this->ref_cod_escola, NULL, $matricula['ref_cod_matricula'],
                NULL, NULL, NULL, NULL, NULL, 1, NULL, $disciplina['ref_cod_disciplina']);
            }

            if (is_array($det_falta)) {
              foreach ($det_falta as $key => $value) {
                $total_faltas += $det_falta[$key]['faltas'];
              }
            }
          }

          if (strlen($matricula['nome']) > 30) {
            $matricula['nome'] = explode(' ', $matricula['nome']);

            if (is_array($matricula['nome'])) {
              $nome_aluno = array_shift($matricula['nome']);
            }

            if (is_array($matricula['nome'])) {
              $nome_aluno .= ' ' . array_shift($matricula['nome']);
            }

            if (is_array($matricula['nome'])) {
              $nome_aluno .= ' ' . array_pop($matricula['nome']);
            }

            $matricula['nome'] = $nome_aluno;
          }

          if ($segue_padrão || !$conceitual) {
            $relatorio->novalinha(array($matricula['ref_cod_aluno'], $matricula['nome'],
              $nota1, $nota2, $nota3, $nota4, $media_parcial, $nota_exame,
              $media_final, $total_faltas), 0, 12, FALSE, 'arial',
              array(35, 165, 30, 30, 30, 40, 55, 50, 55), '#515151', '#D3D3D3',
              '#FFFFFF', FALSE, TRUE);
          }
          else {
            $obj_matricula = new clsPmieducarMatricula($matricula["ref_cod_matricula"]);
            $situacao = $obj_matricula->detalhe();
            $situacao = $situacao["aprovado"];

            if ($situacao == 1) {
              $situacao = 'Apr.';
            }
            elseif ($situacao == 2) {
              $situacao = 'Repr;';
            }
            elseif ($situacao == 3) {
              $situacao = 'And.';
            }

            $relatorio->novalinha(array($matricula['ref_cod_aluno'], $matricula['nome'],
              $nota1, $nota2, $nota3, $nota4,  $media_parcial, $nota_exame, $media_final,
              $total_faltas, $situacao), 0, 12, FALSE, 'arial',
              array(40, 160, 30, 30, 30, 30, 55, 50, 50, 38), '#515151', '#D3D3D3',
              '#FFFFFF', FALSE, TRUE);
          }
        }

        $relatorio->quebraPagina();
      }

      $this->get_link = $relatorio->fechaPdf();
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


  function Editar()
  {
    return false;
  }

  function Excluir()
  {
    return false;
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