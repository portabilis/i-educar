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
 * @author    Alan Felipe Farias <alan@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */



require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';

require_once 'App/Date/Utils.php';

require_once 'ComponenteCurricular/Model/TurmaDataMapper.php';


class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Unifica&ccedil;&atilde;o de alunos');
    $this->processoAp = "21250";
    $this->addEstilo("localizacaoSistema");
  }
}


class indice extends clsCadastro
{
  var $pessoa_logada;

  var $tabela_alunos = array();
  var $aluno_duplicado; 

  function Inicializar()
  {
    $retorno = 'Novo';

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(761, $this->pessoa_logada, 7,
      'index.php'); 

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         ""        => "Unifica&ccedil;&atilde;o de alunos"             
    ));
    $this->enviaLocalizacao($localizacao->montar());

    return $retorno;
  }

  function Gerar()
  {

      $this->inputsHelper()->dynamic('ano', array('required' => false, 'max_length' => 4));
      $this->inputsHelper()->dynamic('instituicao',  array('required' =>  false, 'show-select' => true));
      $this->inputsHelper()->dynamic('escola',  array('required' =>  false, 'show-select' => true, 'value' => 0));
      $this->inputsHelper()->simpleSearchAluno(null,array('label' => 'Aluno principal' ));
      $this->campoTabelaInicio("tabela_alunos","",array("Aluno duplicado"),$this->tabela_alunos);
      $this->campoTexto( "aluno_duplicado", "Aluno duplicado", $this->aluno_duplicado, 50, 255, false, true, false, '', '', '', 'onfocus' );
      $this->campoTabelaFim();

  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(761, $this->pessoa_logada, 7,
      'index.php');
    // Pega o codigo do aluno principal
    $aluno_principal = $this->aluno_id;
    $obj_aluno = new clsPmieducarAluno($aluno_principal);
    $obj_aluno = $obj_aluno->detalhe();
    $cod_aluno_principal = $obj_aluno['cod_aluno'];

    $alunos_duplicados = $this->aluno_duplicado;
    $numeroAlunosDuplicadoArray = count($alunos_duplicados) - 1;

    $db = new clsBanco();
    $contPessoa = -1;
    $montaSql = '';
    $db->consulta("SELECT max(sequencial) as maior_seq    from  pmieducar.historico_escolar      where ref_cod_aluno = {$cod_aluno_principal}");
    $db->ProximoRegistro();
    $maiorSeqAlunoPrincipal = $db->Tupla();
   
    $comecoSequencial = 100 + $maiorSeqAlunoPrincipal['maior_seq'];
    while( $numeroAlunosDuplicadoArray > $contPessoa){
      $contPessoa++;
        $explode = explode(" ", $alunos_duplicados[$contPessoa]);
        $montaSql .= $explode[0].", ";
        $db->consulta("SELECT count(*) as qtd     from pmieducar.historico_escolar     where ref_cod_aluno = {$explode[0]} ");      
        $db->ProximoRegistro();
        $temRegistro = $db->Tupla();
        if($temRegistro['qtd'] > 0){
     if($explode[0] != $cod_aluno_principal){
        
        $iHisEsc = -1;
        $arraySeqHisEsco  = array($db->consulta("SELECT sequencial     from historico_escolar     where ref_cod_aluno = {$explode[0]}     GROUP BY ref_cod_aluno, sequencial         order by  ref_cod_aluno, sequencial "));
  while($db->ProximoRegistro()){
        $iHisEsc++;
        $varHisEsc[$iHisEsc] = $db->Tupla();
        $arraySeqHisEsco[$iHisEsc] = $varHisEsc[$iHisEsc][0];
        }
        $iHisDisc = -1;
        $arraySeqHisDisci = array($db->consulta("SELECT ref_sequencial from historico_disciplinas where ref_ref_cod_aluno = {$explode[0]} GROUP BY ref_ref_cod_aluno, ref_sequencial order by  ref_ref_cod_aluno, ref_sequencial "));        
  while($db->ProximoRegistro()){
        $iHisDisc++;
        $varHisDisc[$iHisDisc] = $db->Tupla();
        $arraySeqHisDisci[$iHisDisc] = $varHisDisc[$iHisDisc][0];
        }
        $numElementsHistorico = count($arraySeqHisEsco) - 1;
        $numElementsHisDisci  = count($arraySeqHisDisci) - 1;
        $i = -1;
     if($numElementsHistorico == $numElementsHisDisci){
      $mairoSeqAlunoDuplicado = $db->consulta("SELECT max(sequencial)     from historico_escolar     where ref_cod_aluno = {$explode[0]}     GROUP BY ref_cod_aluno, sequencial   order by  ref_cod_aluno, sequencial ");
        $numeroGrande += $comecoSequencial + $mairoSeqAlunoDuplicado;
  while($i < $numElementsHisDisci){
        $i++;
        $db->consulta("UPDATE pmieducar.historico_escolar     SET sequencial     = {$numeroGrande} where ref_cod_aluno     = {$explode[0]} and sequencial     = $arraySeqHisEsco[$i]");
        $db->consulta("UPDATE pmieducar.historico_disciplinas SET ref_sequencial = {$numeroGrande} where ref_ref_cod_aluno = {$explode[0]} and ref_sequencial = $arraySeqHisDisci[$i]");
        $numeroGrande++;            
        }
        }
        }else{
        $this->mensagem = 'Impossivel de unificar alunos iguais.<br />';
        return false;
        }
    }}
    $montaSql = substr($montaSql, 0, -2);
    
    $db->consulta("UPDATE pmieducar.historico_escolar SET ref_cod_aluno = {$cod_aluno_principal} where ref_cod_aluno in ({$montaSql})");
    $db->consulta("UPDATE pmieducar.historico_disciplinas SET ref_ref_cod_aluno = {$cod_aluno_principal} where ref_ref_cod_aluno in ({$montaSql})");
    $db->consulta("UPDATE pmieducar.matricula SET ref_cod_aluno = {$cod_aluno_principal} where ref_cod_aluno in ({$montaSql})");
    $db->consulta("UPDATE pmieducar.aluno SET ativo = 0, data_exclusao = now(), ref_usuario_exc = {$this->pessoa_logada} where cod_aluno in ({$montaSql})");
    $this->mensagem = "<span class='success'>Alunos unificados com sucesso.</span>";
    return true;
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

  var handleSelect = function(event, ui){
    $j(event.target).val(ui.item.label);
    return false;
  };

  var search = function(request, response) {
    var searchPath = '/module/Api/Aluno?oper=get&resource=aluno-search';
    var params     = { query : request.term };

    $j.get(searchPath, params, function(dataResponse) {
      simpleSearch.handleSearch(dataResponse, response);
    });
  };

  function setAutoComplete() {
    $j.each($j('input[id^="aluno_duplicado"]'), function(index, field) {

      $j(field).autocomplete({
        source    : search,
        select    : handleSelect,
        minLength : 1,
        autoFocus : true
      });

    });
  }

  setAutoComplete();  

  // bind events

  var $addPontosButton = $j('#btn_add_tab_add_1');

  $addPontosButton.click(function(){
    setAutoComplete();
  });

$j('#btn_enviar').val('Unificar');


</script>