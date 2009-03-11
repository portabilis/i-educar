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
/**
 * @author Adriano Erik Weiguert Nagasava
 */
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Quadro de Hor&aacute;rios" );
		$this->processoAp = "641";
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

	var $ref_cod_turma;
	var $ref_ref_cod_serie;
	var $ref_cod_curso;
	var $ref_cod_escola;
	var $ref_cod_instituicao;
	var $cod_quadro_horario;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $data_cadastra;
	var $data_exclusao;
	var $ativo;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->ref_cod_turma  	   = $_GET["ref_cod_turma"];
		$this->ref_ref_cod_serie   = $_GET["ref_cod_serie"];
		$this->ref_cod_curso  	   = $_GET["ref_cod_curso"];
		$this->ref_cod_escola 	   = $_GET["ref_cod_escola"];
		$this->ref_cod_instituicao = $_GET["ref_cod_instituicao"];
		$this->cod_quadro_horario  = $_GET["ref_cod_quadro_horario"];

		if ( is_numeric( $this->cod_quadro_horario ) )
		{
			$obj_quadro_horario = new clsPmieducarQuadroHorario( $this->cod_quadro_horario );
			$det_quadro_horario = $obj_quadro_horario->detalhe();
			if ( $det_quadro_horario )
			{
				foreach( $det_quadro_horario AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$obj_permissoes = new clsPermissoes();
				if ( $obj_permissoes->permissao_excluir( 641, $this->pessoa_logada, 7 ) )
				{
					$this->fexcluir = true;
				}

				$retorno = "Editar";
			}
		}

		$obj_permissoes = new clsPermissoes();

		$obj_permissoes->permissao_cadastra( 641, $this->pessoa_logada, 7,  "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" );

		$this->url_cancelar = ( $retorno == "Editar" ) ? "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" : "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		if ( $this->retorno == "Editar" )
		{
			$this->Excluir();
		}
		// primary keys
		$this->campoOculto( "cod_quadro_horario", $this->cod_quadro_horario );

		$obrigatorio 	 		= true;
		$get_escola 			= true;
//		$get_escola_curso 		= true;
		$get_curso 				= true;
		$get_escola_curso_serie = true;
		$get_turma				= true;
		include( "include/pmieducar/educar_campo_lista.php" );

		$this->url_cancelar = ($retorno == "Editar") ? "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" : "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}";
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 641, $this->pessoa_logada, 7,  "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" );

		$obj   = new clsPmieducarQuadroHorario();
		$lista = $obj->lista( null, null, $this->pessoa_logada, $this->ref_cod_turma, null, null, null, null, 1 );
		if ( $lista )
		{
			echo "<script>alert( 'Quadro de Horário já cadastrado para esta turma' );</script>";
			return false;
		}
		$obj 	   = new clsPmieducarQuadroHorario( null, null, $this->pessoa_logada, $this->ref_cod_turma, null, null, 1 );
		$cadastrou = $obj->cadastra();
		if ( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&busca=S" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro não realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarCalendarioAnoLetivo\nvalores obrigatorios\nis_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ano ) && is_string( $this->inicio_ano_letivo ) && is_string( $this->termino_ano_letivo )\n-->";
		return false;
	}

	function Editar()
	{
		/*@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 641, $this->pessoa_logada, 7,  "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" );


		$obj = new clsPmieducarCalendarioAnoLetivo($this->cod_calendario_ano_letivo, $this->ref_cod_escola, $this->pessoa_logada, $this->pessoa_logada, $this->ano, $this->data_cadastra, $this->data_exclusao, $this->ativo, $this->inicio_ano_letivo, $this->termino_ano_letivo);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarCalendarioAnoLetivo\nvalores obrigatorios\nif( is_numeric( $this->cod_calendario_ano_letivo ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;*/
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 641, $this->pessoa_logada, 7,  "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" );

		if ( is_numeric( $this->cod_quadro_horario ) )
		{
			$obj_horarios = new clsPmieducarQuadroHorarioHorarios( $this->cod_quadro_horario, null, null, null, null, null, null, null, null, null, null, null, null, null, 1 );
			if ( $obj_horarios->excluirTodos() )
			{
				$obj_quadro = new clsPmieducarQuadroHorario( $this->cod_quadro_horario, $this->pessoa_logada );
				if ( $obj_quadro->excluir() )
				{
					$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
					header( "Location: educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" );
					die();
					return true;
				}
			}
		}
		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
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
document.getElementById('ref_cod_escola').onchange = function()
{
	getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
	getEscolaCursoSerie();
}

document.getElementById('ref_ref_cod_serie').onchange = function()
{
	getTurma();
}
</script>