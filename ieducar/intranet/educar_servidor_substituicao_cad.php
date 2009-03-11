<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itajaí								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itajaí			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  é  software livre, você pode redistribuí-lo e/ou	 *
	*	modificá-lo sob os termos da Licença Pública Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a versão 2 da	 *
	*	Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.	 *
	*																		 *
	*	Este programa  é distribuído na expectativa de ser útil, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-	 *
	*	ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU	 *
	*	junto  com  este  programa. Se não, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

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

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->ref_cod_servidor = $_GET["ref_cod_servidor"];
		$this->ref_ref_cod_instituicao = $_GET["ref_cod_instituicao"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 3,  "educar_servidor_lst.php" );

		if( is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->ref_ref_cod_instituicao ) )
		{


			$retorno = "Novo";

			$obj_servidor = new clsPmieducarServidor($this->ref_cod_servidor,null,null,null,null,null,null,$this->ref_ref_cod_instituicao);
			$det_servidor = $obj_servidor->detalhe();
			if(!$det_servidor)
			{
				header("location: educar_servidor_lst.php");
				die;
			}
			$obj_funcao = new clsPmieducarFuncao($det_servidor['ref_cod_funcao'],null,null,null,null,null,null,null,1,$this->ref_ref_cod_instituicao);
			$det_funcao = $obj_funcao->detalhe();
			$this->professor = $det_funcao['professor'] == 1 ? "true"	 : "false";

			$obj = new clsPmieducarServidorAlocacao( );
			$lista  = $obj->lista(null,$this->ref_ref_cod_instituicao,null,null,null,$this->ref_cod_servidor,null,null,null,null,null,null,null,null,null,1);
			if( $lista )
			{
				foreach( $lista AS $campo => $val ){	// passa todos os valores obtidos no registro para atributos do objeto
					$temp = array();
					$temp['carga_horaria'] = $val['carga_horaria'];
					$temp['periodo'] = $val['periodo'];
					//$temp['hora_final'] = $val['hora_final'];
					//$temp['dia_semana'] = $val['dia_semana'];
					$temp['ref_cod_escola'] = $val['ref_cod_escola'];

					$this->alocacao_array[] = $temp;

				}

				/*$obj_permissoes = new clsPermissoes();
				if( $obj_permissoes->permissao_excluir( 635, $this->pessoa_logada, 3 ) )
				{
					$this->fexcluir = true;
				}*/

					$retorno = "Novo";
			}


			$this->carga_horaria = $det_servidor['carga_horaria'];
		}
		else{
			header("location: educar_servidor_lst.php");
			die;
		}
		$this->url_cancelar = "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{

		$obj_inst = new clsPmieducarInstituicao($this->ref_ref_cod_instituicao);
		$inst_det = $obj_inst->detalhe();

		$this->campoRotulo("nm_instituicao","Institui&ccedil;&atilde;o",$inst_det['nm_instituicao']);
		$this->campoOculto("ref_ref_cod_instituicao",$this->ref_ref_cod_instituicao);


		//$this->campoRotulo("nm_escola","Escola",$nm_escola);

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarServidor" ) )
		{
			$objTemp = new clsPmieducarServidor($this->ref_cod_servidor);
			$det = $objTemp->detalhe();
			if ($det )
			{
				foreach ( $det as $key => $registro )
				{
					$this->$key =  $registro;
				}
			}

			if( $this->ref_cod_servidor )
			{
				$objTemp = new clsFuncionario( $this->ref_cod_servidor );
				$detalhe = $objTemp->detalhe();
				$detalhe = $detalhe["idpes"]->detalhe();
				$nm_servidor = $detalhe["nome"];
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarServidor nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}

		$this->campoRotulo( "nm_servidor", "Servidor", $nm_servidor);

		$this->campoOculto( "ref_cod_servidor", $this->ref_cod_servidor);
		$this->campoOculto("professor",$this->professor);
	//	$this->campoRotulo("substituir_todos","substituir todos","<a href=\"javascript:trocaDisplay('tr_ref_cod_servidor_todos_');\"><img src='imagens/i-educar/bot_subt_todos.gif' id='trocar' border='0'></a>");
		$this->campoTextoInv( "ref_cod_servidor_todos_", "Substituir por:", "", 30, 255, true, false, false, "", "<img border='0'  onclick=\"pesquisa_valores_popless('educar_pesquisa_servidor_lst.php?campo1=ref_cod_servidor_todos&campo2=ref_cod_servidor_todos_&ref_cod_instituicao={$this->ref_ref_cod_instituicao}&ref_cod_servidor={$this->ref_cod_servidor}&tipo=livre&professor={$this->professor}', 'nome')\" src=\"imagens/lupa.png\">","","","" );
		$this->campoOculto("ref_cod_servidor_todos", "");

		//$this->campoOculto( "todos","false");


		$this->campoOculto( "alocacao_array", serialize( $this->alocacao_array ) );
		//array_multisort($this->alocacao_array);



	/*	if ( $this->alocacao_array )
		{
			$excluir_ok = false;
			if($_POST['excluir_dia_semana'] || $_POST['excluir_dia_semana'] == "0")
				$excluir_ok = true;
				$tamanho = sizeof($alocacao);
				$script = "<script>\nvar num_alocacao = {$tamanho};\n";
				$script .= "var array_servidores = Array();\n";
			foreach ( $this->alocacao_array as $key => $alocacao)
			{

				$script .= "array_servidores[{$key}] = new Array();\n";

				$hora_ini = explode(":",$alocacao['hora_inicial']);
				$hora_fim = explode(":",$alocacao['hora_final']);

				$horas_utilizadas = ( $hora_fim[0] -  $hora_ini[0] );
				$minutos_utilizados = ( $hora_fim[1] -  $hora_ini[1] );
				$horas = sprintf("%02d",(int)$horas_utilizadas);
				$minutos = sprintf("%02d",(int)$minutos_utilizados);
				$str_horas_utilizadas =  "{$horas}:{$minutos}";
				$script .= "array_servidores[{$key}][0] = '{$str_horas_utilizadas}'; \n";
				$script .= "array_servidores[{$key}][1] = ''; \n\n";


				$obj_escola = new clsPmieducarEscola($alocacao['ref_cod_escola']);
				$det_escola = $obj_escola->detalhe();
				$det_escola = $det_escola["nome"];
				$nm_dia_semana = $this->dias_da_semana[$alocacao["dia_semana"]];

				$this->campoTextoInv( "dia_semana_{$key}", "", $nm_dia_semana, 8, 8, false, false, true,"","","","","dia_semana" );
				$this->campoTextoInv( "hora_inicial_{$key}", "", $alocacao['hora_inicial'], 5, 5, false, false, true, "","","","","ds_hora_inicial_" );
				$this->campoTextoInv( "hora_final_{$key}", "", $alocacao['hora_final'], 5, 5, false, false, true, "", "","","","ds_hora_final_" );
				$this->campoTextoInv( "ref_cod_escola_{$key}", "", $det_escola, 30, 255, false, false, true, "", "","","","ref_cod_escola_" );
				$this->campoOculto( "ref_cod_servidor_substituto_{$key}","");
				$this->campoTextoInv( "ref_cod_servidor_substituto_{$key}_", "", $ref_cod_servidor_substituto, 30, 255, false, false, false, "", "<span name=\"ref_cod_servidor_substituto\" id=\"ref_cod_servidor_substituicao_{$key}\"><img border='0'  onclick=\"pesquisa_valores_popless('educar_pesquisa_servidor_lst.php?campo1=ref_cod_servidor_substituto_{$key}&campo2=ref_cod_servidor_substituto_{$key}_&ref_cod_instituicao={$this->ref_ref_cod_instituicao}&dia_semana={$alocacao["dia_semana"]}&hora_inicial={$alocacao["hora_inicial"]}&hora_final={$alocacao["hora_final"]}&ref_cod_servidor={$this->ref_cod_servidor}', 'nome')\" src=\"imagens/lupa.png\" ></span>","","","ref_cod_servidor_substituto" );

			}

*/
/*			$db = new clsBanco();
			$consulta = "SELECT ref_cod_servidor
						        ,ref_ref_cod_instituicao
						        ,sum(hora_final - hora_inicial ) as horas
						   FROM pmieducar.servidor_alocacao
						  WHERE ativo = 1
						  GROUP BY ref_cod_servidor,ref_ref_cod_instituicao";
			$registros = $db->Consulta($consulta);
			if($registros)
			{
				$ct = 0;
				$script .="array_horas_utilizadas_servidor = new Array();\n";

				while($db->ProximoRegistro()){
					$registro = $db->Tupla();
					$script .= "array_horas_utilizadas_servidor['{$registro['ref_cod_servidor']}_'] = new Array('{$registro['horas']}'); \n";
					$ct++;
				}
			}

			$consulta = "SELECT DISTINCT cod_servidor
						        ,ref_cod_instituicao
						        ,coalesce(carga_horaria , '0') as carga_horaria
						   FROM pmieducar.servidor s
						   		,pmieducar.servidor_alocacao a
						  WHERE s.ativo = 1
						    AND a.ativo = 1
						    AND s.ref_cod_instituicao = a.ref_ref_cod_instituicao";
			$registros = $db->Consulta($consulta);
			if($registros)
			{
				$ct = 0;
				$script .="array_horas_servidor = new Array();\n";

				while($db->ProximoRegistro()){
					$registro = $db->Tupla();
					$carga_horaria = $registro['carga_horaria'];
					$total_horas = sprintf("%02d",(int)floor($carga_horaria));
					$total_minutos = sprintf("%02d",(int)(floatval($carga_horaria) - floatval($total_horas)) * 60);
					$carga_horaria = "{$total_horas}:{$total_minutos}";//date("H:i",mktime($total_horas, $total_minutos, 0, 1, 1, 1970));
					$script .= "array_horas_servidor['{$registro['cod_servidor']}_'] = new Array('{$carga_horaria}'); \n";
					$ct++;
				}
			}

			$script .= "\n</script>";
			echo $script;
		}*/
		$this->acao_enviar = 'acao2()';

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 3,  "educar_servidor_alocacao_lst.php" );

		if ( $_POST["alocacao_array"] )
			$this->alocacao_array = unserialize( urldecode( $_POST["alocacao_array"] ) );

		if($this->alocacao_array){

			$substituto = $_POST['ref_cod_servidor_todos'];

			foreach ($this->alocacao_array as $key => $alocacao)
			{
				//echo $_POST['todos'] == false ;

				//if($substituto){


				$obj = new clsPmieducarServidorAlocacao(null,$this->ref_ref_cod_instituicao,$this->pessoa_logada,$this->pessoa_logada,$alocacao['ref_cod_escola'],$this->ref_cod_servidor,null,null,null,$alocacao['carga_horaria'],$alocacao['periodo'] );

//				if($obj->lista(null, $this->ref_ref_cod_instituicao, $this->pessoa_logada, $this->pessoa_logada, $alocacao['ref_cod_escola'], $this->ref_cod_servidor, $alocacao['dia_semana'], $alocacao['hora_inicial'], $alocacao['hora_final'], $this->data_cadastro, $this->data_exclusao, $this->ativo)){
				if($obj->lista(null, $this->ref_ref_cod_instituicao, null,null, $alocacao['ref_cod_escola'], $this->ref_cod_servidor, null,null,null,null,1,$alocacao['carga_horaria'])){
					//$obj->edita();

					//return true;
					$substituiu = $obj->substituir_servidor($substituto);
					if( !$substituiu )
					{
						$this->mensagem = "Substituicao n&atilde;o realizado.<br>";
						echo "<!--\nErro ao substituir servidor ref_cod_servidor($this->ref_cod_servidor) ref_cod_servidor_substituto_({$_POST["ref_cod_servidor_substituto_$key"]})\n-->";
						return false;

					}
				}
					/*$obj_serv = new clsPmieducarServidor($this->ref_cod_servidor,null,null,null,null,null,null,1,$this->ref_ref_cod_instituicao);
					$det_serv = $obj_serv->detalhe();
					if($det_serv['ref_cod_funcao'])
					{
						$obj_funcao = new clsPmieducarFuncao($det_serv['ref_cod_funcao'],null,null,null,null,null,null,null,1,$this->ref_ref_cod_instituicao);
						$det_funcao = $obj_funcao->detalhe();
						//if($det_funca)
					}*/
				//}
			}
			/**
			 * SUBSTITUICAO NO QUADRO DE HORARIO PARA SERVIDORES QUE SAO PROFESSORES
			 */
			if($this->professor == "true"){
				$obj_quadro_horario = new clsPmieducarQuadroHorarioHorarios(null,null,null,null,null,null,$this->ref_ref_cod_instituicao,null,$this->ref_cod_servidor,null,null,null,null,1,null,null);
				$obj_quadro_horario->substituir_servidor($substituto);
			}
		}

		$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
		header( "Location: educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao} ");
		die();
		return true;
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