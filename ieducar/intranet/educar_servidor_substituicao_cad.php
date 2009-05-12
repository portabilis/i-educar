<?php

/*
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
 */

/**
 * Formulário de substituição de servidor
 *
 * Interface administrativa para a substituição de horário de um servidor por
 * outro. As classes deste arquivo extendem as classes básicas de interface
 * com o usuário.
 *
 * @author      Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  Servidor
 * @since       Disponível desde a versão 1.0.0
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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Servidor Substitui&ccedil;&atilde;o" );
		$this->processoAp = "635";
	}
}

class indice extends clsCadastro
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	var $cod_servidor_alocacao;
	var $ref_ref_cod_instituicao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_escola;
	var $ref_cod_servidor;
	var $dia_semana;
	var $hora_inicial;
	var $hora_final;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	var $todos;

	var $alocacao_array = array();
	var $professor;
	//var $dias_da_semana = array( '' => 'Selecione', 1 => 'Domingo', 2 => 'Segunda', 3 => 'Ter&ccedil;a', 4 => 'Quarta', 5 => 'Quinta', 6 => 'Sexta', 7 => 'S&aacute;bado' );

  public function Inicializar() {
    $retorno = 'Novo';
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->ref_cod_servidor        = $_GET['ref_cod_servidor'];
    $this->ref_ref_cod_instituicao = $_GET['ref_cod_instituicao'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 3, 'educar_servidor_lst.php');

    if (is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_ref_cod_instituicao)) {
      $retorno = 'Novo';

      $obj_servidor = new clsPmieducarServidor($this->ref_cod_servidor,
        NULL, NULL, NULL, NULL, NULL, NULL, $this->ref_ref_cod_instituicao);
      $det_servidor = $obj_servidor->detalhe();

      // Nenhum servidor com o código de servidor e instituição
      if (!$det_servidor) {
        header('Location: educar_servidor_lst.php');
        die;
      }

      $this->professor = $obj_servidor->isProfessor() == TRUE ? 'true' : 'false';

      $obj = new clsPmieducarServidorAlocacao();
      $lista  = $obj->lista(NULL, $this->ref_ref_cod_instituicao, NULL,
        NULL, NULL, $this->ref_cod_servidor, NULL, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, NULL, 1);

      if ($lista) {
        // passa todos os valores obtidos no registro para atributos do objeto
        foreach ($lista as $campo => $val){
          $temp = array();
          $temp['carga_horaria']  = $val['carga_horaria'];
          $temp['periodo']        = $val['periodo'];
          $temp['ref_cod_escola'] = $val['ref_cod_escola'];

          $this->alocacao_array[] = $temp;
        }

        $retorno = "Novo";
      }

      $this->carga_horaria = $det_servidor['carga_horaria'];
    }
    else {
      header('Location: educar_servidor_lst.php');
      die;
    }

    $this->url_cancelar = "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
    $this->nome_url_cancelar = "Cancelar";

    return $retorno;
  }



  public function Gerar() {
    $obj_inst = new clsPmieducarInstituicao($this->ref_ref_cod_instituicao);
    $inst_det = $obj_inst->detalhe();

    $this->campoRotulo("nm_instituicao", "Institui&ccedil;&atilde;o", $inst_det['nm_instituicao']);
    $this->campoOculto("ref_ref_cod_instituicao", $this->ref_ref_cod_instituicao);

    $opcoes = array("" => "Selecione");
    if (class_exists("clsPmieducarServidor")) {
      $objTemp = new clsPmieducarServidor($this->ref_cod_servidor);
      $det = $objTemp->detalhe();
      if ($det) {
        foreach ($det as $key => $registro) {
          $this->$key =  $registro;
        }
      }

      if ($this->ref_cod_servidor) {
        $objTemp = new clsFuncionario($this->ref_cod_servidor);
        $detalhe = $objTemp->detalhe();
        $detalhe = $detalhe['idpes']->detalhe();
        $nm_servidor = $detalhe['nome'];
      }
    }

    $this->campoRotulo("nm_servidor", "Servidor", $nm_servidor);

    $this->campoOculto("ref_cod_servidor", $this->ref_cod_servidor);
    $this->campoOculto("professor",$this->professor);

    $this->campoTextoInv("ref_cod_servidor_todos_", "Substituir por:", "",
      30, 255, TRUE, FALSE, FALSE, "", "<img border='0' onclick=\"pesquisa_valores_popless('educar_pesquisa_servidor_lst.php?campo1=ref_cod_servidor_todos&campo2=ref_cod_servidor_todos_&ref_cod_instituicao={$this->ref_ref_cod_instituicao}&ref_cod_servidor={$this->ref_cod_servidor}&tipo=livre&professor={$this->professor}', 'nome')\" src=\"imagens/lupa.png\">","","","" );
    $this->campoOculto("ref_cod_servidor_todos", "");

    $this->campoOculto("alocacao_array", serialize($this->alocacao_array));
    $this->acao_enviar = 'acao2()';
  }



  public function Novo() {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $professor  = isset($_POST['professor']) ? strtolower($_POST['professor']) : 'FALSE';
    $substituto = isset($_POST['ref_cod_servidor_todos']) ? $_POST['ref_cod_servidor_todos'] : NULL;

    $permissoes = new clsPermissoes();
    $permissoes->permissao_cadastra(635, $this->pessoa_logada, 3, 'educar_servidor_alocacao_lst.php');

    $this->alocacao_array = array();
    if ($_POST['alocacao_array']) {
      $this->alocacao_array = unserialize(urldecode($_POST['alocacao_array']));
    }

    if ($this->alocacao_array) {
      // Substitui todas as alocações
      foreach ($this->alocacao_array as $key => $alocacao) {
        $obj = new clsPmieducarServidorAlocacao(NULL, $this->ref_ref_cod_instituicao,
          $this->pessoa_logada, $this->pessoa_logada, $alocacao['ref_cod_escola'],
          $this->ref_cod_servidor, NULL, NULL, NULL, $alocacao['carga_horaria'],
          $alocacao['periodo']);

        $return = $obj->lista(NULL, $this->ref_ref_cod_instituicao, NULL, NULL,
          $alocacao['ref_cod_escola'], $this->ref_cod_servidor, NULL, NULL, NULL,
          NULL, 1, $alocacao['carga_horaria']);

        if (FALSE !== $return) {
          $substituiu = $obj->substituir_servidor($substituto);
          if (!$substituiu) {
            $this->mensagem = "Substituicao n&atilde;o realizado.<br>";

            return FALSE;
          }
        }
      }

      // Substituição do servidor no quadro de horários (caso seja professor)
      if('true' == $professor) {
        $quadroHorarios = new clsPmieducarQuadroHorarioHorarios(NULL, NULL, NULL,
          NULL, NULL, NULL, $this->ref_ref_cod_instituicao, NULL, $this->ref_cod_servidor,
          NULL, NULL, NULL, NULL, 1, NULL, NULL);
        $quadroHorarios->substituir_servidor($substituto);
      }
    }

    $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
    $destination = 'educar_servidor_det.php?cod_servidor=%s&ref_cod_instituicao=%s';
    $destination = sprintf($destination, $this->ref_cod_servidor, $this->ref_ref_cod_instituicao);

    header('Location: ' . $destination);
    die();
  }



	function Editar()
	{
		/*@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 3,  "educar_servidor_alocacao_lst.php" );


		$obj = new clsPmieducarServidorAlocacao($this->cod_servidor_alocacao, $this->ref_ref_cod_instituicao, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_escola, $this->ref_cod_servidor, $this->dia_semana, $this->hora_inicial, $this->hora_final, $this->data_cadastro, $this->data_exclusao, $this->ativo);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_servidor_alocacao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarServidorAlocacao\nvalores obrigatorios\nif( is_numeric( $this->cod_servidor_alocacao ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		*/
		return false;
	}

	function Excluir()
	{
	/*	@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 635, $this->pessoa_logada, 3,  "educar_servidor_alocacao_lst.php" );


		$obj = new clsPmieducarServidorAlocacao($this->cod_servidor_alocacao, $this->ref_ref_cod_instituicao, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_escola, $this->ref_cod_servidor, $this->dia_semana, $this->hora_inicial, $this->hora_final, $this->data_cadastro, $this->data_exclusao, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_servidor_alocacao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarServidorAlocacao\nvalores obrigatorios\nif( is_numeric( $this->cod_servidor_alocacao ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		*/
		return false;

	}
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
<script>
//setVisibility('tr_ref_cod_servidor_todos_',false);

function trocaDisplay(id)
{
	if(getVisibility(id)){
		setVisibility(id,false);
		setAll('ref_cod_servidor_substituto',true);
		document.getElementById('todos').value='false';
		document.getElementById('trocar').src = 'imagens/i-educar/bot_subt_todos.gif';
		document.getElementById('trocar').blur();
	}
	else{
		setVisibility(id,true);
		setAll('ref_cod_servidor_substituto',false);
		document.getElementById('todos').value='true';
		document.getElementById('trocar').src = image.src;
		document.getElementById('trocar').blur();

	}
}

function setAll(field,visibility){
	var elements = window.parent.document.getElementsByName(field);

	for(var ct =0;ct < elements.length;ct++)
	{
		setVisibility(elements[ct].id,visibility);
	}
}

function acao2(){
	//if(	document.getElementById('todos').value == "true"){
		if(	document.getElementById('ref_cod_servidor_todos').value == ''){
			alert("Selecione um servidor substituto!");
			return false;
		}

//	}
	acao();
}

//var image = new Image();
//image.src = 'imagens/i-educar/bot_subt_horario.gif';


</script>