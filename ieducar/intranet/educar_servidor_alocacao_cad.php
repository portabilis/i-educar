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
 * @author    Haissam Yebahi <ctima@itajai.sc.gov.br>
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

/**
 * clsIndexBase class.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @author    Haissam Yebahi <ctima@itajai.sc.gov.br>
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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Servidor Alocação');
    $this->processoAp = 635;
  }
}

/**
 * indice class.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @author    Haissam Yebahi <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $pessoa_logada;
  var $cod_servidor_alocacao;
  var $ref_ref_cod_instituicao;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_escola;
  var $ref_cod_servidor;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $carga_horaria_alocada;
  var $carga_horaria_disponivel;
  var $periodo;

  var $alocacao_array          = array();
  var $alocacao_excluida_array = array();

  static $escolasPeriodos = array();
  static $periodos = array();

  function Inicializar()
  {
    $retorno = 'Novo';
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->ref_cod_servidor        = $_GET['ref_cod_servidor'];
    $this->ref_ref_cod_instituicao = $_GET['ref_cod_instituicao'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
      'educar_servidor_alocacao_lst.php');

    if (is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_ref_cod_instituicao)) {
      $obj   = new clsPmieducarServidorAlocacao();
      $lista = $obj->lista(NULL, $this->ref_ref_cod_instituicao, NULL, NULL,
        NULL, $this->ref_cod_servidor, NULL, NULL, NULL, NULL, 1, NULL, NULL);

      if ($lista) {
        foreach ($lista as $campo => $val) {
          $temp = array();
          $temp['carga_horaria_alocada'] = $val['carga_horaria'];
          $temp['periodo']               = $val['periodo'];
          $temp['ref_cod_escola']        = $val['ref_cod_escola'];
          $temp['novo']                  = 0;

          $this->alocacao_array[] = $temp;
        }

        $retorno = 'Novo';
      }

      $obj_servidor = new clsPmieducarServidor($this->ref_cod_servidor, NULL,
        NULL, NULL, NULL, NULL, 1, $this->ref_ref_cod_instituicao);
      $det_servidor = $obj_servidor->detalhe();

      $this->carga_horaria_disponivel = $det_servidor['carga_horaria'];
    }
    else {
      header('Location: educar_servidor_lst.php');
      die();
    }

    $this->url_cancelar      = sprintf(
      'educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d',
      $this->ref_cod_servidor, $this->ref_ref_cod_instituicao
    );
    $this->nome_url_cancelar = 'Cancelar';
    return $retorno;
  }

  function Gerar()
  {
    if ($_POST) {
      foreach ($_POST as $campo => $val) {
        if (is_string($val)) {
          $val = urldecode($val);
        }

        $this->$campo = ($this->$campo) ? $this->$campo : $val;
      }
    }

    $obj_inst = new clsPmieducarInstituicao($this->ref_ref_cod_instituicao);
    $inst_det = $obj_inst->detalhe();

    $this->campoRotulo('nm_instituicao', 'Instituição', $inst_det['nm_instituicao']);
    $this->campoOculto('ref_ref_cod_instituicao', $this->ref_ref_cod_instituicao);

    // Dados do servidor
    $objTemp = new clsPmieducarServidor($this->ref_cod_servidor);
    $det = $objTemp->detalhe();

    if ($det) {
      foreach ($det as $key => $registro) {
        $this->$key = $registro;
      }
    }

    if ($this->ref_cod_servidor) {
      $objTemp = new clsFuncionario($this->ref_cod_servidor);
      $detalhe = $objTemp->detalhe();
      $detalhe = $detalhe['idpes']->detalhe();
      $nm_servidor = $detalhe['nome'];
    }

    $this->campoRotulo('nm_servidor', 'Servidor', $nm_servidor);

    $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);

    if ($_POST['alocacao_array']) {
      $this->alocacao_array = unserialize(urldecode($_POST['alocacao_array']));
    }

    if ($_POST['alocacao_excluida_array']) {
      $this->alocacao_excluida_array = unserialize(urldecode($_POST['alocacao_excluida_array']));
    }

    if ($_POST['carga_horaria_alocada'] && $_POST['periodo']) {
      $aux = array();
      $aux['carga_horaria_alocada'] = $_POST['carga_horaria_alocada'];
      $aux['periodo']               = $_POST['periodo'];
      $aux['ref_cod_escola']        = $_POST['ref_cod_escola'];
      $aux['novo']                  = 1;

      $this->alocacao_array[] = $aux;

      unset($this->periodo);
      unset($this->carga_horaria_alocada);
      unset($this->ref_cod_escola);
    }

    // Exclusão
    if ($this->alocacao_array) {
      foreach ($this->alocacao_array as $key => $alocacao) {
        if (is_numeric($_POST['excluir_periodo'])) {
          if ($_POST['excluir_periodo'] == $key) {
            $this->alocacao_excluida_array[] = $alocacao;
            unset($this->alocacao_array[$key]);
            unset($this->excluir_periodo);
          }
        }
      }
    }

    // Carga horária
    $carga = $this->carga_horaria_disponivel;
    $this->campoRotulo('carga_horaria_disponivel', 'Carga Horária', $carga . ':00');

    foreach ($this->alocacao_array as $alocacao) {
      $carga_horaria_ = explode(':', $alocacao['carga_horaria_alocada']);

      $horas   += (int) $carga_horaria_[0];
      $minutos += (int) $carga_horaria_[1];
    }

    $total = ($horas * 60) + $minutos;
    $rest  = ($carga * 60) - $total;

    $total = sprintf('%02d:%02d', ($total / 60), ($total % 60));
    $rest  = sprintf('%02d:%02d', ($rest / 60), ($rest % 60));

    $this->campoRotulo('horas_utilizadas', 'Horas Utilizadas', $total);
    $this->campoRotulo('horas_restantes', 'Horas Restantes', $rest);
    $this->campoOculto('horas_restantes_', $rest);

    $this->campoQuebra();

    $this->campoOculto('excluir_periodo', '');
    unset($aux);

    // Escolas
    $obj_escola = new clsPmieducarEscola();
    $permissao  = new clsPermissoes();

    // Exibe apenas a escola ao qual o usuário de nível escola está alocado
    if (4 == $permissao->nivel_acesso($this->pessoa_logada)) {
      $lista_escola = $obj_escola->lista($permissao->getEscola($this->pessoa_logada),
        NULL, NULL, $this->ref_ref_cod_instituicao, NULL, NULL, NULL, NULL, NULL,
        NULL, 1);

      $nome_escola = $lista_escola[0]['nome'];
      $cod_escola  = $lista_escola[0]['cod_escola'];

      $this->campoTextoInv('ref_cod_escola_label', 'Escola', $nome_escola, 100, 255, FALSE);
      $this->campoOculto('ref_cod_escola', $cod_escola);
    }
    // Usuário administrador visualiza todas as escolas disponíveis
    else {
      $lista_escola = $obj_escola->lista(NULL, NULL, NULL,
        $this->ref_ref_cod_instituicao, NULL, NULL, NULL, NULL, NULL, NULL, 1);

      $opcoes = array('' => 'Selecione');

      if ($lista_escola) {
        foreach ($lista_escola as $escola) {
          $opcoes[$escola['cod_escola']] = $escola['nome'];
        }
      }

      $this->campoLista('ref_cod_escola', 'Escola', $opcoes, $this->ref_cod_escola,
        '', FALSE, '', '', FALSE, FALSE);
    }

    $periodo = array(
      1  => 'Matutino',
      2  => 'Vespertino',
      3  => 'Noturno'
    );
    self::$periodos = $periodo;

    $this->campoLista('periodo', 'Período', $periodo, $this->periodo, NULL, FALSE,
      '', '', FALSE, FALSE);

    $this->campoHora('carga_horaria_alocada', 'Carga Horária',
      $this->carga_horaria_alocada, FALSE);

    // Altera a string de descrição original do campo hora
    $this->campos['carga_horaria_alocada'][6] = sprintf('Formato hh:mm (máximo de %d horas por período)', clsPmieducarServidorAlocacao::$cargaHorariaMax);

    $this->campoOculto('alocacao_array', serialize($this->alocacao_array));

    $this->campoOculto('alocacao_excluida_array', serialize($this->alocacao_excluida_array));

    $this->campoRotulo('bt_incluir_periodo', 'Período', "<a href='#' onclick=\"if(validaHora()) { document.getElementById('incluir_periodo').value = 'S'; document.getElementById('tipoacao').value = ''; document.{$this->__nome}.submit();}\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>");

    if ($this->alocacao_array) {
      $excluir_ok = FALSE;

      if ($_POST['excluir_periodo'] || $_POST['excluir_periodo'] == '0') {
        $excluir_ok = TRUE;
      }

      foreach ($this->alocacao_array as $key => $alocacao) {
        $obj_permissoes = new clsPermissoes();
        $link_excluir   = '';

        $obj_escola = new clsPmieducarEscola($alocacao['ref_cod_escola']);
        $det_escola = $obj_escola->detalhe();
        $det_escola = $det_escola['nome'];

        if ($obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7)) {

          $show = TRUE;
          if (4 == $permissao->nivel_acesso($this->pessoa_logada)
              && $alocacao['ref_cod_escola'] != $permissao->getEscola($this->pessoa_logada)
          ) {
            $show = FALSE;
          }

          $link_excluir = $show ? "<a href='#' onclick=\"getElementById('excluir_periodo').value = '{$key}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>" : "";
        }

        // @todo CoreExt_Enum
        switch ($alocacao['periodo']) {
          case 1:
            $nm_periodo = 'Matutino';
            break;
          case 2:
            $nm_periodo = 'Vespertino';
            break;
          case 3:
            $nm_periodo = 'Noturno';
            break;
        }

        // Períodos usados na escola
        self::$escolasPeriodos[$alocacao['ref_cod_escola']][$alocacao['periodo']] = $alocacao['periodo'];

        $this->campoTextoInv('periodo_' . $key, '', $nm_periodo, 10, 10, FALSE,
          FALSE, TRUE, '', '', '', '', 'periodo');

        $this->campoTextoInv('carga_horaria_alocada_' . $key, '',
          substr($alocacao['carga_horaria_alocada'], 0, 5), 5, 5, FALSE, FALSE, TRUE, '', '',
          '', '', 'ds_carga_horaria_');

        $this->campoTextoInv('ref_cod_escola_' . $key, '', $det_escola, 70, 255,
          FALSE, FALSE, FALSE, '', $link_excluir, '', '', 'ref_cod_escola_');
      }
    }

    $this->campoOculto('incluir_periodo', '');
    $this->campoQuebra();
  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
      'educar_servidor_alocacao_lst.php');

    if ($_POST['alocacao_array']) {
      $this->alocacao_array = unserialize(urldecode($_POST['alocacao_array']));
    }

    if ($_POST['alocacao_excluida_array']) {
      $this->alocacao_excluida_array = unserialize(urldecode($_POST['alocacao_excluida_array']));
    }

    if ($this->alocacao_excluida_array) {
      foreach ($this->alocacao_excluida_array as $excluida) {
        $obj = new clsPmieducarServidorAlocacao(NULL, $this->ref_ref_cod_instituicao,
          $this->pessoa_logada, $this->pessoa_logada, $excluida['ref_cod_escola'],
          $this->ref_cod_servidor, NULL, NULL, $this->ativo,
          $excluida['carga_horaria_alocada'], $excluida['periodo']);

        $cadastrou = $obj->excluir_horario();
      }
    }

    if ($_POST['carga_horaria_alocada'] && $_POST['periodo']) {
      $aux                          = array();
      $aux['periodo']               = $_POST['periodo'];
      $aux['carga_horaria_alocada'] = $_POST['carga_horaria_alocada'];
      $aux['ref_cod_escola']        = $_POST['ref_cod_escola'];
      $aux['novo']                  = 1;
      $achou                        = FALSE;

      foreach ($this->alocacao_array as $alocacao) {
        if ($alocacao['periodo'] == $aux['periodo']) {
          $achou = TRUE;
        }
      }

      if (!$achou) {
        $this->alocacao_array[] = $aux;
      }

      unset($this->periodo);
      unset($this->carga_horaria_alocada);
    }

    if ($this->alocacao_array) {
      foreach ($this->alocacao_array as $alocacao) {
        if ($alocacao['novo']) {
          $cargaHoraria = explode(':', $alocacao['carga_horaria_alocada']);

          $hora    = isset($cargaHoraria[0]) ? $cargaHoraria[0] : 0;
          $minuto  = isset($cargaHoraria[1]) ? $cargaHoraria[1] : 0;
          $segundo = isset($cargaHoraria[2]) ? $cargaHoraria[2] : 0;

          $cargaHoraria = sprintf("%'02d:%'02d:%'02d", $hora, $minuto, $segundo);

          $obj = new clsPmieducarServidorAlocacao(NULL, $this->ref_ref_cod_instituicao,
            NULL, $this->pessoa_logada, $alocacao['ref_cod_escola'],
            $this->ref_cod_servidor, NULL, NULL, $this->ativo,
            $cargaHoraria, $alocacao['periodo']);

          $cadastrou = FALSE;

          if (FALSE == $obj->lista(NULL, $this->ref_ref_cod_instituicao,
            NULL, NULL, $alocacao['ref_cod_escola'], $this->ref_cod_servidor, NULL, NULL,
            NULL, NULL, NULL, NULL, $alocacao['periodo'])
          ) {
            $cadastrou = $obj->cadastra();
          }

          if (!$cadastrou) {
            $this->mensagem = 'Cadastro não realizado.<br />';
            echo "<!--\nErro ao cadastrar clsPmieducarServidorAlocacao\nvalores obrigatorios\nis_numeric($this->ref_ref_cod_instituicao) && is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_servidor) && is_numeric($this->periodo) && ($this->carga_horaria_alocada)\n-->";
            return FALSE;
          }
        }
      }
    }

    $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
    header('Location: ' . sprintf('educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d',
      $this->ref_cod_servidor, $this->ref_ref_cod_instituicao));
    die();
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
?>
<script type="text/javascript">
var escolasPeriodos = <?php print json_encode(indice::$escolasPeriodos); ?>;
var periodos = <?php print json_encode(indice::$periodos); ?>;

window.onload = function()
{
  getPeriodos(document.getElementById('ref_cod_escola').value);
}

document.getElementById('ref_cod_escola').onchange = function()
{
  getPeriodos(document.getElementById('ref_cod_escola').value);
}

function getPeriodos(codEscola)
{
  obj = document.getElementById('periodo');
  obj.length = 0;

  for (var ii in periodos) {
    if (!escolasPeriodos[codEscola] || !escolasPeriodos[codEscola][ii]) {
      obj.options[obj.length] = new Option(periodos[ii], ii);
    }
  }
}

function validaHora()
{
  var carga_horaria   = document.getElementById('carga_horaria_alocada').value;
  var periodo         = document.getElementById('periodo').value;
  var ref_cod_escola  = document.getElementById('ref_cod_escola').value;
  var horas_restantes = document.getElementById('horas_restantes_').value;

  if (!ref_cod_escola) {
    alert('Preencha o campo "Escola" corretamente!');
    return false;
  }

  if (!((/[0-9]{2}:[0-9]{2}/).test(document.formcadastro.carga_horaria_alocada.value))) {
    alert('Preencha o campo "Carga Horária" corretamente!');
    return false;
  }

  if (!periodo) {
    alert('Preencha o campo "Período" corretamente!');
    return false;
  }

  horas_restantes = unescape(horas_restantes);
  horas_restantes = unescape(horas_restantes).split(':');

  var carga_horaria_alocada_ = document.getElementById('carga_horaria_alocada').value.split(":");

  hora_           = Date.UTC(1970, 01, 01, carga_horaria_alocada_[0], carga_horaria_alocada_[1], 0);
  hora_max_       = Date.UTC(1970, 01, 01, <?php print clsPmieducarServidorAlocacao::$cargaHorariaMax ?>, 0, 0);
  hora_restantes_ = Date.UTC(1970, 01, 01, horas_restantes[0], horas_restantes[1], 0);

  if (hora_ > hora_max_) {
    message = <?php print sprintf('"O número de horas máximo por período/escola é de %.0fh."', clsPmieducarServidorAlocacao::$cargaHorariaMax); ?>;
    alert(message);
    return false;
  }

  if (hora_ > hora_restantes_) {
    alert("Atenção número de horas excedem o número de horas disponíveis! Por favor, corrija.");
    document.getElementById('ref_cod_escola').value        = '';
    document.getElementById('periodo').value               = '';
    document.getElementById('carga_horaria_alocada').value = '';
    return false;
  }

  return true;
}
</script>