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
 * @subpackage  pmieducar
 * @subpackage  Matricula
 * @subpackage  Rematricula
 * @since       Arquivo disponível desde a versão 1.0.0
 * @todo        Refatorar a lógica de indice::Novo() para uma classe na camada de domínio
 * @version     $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar');
    $this->processoAp = '561';
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;

  function Inicializar()
  {
    $retorno = 'Novo';
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    return $retorno;
  }

  function Gerar()
  {
    $instituicao_obrigatorio        = TRUE;
    $escola_obrigatorio             = TRUE;
    $curso_obrigatorio              = TRUE;
    $escola_curso_serie_obrigatorio = TRUE;
    $turma_obrigatorio              = TRUE;
    $get_escola                     = TRUE;
    $get_curso                      = TRUE;
    $get_escola_curso_serie         = TRUE;
    $get_turma                      = TRUE;
    $get_cursos_nao_padrao          = FALSE;

    include 'include/pmieducar/educar_campo_lista.php';
  }

  /**
   * @todo Refatorar a lógica para uma classe na camada de domínio.
   */
  function Novo()
  {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $db  = new clsBanco();
    $db2 = new clsBanco();

    // Seleciona o maior ano letivo da escola em andamento
    $ano = $db2->CampoUnico(sprintf("
      SELECT MAX(ano) FROM pmieducar.escola_ano_letivo
      WHERE ref_cod_escola = '%d' AND andamento = 1", $this->ref_cod_escola)
    );

    // Caso a escola não tenha um ano letivo, usa o ano da data do servidor web
    if (! is_numeric($ano)) {
      $ano = date('Y');
    }

    // Seleciona todos os alunos que foram aprovados na turma/série/curso/escola informados
	// Query abaixo foi revisada pela Portabilis em 22/02/2011 para não permitir duplicar as matrículas caso já existam no ano letivo subsequente
    $db->Consulta(sprintf("
      SELECT
        cod_matricula, ref_cod_aluno
      FROM
        pmieducar.matricula m, pmieducar.matricula_turma
      WHERE
        aprovado = '1' AND m.ativo = '1' AND ref_ref_cod_escola = '%d' AND
        ref_ref_cod_serie='%d' AND ref_cod_curso = '%d' AND
        cod_matricula = ref_cod_matricula AND ref_cod_turma = '%d' AND
		NOT EXISTS(select 1 from pmieducar.matricula m2 where 
			m2.ref_cod_aluno = m.ref_cod_aluno AND
			m2.ano = '%d' AND
			m2.ativo <> 0 AND
			m2.ref_ref_cod_escola = m.ref_ref_cod_escola)
							
		",
      $this->ref_cod_escola, $this->ref_ref_cod_serie, $this->ref_cod_curso, $this->ref_cod_turma, $ano)
    );

    while ($db->ProximoRegistro()) {
      list($cod_matricula, $ref_cod_aluno) = $db->Tupla();

      // Seleciona a série da sequência de séries
      $prox_mod = $db2->campoUnico(sprintf(
        "SELECT
          ref_serie_destino
        FROM
          pmieducar.sequencia_serie
        WHERE
          ref_serie_origem = '%d' AND ativo = '1'", $this->ref_ref_cod_serie)
      );

      // Seleciona o código do curso da série de sequência
      $ref_cod_curso = $db2->CampoUnico(sprintf("SELECT ref_cod_curso FROM pmieducar.serie WHERE cod_serie = %d", $prox_mod));

      if (is_numeric($prox_mod)) {
        // Atualiza a matrícula atual do aluno, para evitar que seja listada no cadastro deste
        $db2->Consulta(sprintf("UPDATE pmieducar.matricula SET ultima_matricula = '0' WHERE cod_matricula = '%d'", $cod_matricula));

        // Cria uma nova matrícula
        $db2->Consulta(sprintf("
          INSERT INTO pmieducar.matricula
            (ref_ref_cod_escola, ref_ref_cod_serie, ref_usuario_cad, ref_cod_aluno, aprovado, data_cadastro, ano, ref_cod_curso, ultima_matricula)
          VALUES
            ('%d', '%d', '%d', '%d', '3', 'NOW()', '%d', '%d', '1')",
          $this->ref_cod_escola, $prox_mod, $this->pessoa_logada, $ref_cod_aluno, $ano, $ref_cod_curso)
        );
      }	  	  
    }

    // Seleciona todos os alunos que foram reprovados na turma/série/curso/escola informados
	// Query abaixo foi revisada pela Portabilis em 22/02/2011 para não permitir duplicar as matrículas caso já existam no ano letivo subsequente
    $db->Consulta(sprintf("
      SELECT
        cod_matricula, ref_cod_aluno, ref_ref_cod_serie
      FROM
        pmieducar.matricula m, pmieducar.matricula_turma
      WHERE
        aprovado = '2' AND ref_ref_cod_escola = '%d' AND ref_ref_cod_serie='%d' AND cod_matricula = ref_cod_matricula AND ref_cod_turma = '%d' AND
		NOT EXISTS(select 1 from pmieducar.matricula m2 where 
			m2.ref_cod_aluno = m.ref_cod_aluno AND
			m2.ano = '%d' AND
			m2.ativo <> 0 AND
			m2.ref_ref_cod_escola = ref_ref_cod_escola)			
		",
      $this->ref_cod_escola, $this->ref_ref_cod_serie, $this->ref_cod_turma, $ano)
    );

    // Cria uma nova matrícula para cada aluno reprovado na mesma série/curso/escola informados
    while ($db->ProximoRegistro()) {
      list($cod_matricula, $ref_cod_aluno, $ref_cod_serie) = $db->Tupla();

      $db2->Consulta(sprintf("UPDATE pmieducar.matricula SET ultima_matricula = '0'
        WHERE cod_matricula = '%d'", $cod_matricula));

      $db2->Consulta(
        sprintf("INSERT INTO pmieducar.matricula
          (ref_ref_cod_escola, ref_ref_cod_serie, ref_usuario_cad, ref_cod_aluno, aprovado, data_cadastro, ano, ref_cod_curso, ultima_matricula)
        VALUES
          ('%d', '%d', '%d', '%d', '3', 'NOW()', '%d', '%d', '1')",
        $this->ref_cod_escola, $ref_cod_serie, $this->pessoa_logada, $ref_cod_aluno, $ano, $this->ref_cod_curso)
      );
    }

    $this->mensagem = "Rematrícula efetuada com sucesso!";
    return TRUE;
  }

  function Editar() {
    return TRUE;
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
?>
<script type="text/javascript">
document.getElementById('ref_cod_escola').onchange = function() {
  getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function() {
  getEscolaCursoSerie();
}

document.getElementById('ref_ref_cod_serie').onchange = function() {
  getTurma();
}
</script>