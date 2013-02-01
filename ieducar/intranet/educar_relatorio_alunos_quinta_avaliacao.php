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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Alunos em Exame');
    $this->processoAp = 807;
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
  var $ref_cod_curso;
  var $ref_cod_serie;
  var $ref_cod_turma;

  function Inicializar()
  {
    $retorno = 'Novo';

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    return $retorno;
  }

  function Gerar()
  {
    $obj_permissoes = new clsPermissoes();
    $nivel_usuario  = $obj_permissoes->nivel_acesso($this->pessoa_logada);

    if ($_POST){
      foreach ($_POST as $key => $value) {
        $this->$key = $value;
      }
    }

    $this->campoNumero('ano', 'Ano', date('Y'), 4, 4, TRUE);

    $get_escola              = TRUE;
    $exibe_nm_escola         = TRUE;
    $get_curso               = TRUE;
    $get_escola_curso_serie  = TRUE;
    $escola_obrigatorio      = FALSE;
    $curso_obrigatorio       = TRUE;
    $instituicao_obrigatorio = TRUE;

    include 'include/pmieducar/educar_campo_lista.php';

    $this->campoLista('ref_cod_turma', 'Turma', array('' => 'Selecione'), '');

    $this->url_cancelar = 'educar_index.php';
    $this->nome_url_cancelar = 'Cancelar';

    $this->acao_enviar = 'acao2()';
    $this->acao_executa_submit = FALSE;
  }
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();

// cria o conteudo
$miolo = new indice();

// adiciona o conteudo na clsBase
$pagina->addForm($miolo);

// gera o html
$pagina->MakeAll();
?>
<script type="text/javascript">
document.getElementById('ref_cod_escola').onchange = function()
{
  getEscolaCurso();
  var campoTurma = document.getElementById('ref_cod_turma');
  getTurmaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
  getEscolaCursoSerie();
  getTurmaCurso();
}

document.getElementById('ref_ref_cod_serie').onchange = function()
{
  var campoEscola = document.getElementById('ref_cod_escola').value;
  var campoSerie = document.getElementById('ref_ref_cod_serie').value;

  var xml1 = new ajax(getTurma_XML);
  strURL = 'educar_turma_xml.php?esc=' + campoEscola + '&ser=' + campoSerie;
  xml1.envia(strURL);
}

function getTurma_XML(xml)
{
  var campoSerie = document.getElementById('ref_ref_cod_serie').value;

  var campoTurma = document.getElementById('ref_cod_turma');

  var turma = xml.getElementsByTagName('turma');

  campoTurma.length = 1;
  campoTurma.options[0] = new Option('Selecione uma Turma', '', false, false);

  for (var j = 0; j < turma.length; j++) {
    campoTurma.options[campoTurma.options.length] = new Option(
      turma[j].firstChild.nodeValue, turma[j].getAttribute('cod_turma'), false, false
    );
  }

  if (campoTurma.length == 1 && campoSerie != '') {
    campoTurma.options[0] = new Option('A série não possui nenhuma turma', '', false, false);
  }
}

function getTurmaCurso()
{
  var campoCurso = document.getElementById('ref_cod_curso').value;
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

  var xml1 = new ajax(getTurmaCurso_XML);
  strURL = 'educar_turma_xml.php?ins=' + campoInstituicao + '&cur=' + campoCurso;

  xml1.envia(strURL);
}

function getTurmaCurso_XML(xml)
{
  var turma = xml.getElementsByTagName('turma');
  var campoTurma = document.getElementById('ref_cod_turma');
  var campoCurso = document.getElementById('ref_cod_curso');

  campoTurma.length = 1;
  campoTurma.options[0] = new Option('Selecione uma Turma', '', false, false);

  for (var j = 0; j < turma.length; j++) {
    campoTurma.options[campoTurma.options.length] = new Option(
      turma[j].firstChild.nodeValue, turma[j].getAttribute('cod_turma'), false, false
    );
  }
}

function acao2()
{
  if (!acao()) {
    return;
  }

  showExpansivelImprimir(400, 200,'',[], 'Alunos em Exame');

  document.formcadastro.target = 'miolo_' + (DOM_divs.length - 1);

  document.getElementById('btn_enviar').disabled =false;

  document.formcadastro.submit();
}

document.formcadastro.action = 'educar_relatorio_alunos_quinta_avaliacao_proc.php';
</script>