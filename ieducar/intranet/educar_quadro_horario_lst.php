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
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

use iEducar\Support\Navigation\Breadcrumb;
use Illuminate\Support\Facades\Session;

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'include/localizacaoSistema.php';
require_once 'include/pmieducar/clsPmieducarEscolaUsuario.inc.php';

/**
 * clsIndexBase class.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Quadro de Horário');
    $this->processoAp = "641";
  }
}

/**
 * indice class.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice
{
  var $pessoa_logada;
  var $titulo;
  var $limite;
  var $offset;
  var $cod_calendario_ano_letivo;
  var $ref_cod_escola;
  var $ref_cod_curso;
  var $ref_cod_serie;
  var $ref_cod_turma;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ano;
  var $data_cadastra;
  var $data_exclusao;
  var $ativo;
  var $ref_cod_instituicao;
  var $busca;

  function renderHTML()
  {
    $retorno = '';

    $this->pessoa_logada = Session::get('id_pessoa');

    $obj_permissoes = new clsPermissoes();

    if ($obj_permissoes->nivel_acesso($this->pessoa_logada) > 7)
    {
      $retorno .= '
        <table width="100%" height="40%" cellspacing="1" cellpadding="2" border="0" class="tablelistagem">
          <tbody>
            <tr>
              <td colspan="2" valig="center" height="50">
                <center class="formdktd">Usuário sem permissão para acessar esta página</center>
              </td>
            </tr>
          </tbody>
        </table>';

      return $retorno;
    }

    app(Breadcrumb::class)->current('Quadros de horários', [
        url('intranet/educar_servidores_index.php') => 'Servidores',
    ]);

    $retorno .= '
      <table width="100%" cellspacing="1" cellpadding="2" border="0" class="tablelistagem">
        <tbody>';

    if ($_POST) {
      $this->ref_cod_turma       = $_POST['ref_cod_turma'] ? $_POST['ref_cod_turma'] : NULL;
      $this->ref_cod_serie       = $_POST['ref_cod_serie'] ? $_POST['ref_cod_serie'] : NULL;
      $this->ref_cod_curso       = $_POST['ref_cod_curso'] ? $_POST['ref_cod_curso'] : NULL;
      $this->ref_cod_escola      = $_POST['ref_cod_escola'] ? $_POST['ref_cod_escola'] : NULL;
      $this->ref_cod_instituicao = $_POST['ref_cod_instituicao'] ? $_POST['ref_cod_instituicao'] : NULL;
      $this->ano                 = $_POST['ano'] ? $_POST['ano'] : NULL;
      $this->busca               = $_GET['busca'] ? $_GET['busca'] : NULL;
    }
    else {
      if ($_GET) {
        // Passa todos os valores obtidos no GET para atributos do objeto
        foreach( $_GET as $var => $val) {
          $this->$var = $val === '' ? NULL : $val;
        }
      }
    }

    $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

    if (!$this->ref_cod_escola) {
      $this->ref_cod_escola = $obj_permissoes->getEscola($this->pessoa_logada);
    }

    if (!is_numeric($this->ref_cod_instituicao)) {
      $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
    }

    // Componente curricular
    $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();

    $obrigatorio     = FALSE;
    $get_instituicao = TRUE;
    $get_escola      = TRUE;
    $get_ano         = TRUE;
    $get_curso       = TRUE;
    $get_serie       = TRUE;
    $get_turma       = TRUE;
    include 'educar_quadro_horarios_pesquisas.php';

    if ($this->busca == 'S') {
      if (is_numeric( $this->ref_cod_turma)) {
        $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
        $det_turma = $obj_turma->detalhe();

        $obj_quadro = new clsPmieducarQuadroHorario(NULL, NULL, NULL,
          $this->ref_cod_turma, NULL, NULL, 1);
        $det_quadro = $obj_quadro->detalhe();

        if (is_array($det_quadro)) {
          $quadro_horario = "<table class='calendar' cellspacing='0' cellpadding='0' border='0'>
                            <tr>
                              <td class='cal_esq_qh' width='40px'><i class='fa fa-calendar' aria-hidden='true'></i></td>
                              <td width='100%' class='mes'>Turma: {$det_turma["nm_turma"]}</td>
                              <td align='right' class='cal_dir'>&nbsp;</td>
                              </tr>
                            <tr>
                              <td colspan='3'  align='center'>
                                <table width='100%' cellspacing='2' cellpadding='0'  border='0' >
                                  <tr class='header'>
                                    <td style='width: 100px;'>DOM</td>
                                    <td style='width: 100px;'>SEG</td>
                                    <td style='width: 100px;'>TER</td>
                                    <td style='width: 100px;'>QUA</td>
                                    <td style='width: 100px;'>QUI</td>
                                    <td style='width: 100px;'>SEX</td>
                                    <td style='width: 100px;'>SAB</td>
                                  </tr>";
          $texto = '<tr>';

          for ($c = 1; $c <= 7; $c++) {
            $obj_horarios = new clsPmieducarQuadroHorarioHorarios();
            $resultado    = $obj_horarios->retornaHorario($this->ref_cod_instituicao,
              $this->ref_cod_escola, $this->ref_cod_serie, $this->ref_cod_turma, $c);

            $texto .= "<td valign=top align='center' width='100' style='cursor: pointer; ' onclick='envia( this, {$this->ref_cod_turma}, {$this->ref_cod_serie}, {$this->ref_cod_curso}, {$this->ref_cod_escola}, {$this->ref_cod_instituicao}, {$det_quadro["cod_quadro_horario"]}, {$c}, {$this->ano} );'>";

            if (is_array($resultado)) {
              $resultado = $this->organizarHorariosIguais($resultado);
              foreach ($resultado as $registro) {
                if($registro['ref_cod_disciplina'] == 0){
                  $componente->abreviatura = 'EDUCAÇÃO INFANTIL';
                }else{
                  $componente = $componenteMapper->find($registro['ref_cod_disciplina']);
                }

                // Servidor
                $obj_servidor = new clsPmieducarServidor();

                if ($registro['ref_servidor_substituto']) {
                  $det_servidor = array_shift($obj_servidor->lista(
                    $registro['ref_servidor_substituto'], NULL, NULL, NULL, NULL, NULL, NULL,
                    NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, TRUE));
                } else {
                  $det_servidor = array_shift($obj_servidor->lista(
                    $registro['ref_servidor'], NULL, NULL, NULL, NULL, NULL, NULL,
                    NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, TRUE));
                }

                $det_servidor['nome'] = array_shift(explode(' ',$det_servidor['nome']));

                //$texto .= "<div  style='text-align: center;background-color: #F6F6F6;font-size: 11px; width: 100px; margin: 3px; border: 1px solid #CCCCCC; padding:5px; '>". substr($registro['hora_inicial'], 0, 5) . ' - ' . substr($registro['hora_final'], 0, 5) . " <br> {$componente->abreviatura} <br> {$det_servidor["nome"]}</div>";
                $detalhes = sprintf("%s - %s<br />%s<br />%s",
                  substr($registro['hora_inicial'], 0, 5), substr($registro['hora_final'], 0, 5),
                  $componente->abreviatura, $det_servidor['nome']);

                $texto .= sprintf('<div class="horario">%s</div>',
                  $detalhes);
              }
            }
            else {
              $texto .= "<div  class='horario'><i class='fa fa-plus-square' aria-hidden='true'></i></div>";
            }

            $texto .= '</td>';
          }

          $texto .= '<tr><td colspan="7">&nbsp;</td></tr>';
          $quadro_horario .= $texto;

          $quadro_horario .= '</table></td></tr></table>';
          $retorno .= "<tr><td colspan='2' ><center><b></b>{$quadro_horario}</center></td></tr>";
        }
        else {
          $retorno .= "<tr><td colspan='2' ><b><center>N&atilde;o existe nenhum quadro de hor&aacute;rio cadastrado para esta turma.</center></b></td></tr>";
        }
      }
    }

    if ($obj_permissoes->permissao_cadastra(641, $this->pessoa_logada, 7)) {
      $retorno .= "<tr><td>&nbsp;</td></tr><tr>
            <td align=\"center\" colspan=\"2\">";

      if (!$det_quadro) {
        $retorno .= "<input type=\"button\" value=\"Novo Quadro de Hor&aacute;rios\" onclick=\"window.location='educar_quadro_horario_cad.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ano={$this->ano}'\" class=\"botaolistagem\"/>";
      }
      else {
        if ($obj_permissoes->permissao_excluir(641, $this->pessoa_logada, 7))
          $retorno .= "<input type=\"button\" value=\"Excluir Quadro de Hor&aacute;rios\" onclick=\"window.location='educar_quadro_horario_cad.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ano={$this->ano}&ref_cod_quadro_horario={$det_quadro["cod_quadro_horario"]}'\" class=\"botaolistagem btn-green\"/>";
      }

      $retorno .= "</td>
            </tr>";
    }

    $retorno .='</tbody>
      </table>';

    return $retorno;
  }
  function organizarHorariosIguais($valores){
    $x = 1;
    $quantidadeElementos = count($valores);
    while ($x < $quantidadeElementos) {
        $mesmoHorario = (($valores[0]['hora_inicial'] == $valores[$x]['hora_inicial']) &&
                         ($valores[0]['hora_final'] == $valores[$x]['hora_final']));

        if($mesmoHorario){
          unset($valores[$x]);
          $valores[0]['ref_cod_disciplina'] = 0;
        }
        $x++;
    }
    return $valores;
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
var campoInstituicao = document.getElementById('ref_cod_instituicao');
var campoEscola = document.getElementById('ref_cod_escola');
var campoCurso = document.getElementById('ref_cod_curso');
var campoSerie = document.getElementById('ref_cod_serie');
var campoTurma = document.getElementById('ref_cod_turma');
var campoAno   = document.getElementById('ano');

campoInstituicao.onchange = function()
{
  var campoInstituicao_ = document.getElementById('ref_cod_instituicao').value;

  campoEscola.length = 1;
  campoEscola.disabled = true;
  campoEscola.options[0].text = 'Carregando escola';

  campoCurso.length = 1;
  campoCurso.disabled = true;
  campoCurso.options[0].text = 'Selecione uma escola antes';

  campoSerie.length = 1;
  campoSerie.disabled = true;
  campoSerie.options[0].text = 'Selecione um curso antes';

  campoTurma.length = 1;
  campoTurma.disabled = true;
  campoTurma.options[0].text = 'Selecione uma Série antes';

  var xml_escola = new ajax(getEscola);
  xml_escola.envia('educar_escola_xml2.php?ins=' + campoInstituicao_);
};

campoEscola.onchange = function()
{
  var campoEscola_ = document.getElementById( 'ref_cod_escola' ).value;

  campoAno.length = 1;
  campoAno.disabled = true;
  campoAno.options[0].text = 'Selecione uma escola antes';

  campoCurso.length = 1;
  campoCurso.disabled = true;
  campoCurso.options[0].text = 'Carregando curso';

  campoSerie.length = 1;
  campoSerie.disabled = true;
  campoSerie.options[0].text = 'Selecione um curso antes';

  campoTurma.length = 1;
  campoTurma.disabled = true;
  campoTurma.options[0].text = 'Selecione uma série antes';

  var xml_curso = new ajax(getCurso);
  xml_curso.envia('educar_curso_xml.php?esc=' + campoEscola_);

  var xml_ano = new ajax(getAnoLetivo);
  xml_ano.envia('educar_escola_ano_letivo_xml.php?esc=' + campoEscola_);
};

campoCurso.onchange = function()
{
  var campoEscola_ = document.getElementById('ref_cod_escola').value;
  var campoCurso_ = document.getElementById('ref_cod_curso').value;

  campoSerie.length = 1;
  campoSerie.disabled = true;
  campoSerie.options[0].text = 'Carregando série';

  campoTurma.length = 1;
  campoTurma.disabled = true;
  campoTurma.options[0].text = 'Selecione uma Série antes';

  var xml_serie = ajax(getSerie);
  xml_serie.envia('educar_escola_curso_serie_xml.php?esc=' + campoEscola_ + '&cur=' + campoCurso_);
};

campoAno.onchange = function()
{
  var campoEscola_ = document.getElementById('ref_cod_escola').value;
  var campoCurso_ = document.getElementById('ref_cod_curso').value;

  campoSerie.length = 1;
  campoSerie.disabled = true;
  campoSerie.options[0].text = 'Carregando série';

  campoTurma.length = 1;
  campoTurma.disabled = true;
  campoTurma.options[0].text = 'Selecione uma Série antes';

  var xml_serie = ajax(getSerie);
  xml_serie.envia('educar_escola_curso_serie_xml.php?esc=' + campoEscola_ + '&cur=' + campoCurso_);
};

campoSerie.onchange = function()
{
  var campoEscola_ = document.getElementById('ref_cod_escola').value;
  var campoSerie_ = document.getElementById('ref_cod_serie').value;
  var campoAno_ = document.getElementById('ano').value;

  campoTurma.length = 1;
  campoTurma.disabled = true;
  campoTurma.options[0].text = 'Carregando turma';

  var xml_turma = new ajax(getTurma);
  xml_turma.envia('educar_turma_xml.php?esc=' + campoEscola_ + '&ser=' + campoSerie_ + '&ano=' + campoAno_);
};

if (document.getElementById('botao_busca')) {
  obj_botao_busca = document.getElementById('botao_busca');
  obj_botao_busca.onclick = function()
  {
    document.formcadastro.action = 'educar_quadro_horario_lst.php?busca=S';
    acao();
  };
}

function envia(obj, var1, var2, var3, var4, var5, var6, var7, var8)
{
  var identificador = Math.round(1000000000 * Math.random());

  if (obj.innerHTML) {
    document.formcadastro.action = 'educar_quadro_horario_horarios_cad.php?ref_cod_turma=' + var1 + '&ref_cod_serie=' + var2 + '&ref_cod_curso=' + var3 + '&ref_cod_escola=' + var4 + '&ref_cod_instituicao=' + var5 + '&ref_cod_quadro_horario=' + var6 + '&dia_semana=' + var7 + '&ano=' + var8 + '&identificador=' + identificador;
    document.formcadastro.submit();
  }
  else {
    document.formcadastro.action = 'educar_quadro_horario_horarios_cad.php?ref_cod_turma=' + var1 + '&ref_cod_serie=' + var2 + '&ref_cod_curso=' + var3 + '&ref_cod_escola=' + var4 + '&ref_cod_instituicao=' + var5 + '&ref_cod_quadro_horario=' + var6 + '&dia_semana=' + var7 + '&ano=' + var8 + '&identificador=' + identificador;
    document.formcadastro.submit();
  }
}

if (document.createStyleSheet) {
  document.createStyleSheet('styles/calendario.css');
}
else {
  var objHead = document.getElementsByTagName('head');
  var objCSS = objHead[0].appendChild(document.createElement('link'));
  objCSS.rel = 'stylesheet';
  objCSS.href = 'styles/calendario.css';
  objCSS.type = 'text/css';
}
</script>
