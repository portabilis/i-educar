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
require_once "lib/Portabilis/String/Utils.php";
require_once 'lib/Portabilis/Date/Utils.php';

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
    $this->SetTitulo($this->_instituicao . ' Servidores - Servidor Alocação');
    $this->processoAp = 635;
    $this->addEstilo('localizacaoSistema');
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
  var $ref_cod_funcionario_vinculo;
  var $ano;
  var $data_admissao;
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

    $ref_cod_servidor        = $_GET['ref_cod_servidor'];
    $ref_ref_cod_instituicao = $_GET['ref_cod_instituicao'];
    $cod_servidor_alocacao   = $_GET['cod_servidor_alocacao'];

    if (is_numeric($cod_servidor_alocacao)) {
      $this->cod_servidor_alocacao = $cod_servidor_alocacao;

      $servidorAlocacao = new clsPmieducarServidorAlocacao($this->cod_servidor_alocacao);
      $servidorAlocacao = $servidorAlocacao->detalhe();

      $this->ref_ref_cod_instituicao     = $servidorAlocacao['ref_ref_cod_instituicao'];
      $this->ref_cod_servidor            = $servidorAlocacao['ref_cod_servidor'];
      $this->ref_cod_escola              = $servidorAlocacao['ref_cod_escola'];
      $this->periodo                     = $servidorAlocacao['periodo'];
      $this->carga_horaria_alocada       = $servidorAlocacao['carga_horaria'];
      $this->cod_servidor_funcao         = $servidorAlocacao['ref_cod_servidor_funcao'];
      $this->ref_cod_funcionario_vinculo = $servidorAlocacao['ref_cod_funcionario_vinculo'];
      $this->ativo                       = $servidorAlocacao['ativo'];
      $this->ano                         = $servidorAlocacao['ano'];
      $this->data_admissao               = $servidorAlocacao['data_admissao'];

    } else if (is_numeric($ref_cod_servidor) && is_numeric($ref_ref_cod_instituicao)) {
      $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
      $this->ref_cod_servidor        = $ref_cod_servidor;
      $this->ref_cod_instituicao = $ref_ref_cod_instituicao;
    } else {
      header('Location: educar_servidor_lst.php');
      die();
    }

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
      'educar_servidor_lst.php');

    if ($obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7)) {
      $this->fexcluir = TRUE;
    }

    $this->url_cancelar      = sprintf(
      'educar_servidor_alocacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
      $this->ref_cod_servidor, $this->ref_ref_cod_instituicao
    );
    $this->nome_url_cancelar = 'Cancelar';

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_servidores_index.php"       => "Servidores",
         ""                                  => "Alocar servidor"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    return $retorno;
  }

  function Gerar()
  {

    $obj_inst = new clsPmieducarInstituicao($this->ref_ref_cod_instituicao);
    $inst_det = $obj_inst->detalhe();

    $this->campoRotulo('nm_instituicao', 'Instituição', $inst_det['nm_instituicao']);
    $this->campoOculto("ref_ref_cod_instituicao", $this->ref_ref_cod_instituicao);
    $this->campoOculto("cod_servidor_alocacao", $this->cod_servidor_alocacao);

    // Dados do servidor
    $objTemp = new clsPmieducarServidor($this->ref_cod_servidor, NULL,
        NULL, NULL, NULL, NULL, 1, $this->ref_ref_cod_instituicao);
    $det = $objTemp->detalhe();

    if ($det) {
      $this->carga_horaria_disponivel = $det['carga_horaria'];
    }

    if ($this->ref_cod_servidor) {
      $objTemp = new clsPessoaFisica($this->ref_cod_servidor);
      $detalhe = $objTemp->detalhe();
      //$detalhe = $detalhe['idpes']->detalhe();
      $nm_servidor = $detalhe['nome'];
    }

    $this->campoRotulo('nm_servidor', 'Servidor', $nm_servidor);

    $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);

    // Carga horária
    $carga = $this->carga_horaria_disponivel;
    $this->campoRotulo('carga_horaria_disponivel', 'Carga horária do servidor', $carga . ':00');

    $this->inputsHelper()->integer('ano', array('value' => $this->ano, 'max_length' => 4));

    $this->inputsHelper()->dynamic('escola');

    // Períodos
    $periodo = array(
      1  => 'Matutino',
      2  => 'Vespertino',
      3  => 'Noturno'
    );
    self::$periodos = $periodo;

    $this->campoLista('periodo', 'Período', $periodo, $this->periodo, NULL, FALSE, '', '', FALSE, TRUE);

    // Carga horária
    $this->campoHoraServidor('carga_horaria_alocada', 'Carga horária', $this->carga_horaria_alocada, TRUE);

    $options = array(
        'label' => 'Data de admissão',
        'placeholder' => 'dd/mm/yyyy',
        'hint' => 'A data deve estar em branco ou dentro do período de datas da exportação para o Educacenso, para o servidor ser exportado.',
        'value' => $this->data_admissao,
        'required' => FALSE,
    );
    $this->inputsHelper()->date('data_admissao', $options);

    // Funções
    $obj_funcoes = new clsPmieducarServidorFuncao();

    $lista_funcoes = $obj_funcoes->funcoesDoServidor($this->ref_ref_cod_instituicao, $this->ref_cod_servidor);

    $opcoes = array('' => 'Selecione');

    if ($lista_funcoes) {
      foreach ($lista_funcoes as $funcao) {
        $opcoes[$funcao['cod_servidor_funcao']] = ( !empty($funcao['matricula']) ? "{$funcao['funcao']} - {$funcao['matricula']}" : $funcao['funcao'] );
      }
    }

    $this->campoLista('cod_servidor_funcao', 'Função', $opcoes, $this->cod_servidor_funcao, '', FALSE, '', '', FALSE, FALSE);

    // Vínculos
    $opcoes = array("" => "Selecione", 5 => "Comissionado", 4 => "Contratado", 3 => "Efetivo", 6 => "Estagi&aacute;rio");

    $this->campoLista("ref_cod_funcionario_vinculo", "V&iacute;nculo", $opcoes, $this->ref_cod_funcionario_vinculo, NULL, FALSE, '', '', FALSE, FALSE);
  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
        "educar_servidor_alocacao_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}");
    $dataAdmissao = $this->data_admissao ? Portabilis_Date_Utils::brToPgSql($this->data_admissao) : NULL;

    $servidorAlocacao = new clsPmieducarServidorAlocacao($this->cod_servidor_alocacao,
                                                 $this->ref_ref_cod_instituicao,
                                                 null,
                                                 null,
                                                 null,
                                                 $this->ref_cod_servidor,
                                                 null,
                                                 null,
                                                 null,
                                                 null,
                                                 null,
                                                 null,
                                                 null,
                                                 $this->ano,
                                                 $dataAdmissao);

    $carga_horaria_disponivel = $this->hhmmToMinutes($this->carga_horaria_disponivel);
    $carga_horaria_alocada    = $this->hhmmToMinutes($this->carga_horaria_alocada);
    $carga_horaria_alocada   += $this->hhmmToMinutes($servidorAlocacao->getCargaHorariaAno());

    if ($carga_horaria_disponivel >= $carga_horaria_alocada){

    $obj_novo = new clsPmieducarServidorAlocacao($this->cod_servidor_alocacao,
                                                 $this->ref_ref_cod_instituicao,
                                                 null,
                                                 $this->pessoa_logada,
                                                 $this->ref_cod_escola,
                                                 $this->ref_cod_servidor,
                                                 null,
                                                 null,
                                                 $this->ativo,
                                                 $this->carga_horaria_alocada,
                                                 $this->periodo,
                                                 $this->cod_servidor_funcao,
                                                 $this->ref_cod_funcionario_vinculo,
                                                 $this->ano,
                                                 $dataAdmissao);

      if ($obj_novo->periodoAlocado()) {
        $this->mensagem = 'Período informado já foi alocado. Por favor, selecione outro.<br />';
        return FALSE;
      }

      $cadastrou = $obj_novo->cadastra();

      if (!$cadastrou) {
        $this->mensagem = 'Cadastro não realizado.<br />';
        echo "<!--\nErro ao cadastrar clsPmieducarServidorAlocacao\nvalores obrigatorios\nis_numeric($this->ref_ref_cod_instituicao) &&
              is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_servidor) &&
              is_numeric($this->periodo) && ($this->carga_horaria_alocada)\n-->";
        return FALSE;
      }

      // Excluí alocação existente
      if ($this->cod_servidor_alocacao) {
        $obj_tmp = new clsPmieducarServidorAlocacao($this->cod_servidor_alocacao, null, $this->pessoa_logada);
        $obj_tmp->excluir();
      }

      // Atualiza código da alocação
      $this->cod_servidor_alocacao = $cadastrou;
    }else{
      $this->mensagem = 'Não é possível alocar quantidade superior de horas do que o disponível.<br />';
      $this->alocacao_array = null;

      return false;
    }

    $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
    header('Location: ' . sprintf('educar_servidor_alocacao_det.php?cod_servidor_alocacao=%d', $this->cod_servidor_alocacao));
    die();
  }

  function Editar()
  {
    return FALSE;
  }

  function Excluir()
  {

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    if ($this->cod_servidor_alocacao) {
      $obj_tmp = new clsPmieducarServidorAlocacao($this->cod_servidor_alocacao, null, $this->pessoa_logada);
      $excluiu = $obj_tmp->excluir();

      if ($excluiu) {
        $this->mensagem = "Exclusão efetuada com sucesso.<br>";
        header("Location: ". sprintf(
              'educar_servidor_alocacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
              $this->ref_cod_servidor, $this->ref_ref_cod_instituicao));
        die();
      }
    }

    $this->mensagem = 'Exclusão não realizada.<br>';
    return false;
  }

  function hhmmToMinutes($hhmm){
    list($hora, $minuto) = explode(':', $hhmm);
    return (((int)$hora * 60) + $minuto);
  }

  function arrayHhmmToMinutes($array){
    $total = 0;
    foreach ($array as $key => $value) {
      $total += $this->hhmmToMinutes($value);
    }
    return $total;
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