<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

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

    $this->url_cancelar      = sprintf(
      'educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d',
      $this->ref_cod_servidor, $this->ref_ref_cod_instituicao
    );
    $this->nome_url_cancelar = 'Cancelar';    

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "i-Educar - Escola",
         ""        => "Alocar servidor"             
    ));
    $this->enviaLocalizacao($localizacao->montar());

    if (is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_ref_cod_instituicao)) {
      $obj   = new clsPmieducarServidorAlocacao();
      $lista = $obj->lista(NULL, $this->ref_ref_cod_instituicao, NULL, NULL,
        NULL, $this->ref_cod_servidor, NULL, NULL, NULL, NULL, 1, NULL, NULL);

      $qtd_registros = 0;
      if( $lista )
      {
        foreach ( $lista AS $campo )
        {          
                   
          $this->alocacao_array[$qtd_registros][] = $campo['ref_cod_escola'];
          $this->alocacao_array[$qtd_registros][] = $campo['periodo'];          
          $this->alocacao_array[$qtd_registros][] = $campo["carga_horaria"];
          $this->alocacao_array[$qtd_registros][] = $campo['ref_cod_servidor_funcao'];          

          $qtd_registros++;
        }
      }
    }
    else {
      header('Location: educar_servidor_lst.php');
      die();
    }

    return $retorno;
  }

  function Gerar()
  {

    $obj_inst = new clsPmieducarInstituicao($this->ref_ref_cod_instituicao);
    $inst_det = $obj_inst->detalhe();

    $this->campoRotulo('nm_instituicao', 'Instituição', $inst_det['nm_instituicao']);
    $this->campoOculto('ref_ref_cod_instituicao', $this->ref_ref_cod_instituicao);    

    // Dados do servidor
    $objTemp = new clsPmieducarServidor($this->ref_cod_servidor, NULL,
        NULL, NULL, NULL, NULL, 1, $this->ref_ref_cod_instituicao);
    $det = $objTemp->detalhe();

    if ($det) {
      $this->carga_horaria_disponivel = $det['carga_horaria'];
    }

    if ($this->ref_cod_servidor) {
      $objTemp = new clsFuncionario($this->ref_cod_servidor);
      $detalhe = $objTemp->detalhe();
      $detalhe = $detalhe['idpes']->detalhe();
      $nm_servidor = $detalhe['nome'];
    }

    $this->campoRotulo('nm_servidor', 'Servidor', $nm_servidor);

    $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);    

    // Carga horária
    $carga = $this->carga_horaria_disponivel;
    $this->campoRotulo('carga_horaria_disponivel', 'Carga Horária', $carga . ':00');

    $this->campoQuebra();

    // Início tabela

    $this->campoTabelaInicio("alocaoes_servidor","Aloca&ccedil;&otilde;es do servidor",array("Escola","Período","Carga hor&aacute;ria", "Fun&ccedil;&atilde;o"),$this->alocacao_array);

    // Escolas
    $obj_escola = new clsPmieducarEscola();

    $lista_escola = $obj_escola->lista(NULL, NULL, NULL,
      $this->ref_ref_cod_instituicao, NULL, NULL, NULL, NULL, NULL, NULL, 1);

    $opcoes = array('' => 'Selecione');

    if ($lista_escola) {
      foreach ($lista_escola as $escola) {
        $opcoes[$escola['cod_escola']] = $escola['nome'];
      }
    }

    $this->campoLista('ref_cod_escola', 'Escola', $opcoes, $this->ref_cod_escola,
      '', FALSE, '', '', FALSE, TRUE);
    

    $periodo = array(
      1  => 'Matutino',
      2  => 'Vespertino',
      3  => 'Noturno'
    );
    self::$periodos = $periodo;

    $this->campoLista('periodo', 'Período', $periodo, $this->periodo, NULL, FALSE,
      '', '', FALSE, TRUE);

    $this->campoHora('carga_horaria_alocada', 'Carga Horária',
      $this->carga_horaria_alocada, TRUE);

    // Funções
    $obj_funcoes = new clsPmieducarServidorFuncao();

    $lista_funcoes = $obj_funcoes->funcoesDoServidor($this->ref_cod_instituicao, $this->ref_cod_servidor);

    $opcoes = array('' => 'Selecione');

    if ($lista_funcoes) {
      foreach ($lista_funcoes as $funcao) {        
        $opcoes[$funcao['cod_servidor_funcao']] = ( !empty($funcao['matricula']) ? "{$funcao['funcao']} - {$funcao['matricula']}" : $funcao['funcao'] );
      }
    }

    $this->campoLista('cod_servidor_funcao', 'Função', $opcoes, $this->cod_servidor_funcao,
      '', FALSE, '', '', FALSE, FALSE);

    $this->campoTabelaFim();
  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
      'educar_servidor_alocacao_lst.php');  

    $carga_horaria_disponivel = urldecode($this->carga_horaria_disponivel);

    if ($this->hhmmToMinutes($carga_horaria_disponivel) >= $this->arrayHhmmToMinutes($this->carga_horaria_alocada)){

      $obj_tmp = new clsPmieducarServidorAlocacao();
      $obj_tmp->excluiAlocacoesServidor($this->ref_cod_servidor);

      foreach ($this->ref_cod_escola as $key => $value) {
        if (stripos($periodos_em_uso, $this->periodo[$key])){
          $this->mensagem = 'Período informado já foi alocado. Por favor, selecione outro.<br />';
          $this->alocacao_array = null;
          foreach ($this->ref_cod_escola as $key => $value) {
            $this->alocacao_array[$key][] = $value;
            $this->alocacao_array[$key][] = $this->periodo[$key];
            $this->alocacao_array[$key][] = $this->carga_horaria_alocada[$key];
            $this->alocacao_array[$key][] = $this->cod_servidor_funcao[$key];  
          }
          return false;
        }else{
          $cargaHoraria = explode(':', $this->carga_horaria_alocada[$key]);

          $hora    = isset($cargaHoraria[0]) ? $cargaHoraria[0] : 0;
          $minuto  = isset($cargaHoraria[1]) ? $cargaHoraria[1] : 0;
          $segundo = isset($cargaHoraria[2]) ? $cargaHoraria[2] : 0;

          $cargaHoraria = sprintf("%'02d:%'02d:%'02d", $hora, $minuto, $segundo);

          $obj = new clsPmieducarServidorAlocacao(NULL, $this->ref_ref_cod_instituicao,
            NULL, $this->pessoa_logada, $value,
            $this->ref_cod_servidor, NULL, NULL, $this->ativo,
            $cargaHoraria, $this->periodo[$key], $this->cod_servidor_funcao[$key]);        
          $cadastrou = FALSE;

          $cadastrou = $obj->cadastra();
        
          $periodos_em_uso = $periodos_em_uso.'-'.$this->periodo[$key];
          if (!$cadastrou) {
            $this->mensagem = 'Cadastro não realizado.<br />';
            echo "<!--\nErro ao cadastrar clsPmieducarServidorAlocacao\nvalores obrigatorios\nis_numeric($this->ref_ref_cod_instituicao) && is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_servidor) && is_numeric($this->periodo) && ($this->carga_horaria_alocada)\n-->";
            return FALSE;
          }
        }    
      }
    }else{
      $this->mensagem = 'Não é possível alocar quantidade superior de horas do que o disponível.<br />';
      $this->alocacao_array = null;
      foreach ($this->ref_cod_escola as $key => $value) {
        $this->alocacao_array[$key][] = $value;
        $this->alocacao_array[$key][] = $this->periodo[$key];
        $this->alocacao_array[$key][] = $this->carga_horaria_alocada[$key];
        $this->alocacao_array[$key][] = $this->cod_servidor_funcao[$key];  
      }
      return false;
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

  function hhmmToMinutes($hhmm){
    if(strlen($hhmm) == 5)
      return ((int)substr($hhmm, 0, 2)) + ((int) substr($hhmm, 3, 2)) * 60;
    else
      return 0;
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
</script>