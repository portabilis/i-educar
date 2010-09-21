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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Resultado Final');
    $this->processoAp = 823;
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

  var $ref_cod_curso;

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
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->campoNumero('ano', 'Ano', date('Y'), 4, 4, TRUE);

    $get_escola             = TRUE;
    $obrigatorio            = TRUE;
    $exibe_nm_escola        = TRUE;
    $get_curso              = TRUE;
    $get_escola_curso_serie = TRUE;
    $get_turma              = TRUE;

    include 'include/pmieducar/educar_campo_lista.php';

    $this->url_cancelar = 'educar_index.php';
    $this->nome_url_cancelar = 'Cancelar';

    $this->acao_enviar = 'acao2()';
    $this->acao_executa_submit = FALSE;
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
function acao2()
{
  if (!acao()) {
    return false;
  }

  showExpansivelImprimir(400, 200, '', [], 'Diário de Classe');
  document.formcadastro.target = 'miolo_' + (DOM_divs.length - 1);
  document.formcadastro.submit();
}

document.formcadastro.action = 'educar_relatorio_resultado_final_proc.php';

document.getElementById('ref_cod_escola').onchange = function()
{
  getEscolaCurso();
  document.getElementById('ref_cod_curso').onchange();
}

document.getElementById('ref_cod_curso').onchange = function()
{
  getEscolaCursoSerie();
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
    campoTurma.options[campoTurma.options.length] = new Option(turma[j].firstChild.nodeValue, turma[j].getAttribute('cod_turma'), false, false);
  }

  if (campoTurma.length == 1 && campoSerie != '') {
    campoTurma.options[0] = new Option('A série não possui nenhuma turma', '', false, false);
  }
}
</script>