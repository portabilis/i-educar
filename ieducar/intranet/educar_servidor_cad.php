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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Servidor" );
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

	var $cod_servidor;
	var $ref_cod_instituicao;
	var $ref_cod_deficiencia;
	var $ref_idesco;
	var $ref_cod_funcao = array();
	var $carga_horaria;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_instituicao_original;

	var $total_horas_alocadas;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada 	   			= $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_servidor		   			= $_GET["cod_servidor"];
		$this->ref_cod_instituicao 			= $_GET["ref_cod_instituicao"];
		$this->ref_cod_instituicao_original = $_GET["ref_cod_instituicao"];
		if ( $_POST["ref_cod_instituicao_original"] )
			$this->ref_cod_instituicao_original = $_POST["ref_cod_instituicao_original"];

		$obj_permissoes = new clsPermissoes();

		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 3,  "educar_servidor_lst.php" );

		if( is_numeric( $this->cod_servidor ) && is_numeric( $this->ref_cod_instituicao ) )
		{

			$obj = new clsPmieducarServidor( $this->cod_servidor, null, null, null, null, null, null, $this->ref_cod_instituicao );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$obj_permissoes = new clsPermissoes();
				if( $obj_permissoes->permissao_excluir( 635, $this->pessoa_logada, 3 ) )
				{
					$this->fexcluir = true;
				}

				$db = new clsBanco();
				$consulta = "SELECT substr(coalesce(sum(carga_horaria),'00:00'),0,6) as horas_utilizadas
							   FROM pmieducar.servidor_alocacao
							  WHERE ref_cod_servidor = '{$this->cod_servidor}'
							    AND ativo            = 1";

				$this->total_horas_alocadas = $db->CampoUnico($consulta);

				$obj_funcoes = new clsPmieducarServidorFuncao();
				$lst_funcoes = $obj_funcoes->lista($this->ref_cod_instituicao,$this->cod_servidor);

				if($lst_funcoes)
				{
					foreach ($lst_funcoes as $funcao)
					{
						$obj_funcao = new clsPmieducarFuncao($funcao['ref_cod_funcao']);
						$det_funcao = $obj_funcao->detalhe();

						$this->ref_cod_funcao[] = array("{$funcao['ref_cod_funcao']}-{$det_funcao['professor']}");

					}
				}

				$obj_servidor_disciplina = new clsPmieducarServidorDisciplina();
				$lst_servidor_disciplina = $obj_servidor_disciplina->lista(null,$this->ref_cod_instituicao,$this->cod_servidor);

				if($lst_servidor_disciplina)
				{
					foreach ($lst_servidor_disciplina as $disciplina)
					{
						$obj_disciplina = new clsPmieducarDisciplina($disciplina['ref_cod_disciplina']);
						$det_disciplina = $obj_disciplina->detalhe();
						$this->cursos_disciplina[$det_disciplina['ref_cod_curso']][$disciplina['ref_cod_disciplina']] = $disciplina['ref_cod_disciplina'];
					}
				}

				@session_start();

				if($_SESSION['cod_servidor'] == $this->cod_servidor)
					$_SESSION['cursos_disciplina'] = $this->cursos_disciplina;
				else
					unset($_SESSION['cursos_disciplina']);

				@session_write_close();

				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_servidor_det.php?cod_servidor={$this->cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" : "educar_servidor_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// foreign keys
		$obrigatorio 	 = true;
		$get_instituicao = true;
		//$get_funcao		 = true;
		include("include/pmieducar/educar_campo_lista.php");

		$opcoes = array( "" => "Pesquise o funcionario clicando na lupa ao lado" );
		if( $this->cod_servidor )
		{
			$objTemp = new clsFuncionario( $this->cod_servidor );
			$detalhe = $objTemp->detalhe();
			$detalhe = $detalhe["idpes"]->detalhe();
			$this->campoRotulo( "nm_servidor", "Servidor", $detalhe["nome"] );
			$this->campoOculto( "cod_servidor", $this->cod_servidor );
			$this->campoOculto( "ref_cod_instituicao_original", $this->ref_cod_instituicao_original );
		}
		else {
			$parametros = new clsParametrosPesquisas();
			$parametros->setSubmit( 0 );
			$parametros->adicionaCampoSelect( "cod_servidor", "ref_cod_pessoa_fj", "nome" );
			$this->campoListaPesq( "cod_servidor", "Servidor", $opcoes, $this->cod_servidor, "pesquisa_funcionario_lst.php", "", false, "", "", null, null, "", false, $parametros->serializaCampos(), true );
		}


		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsCadastroDeficiencia" ) )
		{
			$objTemp = new clsCadastroDeficiencia();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_deficiencia']}"] = "{$registro['nm_deficiencia']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsCadastroDeficiencia nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}

		$script = "javascript:showExpansivelIframe(350, 100, 'educar_deficiencia_cad_pop.php');";
		$script = "<img id='img_deficiencia' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
		$this->campoLista( "ref_cod_deficiencia", "Defici&ecirc;ncia", $opcoes, $this->ref_cod_deficiencia, "", false, "", $script, false, false );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsCadastroEscolaridade" ) )
		{
			$objTemp = new clsCadastroEscolaridade();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['idesco']}"] = "{$registro['descricao']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsCadastroEscolaridade nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		
		$script = "javascript:showExpansivelIframe(350, 100, 'educar_escolaridade_cad_pop.php');";
		$script = "<img id='img_deficiencia' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
		
		$this->campoLista( "ref_idesco", "Escolaridade", $opcoes, $this->ref_idesco,"",false,"",$script,false,false );

		/**
		 *
		 */
		$opcoes = array( "" => "Selecione" );

		if( class_exists( "clsPmieducarFuncao" ) )
		{
			if (is_numeric($this->ref_cod_instituicao))
			{
				$objTemp = new clsPmieducarFuncao();
				$objTemp->setOrderby("nm_funcao ASC");
				$lista = $objTemp->lista(null,null,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao);
				if ( is_array( $lista ) && count( $lista ) )
				{
					foreach ( $lista as $registro )
					{
						$opcoes["{$registro['cod_funcao']}-{$registro['professor']}"] = "{$registro['nm_funcao']}";
					}
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarFuncao nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}

		$this->campoTabelaInicio("funcao","Funções Servidor",array("Fun&ccedil&atilde;o","Disciplinas","Cursos"),($this->ref_cod_funcao));

			$funcao = "popless()";
//			$this->campoLista( "ref_cod_funcao", "Fun&ccedil&atilde;o", $opcoes, $this->ref_cod_funcao,"funcaoChange(this)","","","<img src='imagens/lupa_antiga.png' border='0' style='cursor:pointer;' alt='Buscar Disciplina' title='Buscar Disciplina' onclick=\"$funcao\">" );
			$this->campoLista( "ref_cod_funcao", "Fun&ccedil&atilde;o", $opcoes, $this->ref_cod_funcao,"funcaoChange(this)","","","" );

			$this->campoRotulo("disciplina", "Disciplinas","<img src='imagens/lupa_antiga.png' border='0' style='cursor:pointer;' alt='Buscar Disciplina' title='Buscar Disciplina' onclick=\"$funcao\">");

			$funcao = "popCurso()";

			$this->campoRotulo("curso", "Curso","<img src='imagens/lupa_antiga.png' border='0' style='cursor:pointer;' alt='Buscar Cursos' title='Buscar Cursos' onclick=\"$funcao\">");

		$this->campoTabelaFim();

		if(strtoupper($this->tipoacao) == "EDITAR"){
			$this->campoTextoInv('total_horas_alocadas_',"Total de Horas Alocadadas",$this->total_horas_alocadas,9,20);
			$hora = explode( ":", $this->total_horas_alocadas );
			$this->total_horas_alocadas = $hora[0] + ( $hora[1] / 60 );
			$this->campoOculto( 'total_horas_alocadas', $this->total_horas_alocadas );
			$this->acao_enviar = 'acao2()';
		}
		// text
		if($this->carga_horaria)
		{
			$horas = (int)$this->carga_horaria;
			$minutos =  round(($this->carga_horaria - (int)$this->carga_horaria) * 60);
			$hora_formatada = sprintf("%02d:%02d",$horas,$minutos);
		}

		$this->campoHora( "carga_horaria", "Carga Horária",$hora_formatada , true,"Número de horas deve ser maior que horas alocadas" );

	}

	function Novo()
	{

		$timesep = explode(":",$this->carga_horaria); // split time into hr, min, sec
		$hour = $timesep[0] + ((int)($timesep[1] / 60));
		$min = abs(((int)($timesep[1] / 60)) - ($timesep[1] / 60)) .'<br>';
		$this->carga_horaria = $hour + $min;
		$this->carga_horaria = $hour + $min;

		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 3,  "educar_servidor_lst.php" );

		$obj = new clsPmieducarServidor( $this->cod_servidor, null, null, null, null, null, null, $this->ref_cod_instituicao );
		if ( $obj->detalhe() ) {
			$this->carga_horaria = str_replace( ",", ".", $this->carga_horaria );
			$obj = new clsPmieducarServidor( $this->cod_servidor, $this->ref_cod_deficiencia, $this->ref_idesco, $this->carga_horaria, null, null, 1, $this->ref_cod_instituicao );
			$editou = $obj->edita();
			if ( $editou ) {

				$this->cadastraFuncoes();

				include('educar_limpa_sessao_curso_disciplina_servidor.php');

				$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
				header( "Location: educar_servidor_lst.php" );
				die();
				return true;
			}
		}
		else {
			$this->carga_horaria = str_replace( ",", ".", $this->carga_horaria );
			$obj_2 = new clsPmieducarServidor( $this->cod_servidor, $this->ref_cod_deficiencia, $this->ref_idesco, $this->carga_horaria, null, null, 1, $this->ref_cod_instituicao );
			$cadastrou = $obj_2->cadastra();
			if( $cadastrou )
			{
				$this->cadastraFuncoes();

				include('educar_limpa_sessao_curso_disciplina_servidor.php');


				$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
				header( "Location: educar_servidor_det.php?cod_servidor={$this->cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );
				die();
				return true;
			}
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarServidor\nvalores obrigatorios\nis_numeric( $this->cod_servidor ) && is_numeric( $this->ref_cod_deficiencia ) && is_numeric( $this->ref_idesco ) && is_numeric( $this->ref_cod_funcao ) && is_numeric( $this->carga_horaria )\n-->";
		return false;
	}

	function Editar()
	{
		$timesep = explode(":",$this->carga_horaria); // split time into hr, min, sec
		$hour = $timesep[0] + ((int)($timesep[1] / 60));
		$min = abs(((int)($timesep[1] / 60)) - ($timesep[1] / 60)) .'<br>';
		$this->carga_horaria = $hour + $min;
		$this->carga_horaria = $hour + $min;

		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 3,  "educar_servidor_lst.php" );
		if ( $this->ref_cod_instituicao == $this->ref_cod_instituicao_original ) {
			$this->carga_horaria = str_replace( ",", ".", $this->carga_horaria );
			$obj = new clsPmieducarServidor( $this->cod_servidor, $this->ref_cod_deficiencia, $this->ref_idesco, $this->carga_horaria, null, null, 1, $this->ref_cod_instituicao );
			$editou = $obj->edita();
			if( $editou )
			{
				$this->cadastraFuncoes();

				include('educar_limpa_sessao_curso_disciplina_servidor.php');

				$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
				header( "Location: educar_servidor_det.php?cod_servidor={$this->cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );
				die();
				return true;
			}
		}
		else {
			$obj_quadro_horario = new clsPmieducarQuadroHorarioHorarios( null, null, null, null, null, null, $this->cod_servidor, null, null, null, null, null, null, 1, $this->ref_cod_instituicao );
			if ( $obj_quadro_horario->detalhe() ) {
				$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada. O servidor est&acute; vinculado a um quadro de hor&acute;rios.<br>";
				echo "<!--\nErro ao editar clsPmieducarServidor\nvalores obrigatorios\nif( is_numeric( $this->cod_servidor ) )\n-->";
				return false;
			}
			else {
				$obj_quadro_horario = new clsPmieducarQuadroHorarioHorarios( null, null, null, null, null, null, null, $this->cod_servidor, null, null, null, null, null, 1, null, $this->ref_cod_instituicao );
				if ( $obj_quadro_horario->detalhe() ) {
					$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada. O servidor est&acute; vinculado a um quadro de hor&acute;rios.<br>";
					echo "<!--\nErro ao editar clsPmieducarServidor\nvalores obrigatorios\nif( is_numeric( $this->cod_servidor ) )\n-->";
					return false;
				}
				else {
					$this->carga_horaria = str_replace( ",", ".", $this->carga_horaria );
					$obj = new clsPmieducarServidor( $this->cod_servidor, $this->ref_cod_deficiencia, $this->ref_idesco, $this->carga_horaria, null, null, 0, $this->ref_cod_instituicao_original );
					$editou = $obj->edita();
					if( $editou )
					{
						$obj = new clsPmieducarServidor( $this->cod_servidor, $this->ref_cod_deficiencia, $this->ref_idesco, $this->carga_horaria, null, null, 1, $this->ref_cod_instituicao );

						if($obj->existe())
							$cadastrou = $obj->edita();
						else
							$cadastrou = $obj->cadastra();
						if ( $cadastrou ) {

							$this->cadastraFuncoes();

							include('educar_limpa_sessao_curso_disciplina_servidor.php');

							$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
								header( "Location: educar_servidor_det.php?cod_servidor={$this->cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );
							die();
							return true;
						}
					}
				}
			}
		}
		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarServidor\nvalores obrigatorios\nif( is_numeric( $this->cod_servidor ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 635, $this->pessoa_logada, 3,  "educar_servidor_lst.php" );

		$obj_quadro_horario = new clsPmieducarQuadroHorarioHorarios( null, null, null, null, null, null, $this->cod_servidor, null, null, null, null, null, null, 1, $this->ref_cod_instituicao );
		if ( $obj_quadro_horario->detalhe() ) {
			$this->mensagem = "Exclus&atilde;o n&atilde;o realizada. O servidor est&acute; vinculado a um quadro de hor&acute;rios.<br>";
			echo "<!--\nErro ao editar clsPmieducarServidor\nvalores obrigatorios\nif( is_numeric( $this->cod_servidor ) )\n-->";
			return false;
		}
		else {
			$obj_quadro_horario = new clsPmieducarQuadroHorarioHorarios( null, null, null, null, null, null, null, $this->cod_servidor, null, null, null, null, null, 1, null, $this->ref_cod_instituicao );
			if ( $obj_quadro_horario->detalhe() ) {
				$this->mensagem = "Exclus&atilde;o n&atilde;o realizada. O servidor est&acute; vinculado a um quadro de hor&acute;rios.<br>";
				echo "<!--\nErro ao editar clsPmieducarServidor\nvalores obrigatorios\nif( is_numeric( $this->cod_servidor ) )\n-->";
				return false;
			}
			else {
				$obj = new clsPmieducarServidor( $this->cod_servidor, $this->ref_cod_deficiencia, $this->ref_idesco, $this->carga_horaria, null, null, 0, $this->ref_cod_instituicao_original );
				$excluiu = $obj->excluir();
				if( $excluiu )
				{
					$this->excluiFuncoes();
					$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
					header( "Location: educar_servidor_lst.php" );
					die();
					return true;
				}
			}
		}
		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarServidor\nvalores obrigatorios\nif( is_numeric( $this->cod_servidor ) )\n-->";
		return false;
	}

	function cadastraFuncoes()
	{
		@session_start();
		$cursos_disciplina = $_SESSION['cursos_disciplina'];
		$cursos_servidor = $_SESSION['cursos_servidor'];
		@session_write_close();


		$this->excluiFuncoes();

		$this->excluiCursos();

		$existe_funcao_professor = false;

		if($this->ref_cod_funcao)
		{
			foreach ($this->ref_cod_funcao as $funcao)
			{
				$funcao_professor = explode("-",$funcao);
				$funcao = array_shift($funcao_professor);
				$professor = array_shift($funcao_professor);

				if($professor)
					$existe_funcao_professor = true;

				$obj_servidor_funcao = new clsPmieducarServidorFuncao($this->ref_cod_instituicao,$this->cod_servidor,$funcao);
				if(!$obj_servidor_funcao->existe())
					$obj_servidor_funcao->cadastra();
			}
		}

		//$obj_servidor_disciplina = new clsPmieducarServidorDisciplina(null,$this->ref_cod_instituicao,$this->cod_servidor);
		//$obj_servidor_disciplina->excluirTodos();

		if($existe_funcao_professor)
		{
			if($cursos_disciplina )
			{
				foreach ($cursos_disciplina as $curso => $disciplinas)
				{
					if($disciplinas)
					{
						foreach ($disciplinas as $disciplina)
						{
							$obj_servidor_disciplina = new clsPmieducarServidorDisciplina($disciplina,$this->ref_cod_instituicao,$this->cod_servidor);
							if(!$obj_servidor_disciplina->existe())
								$obj_servidor_disciplina->cadastra();
						}
					}
				}
			}

			if($cursos_servidor)
			{
				foreach ($cursos_servidor as $curso)
				{
					$obj_curso_servidor = new clsPmieducarServidorCursoMinistra($curso,$this->ref_cod_instituicao,$this->cod_servidor);
					if(!$obj_curso_servidor->existe())
						$det_curso_servidor = $obj_curso_servidor->cadastra();
				}
			}
		}
	}

	function excluiFuncoes()
	{
		$obj_servidor_disciplina = new clsPmieducarServidorDisciplina(null,$this->ref_cod_instituicao,$this->cod_servidor);
		$obj_servidor_disciplina->excluirTodos();

		$obj_servidor_funcao = new clsPmieducarServidorFuncao($this->ref_cod_instituicao,$this->cod_servidor);
		$obj_servidor_funcao->excluirTodos();
	}

	function excluiCursos()
	{
		$obj_servidor_curso = new clsPmieducarServidorCursoMinistra(null,$this->ref_cod_instituicao,$this->cod_servidor);
		$obj_servidor_curso->excluirTodos();
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

	function getFuncao(id_campo)
	{
		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
		var campoFuncao	= document.getElementById(id_campo);
		campoFuncao.length = 1;

		if( campoFuncao )
		{
			campoFuncao.disabled = true;
			campoFuncao.options[0].text = 'Carregando funções';

			var xml = new ajax(atualizaLstFuncao,id_campo);
			xml.envia("educar_funcao_xml.php?ins="+campoInstituicao+"&professor=true");
		}
		else
		{
			campoFuncao.options[0].text = 'Selecione';
		}
	}

	function atualizaLstFuncao(xml)
	{

		var campoFuncao = document.getElementById(arguments[1]);

		campoFuncao.length = 1;
		campoFuncao.options[0].text = 'Selecione uma função';
		campoFuncao.disabled = false;

		funcaoChange(campoFuncao);

		var funcoes = xml.getElementsByTagName('funcao');
		if(funcoes.length)
		{
			for( var i = 0; i < funcoes.length; i++ )
			{
				campoFuncao.options[campoFuncao.options.length] = new Option( funcoes[i].firstChild.data, funcoes[i].getAttribute('cod_funcao'),false,false);
			}
		}
		else
		{
			campoFuncao.options[0].text = 'A instituição não possui nenhuma função';
		}


	}

	function funcaoChange(campo)
	{
		var valor = campo.value.split("-");
		var id = /[0-9]+/.exec(campo.id)[0];
		var professor = valor[1] == true;

		var campo_img = document.getElementById('td_disciplina[0]').lastChild.lastChild;
		var campo_img2 = document.getElementById('td_curso[0]').lastChild.lastChild;

		//this.previousSibling.previousSibling.id

		//professor
		if(professor)
		{
			//campo_img = campo_img.lastChild;

			//document.getElementById('novo_func_tab['+(/[0-9]+/.exec(campo.id))+']').value = document.getElementById('novo_func_tab['+(/[0-9]+/.exec(campo.id))+']').value == '' ? 's' : document.getElementById('novo_func_tab['+(/[0-9]+/.exec(campo.id))+']').value == 'n' ? 's' : 'n';

			/*while(campo_img.tagName != 'IMG')
			{
				campo_img = campo_img.nextSibling;
			}*/
			setVisibility(campo_img, true);
			setVisibility(campo_img2, true);
		}
		else
		{
			//campo_img = campo_img.childNodes[0];

			//document.getElementById('novo_func_tab['+(/[0-9]+/.exec(campo.id))+']').value = document.getElementById('novo_func_tab['+(/[0-9]+/.exec(campo.id))+']').value == '' ? 's' : document.getElementById('novo_func_tab['+(/[0-9]+/.exec(campo.id))+']').value == 'n' ? 's' : 'n';

			/*while(campo_img.tagName != 'IMG')
			{
				campo_img = campo_img.nextSibling;
			}*/

			setVisibility(campo_img, false);
			setVisibility(campo_img2, false);
			//!getVisibility(campo)

		}

	}
	function popless()
	{
		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
		var campoServidor = document.getElementById('cod_servidor').value;
		pesquisa_valores_popless1('educar_servidor_disciplina_lst.php?ref_cod_servidor='+campoServidor+'&ref_cod_instituicao='+campoInstituicao, '');
	}

	function popCurso()
	{
		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
		var campoServidor = document.getElementById('cod_servidor').value;
		pesquisa_valores_popless('educar_servidor_curso_lst.php?ref_cod_servidor='+campoServidor+'&ref_cod_instituicao='+campoInstituicao, '');
	}

	function pesquisa_valores_popless1(caminho, campo)
	{
		new_id = DOM_divs.length;
		div = 'div_dinamico_' + new_id;
		if ( caminho.indexOf( '?' ) == -1 )
			showExpansivel( 850, 500, '<iframe src="' + caminho + '?campo=' + campo + '&div=' + div + '&popless=1" frameborder="0" height="100%" width="100%" marginheight="0" marginwidth="0" name="temp_win_popless"></iframe>', 'Pesquisa de valores' );
		else
			showExpansivel( 850, 500, '<iframe src="' + caminho + '&campo=' + campo + '&div=' + div + '&popless=1" frameborder="0" height="100%" width="100%" marginheight="0" marginwidth="0" name="temp_win_popless"></iframe>', 'Pesquisa de valores' );
	}
	
	tab_add_1.afterAddRow = function () { funcaoChange(document.getElementById('ref_cod_funcao['+(tab_add_1.id-1)+']')); getFuncao('ref_cod_funcao['+(tab_add_1.id-1)+']');}

	window.onload = function()
	{
		trocaTodasfuncoes();
	}

	function trocaTodasfuncoes()
	{
		for(var ct=0;ct<tab_add_1.id;ct++)
		{
			//funcaoChange(document.getElementById('ref_cod_funcao['+ct+']'));
			funcaoChange(document.getElementById('ref_cod_funcao['+ct+']'));
			//getFuncao('ref_cod_funcao['+ct+']');
		}
	}

	if ( document.getElementById( 'ref_cod_instituicao' ) ) {
		var ref_cod_instituicao = document.getElementById( 'ref_cod_instituicao' );
		ref_cod_instituicao.onchange = function() {
			trocaTodasfuncoes();
			var xml = new ajax(function(){});
			xml.envia("educar_limpa_sessao_curso_disciplina_servidor.php");}
	}

	function getArrayHora(hora){
		var array_h;
		if(hora)
			array_h = hora.split(":");
		else
			array_h = new Array(0,0);

		return array_h;

	}

	function acao2()
	{
		var total_horas_alocadas = getArrayHora( document.getElementById( 'total_horas_alocadas' ).value );

		var carga_horaria = ( document.getElementById( 'carga_horaria' ).value ).replace( ',', '.' );

		//var horas_trabalhadas = Date.UTC( 1970, 01, 01, parseInt( total_horas_alocadas[0], 10 ), parseInt( total_horas_alocadas[1], 10 ), 0 );

		//var total_horas = Date.UTC( 1970, 01, 01, parseInt( carga_horaria, 10 ), ( carga_horaria - parseInt( carga_horaria, 10 ) ) * 60, 0 );

		if( parseFloat( total_horas_alocadas ) > parseFloat( carga_horaria ) )
		{
			alert( 'Atenção, carga horária deve ser maior que horas alocadas!' );
			return false;

		}
		else
		{
			acao();
		}
	}

	if ( document.getElementById('total_horas_alocadas') )
		document.getElementById('total_horas_alocadas').style.textAlign='right';
</script>