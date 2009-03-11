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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Dispensa Disciplina" );
		$this->processoAp = "578";
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

	/*var $ref_ref_cod_turma;
	var $ref_ref_cod_matricula;*/

	/*var $disc_ref_ref_cod_turma;
	var $disc_ref_ref_cod_serie;
	var $disc_ref_ref_cod_escola;
	var $disc_ref_ref_cod_disciplina;
	*/
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_tipo_dispensa;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $observacao;

	var $ref_cod_matricula;
	var $ref_cod_turma;
	var $ref_cod_serie;
	var $ref_cod_disciplina;
	var $ref_sequencial;
	var $ref_cod_instituicao;
	var $ref_cod_escola;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->ref_cod_disciplina=$_GET["ref_cod_disciplina"];
		$this->ref_cod_matricula = $_GET['ref_cod_matricula'];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_dispensa_disciplina_lst.php?ref_ref_cod_matricula={$this->ref_cod_matricula}" );

		if(is_numeric($this->ref_cod_matricula))
		{
			$obj_matricula = new clsPmieducarMatricula($this->ref_cod_matricula,null,null,null,null,null,null,null,null,null,1);
			$det_matricula = $obj_matricula->detalhe();
			if(!$det_matricula)
			{
				header("location: educar_matricula_lst.php");
				die;

			}

			$this->ref_cod_escola = $det_matricula['ref_ref_cod_escola'];
			$this->ref_cod_serie = $det_matricula['ref_ref_cod_serie'];
		}else
		{
			header("location: educar_matricula_lst.php");
			die;
		}
		if( is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_disciplina ) )
		{

			$obj = new clsPmieducarDispensaDisciplina( $this->ref_cod_matricula, $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_disciplina );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

			$obj_permissoes = new clsPermissoes();
			if( $obj_permissoes->permissao_excluir( 578, $this->pessoa_logada, 7 ) )
			{
				$this->fexcluir = true;
			}

				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_dispensa_disciplina_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_serie={$registro["ref_cod_serie"]}&ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_disciplina={$registro["ref_cod_disciplina"]}" : "educar_dispensa_disciplina_lst.php?ref_cod_matricula={$this->ref_cod_matricula}";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		/**
		 * Busca dados da matricula
		 */
			if( class_exists( "clsPmieducarMatricula" ) )
			{
				$obj_ref_cod_matricula = new clsPmieducarMatricula();
				$detalhe_aluno = array_shift($obj_ref_cod_matricula->lista($this->ref_cod_matricula));
			}
			else
			{
				$registro["ref_cod_matricula"] = "Erro na geracao";
				echo "<!--\nErro\nClasse nao existente: clsPmieducarMatricula\n-->";
			}

//			echo "<pre>";print_r($detalhe_aluno);

			$obj_aluno = new clsPmieducarAluno();
			$det_aluno = array_shift($det_aluno = $obj_aluno->lista($detalhe_aluno['ref_cod_aluno'],null,null,null,null,null,null,null,null,null,1));

			$obj_escola = new clsPmieducarEscola($this->ref_cod_escola,null,null,null,null,null,null,null,null,null,1);
			$det_escola = $obj_escola->detalhe();
			$this->ref_cod_instituicao = $det_escola["ref_cod_instituicao"];


			$this->campoRotulo("nm_aluno","Nome do Aluno",$det_aluno['nome_aluno']);

//			echo $this->ref_cod_matricula;
//			echo $this->ref_cod_turma;
			$obj_matricula_turma = new clsPmieducarMatriculaTurma();
			$lst_matricula_turma = $obj_matricula_turma->lista( $this->ref_cod_matricula,null,null,null,null,null,null,null,1,$this->ref_cod_serie,null,$this->ref_cod_escola );
			if (is_array($lst_matricula_turma))
			{
				$det = array_shift($lst_matricula_turma);
				$this->ref_cod_turma = $det["ref_cod_turma"];
				$this->ref_sequencial = $det["sequencial"];

			}
//			echo $this->ref_cod_turma;
//			echo "seq".$this->ref_sequencial;

		/**
		 *
		 */

		// primary keys
		//$this->campoOculto( "ref_cod_turma", $this->ref_ref_cod_turma );
		$this->campoOculto( "ref_cod_matricula", $this->ref_cod_matricula );
//		$this->campoOculto( "ref_cod_turma", $this->ref_cod_turma );
		$this->campoOculto( "ref_cod_serie", $this->ref_cod_serie );
		$this->campoOculto( "ref_cod_escola", $this->ref_cod_escola );
//		$this->campoOculto( "ref_sequencial", $this->ref_sequencial );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarEscolaSerieDisciplina" ) )
		{
			$objTemp = new clsPmieducarEscolaSerieDisciplina();
			$lista = $objTemp->lista( $this->ref_cod_serie,$this->ref_cod_escola,null,1 );
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$obj_disciplina = new clsPmieducarDisciplina($registro['ref_cod_disciplina'],null,null,null,null,null,null,null,null,null,1);
					$det_disciplina = $obj_disciplina->detalhe();
					$opcoes["{$registro['ref_cod_disciplina']}"] = "{$det_disciplina['nm_disciplina']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTurmaDisciplina nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		if ($this->ref_cod_disciplina)
		{
			$this->campoRotulo( "nm_disciplina", "Disciplina", $opcoes[$this->ref_cod_disciplina] );
			$this->campoOculto( "ref_cod_disciplina", $this->ref_cod_disciplina );
		}
		else
			$this->campoLista( "ref_cod_disciplina", "Disciplina", $opcoes, $this->ref_cod_disciplina );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarTipoDispensa" ) )
		{
			$objTemp = new clsPmieducarTipoDispensa();
			if ($this->ref_cod_instituicao)
				$lista = $objTemp->lista( null,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao );
			else
				$lista = $objTemp->lista( null,null,null,null,null,null,null,null,null,1 );
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_tipo_dispensa']}"] = "{$registro['nm_tipo']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTipoDispensa nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_tipo_dispensa", "Tipo Dispensa", $opcoes, $this->ref_cod_tipo_dispensa );

		// text
		$this->campoMemo( "observacao", "Observa&ccedil;&atilde;o", $this->observacao, 60, 10, false );

		// data

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_dispensa_disciplina_lst.php?ref_cod_matricula={$this->ref_cod_matricula}" );

		$sql = "SELECT MAX(cod_dispensa) + 1 FROM pmieducar.dispensa_disciplina";
		$db = new clsBanco();
		$max_cod_dispensa = $db->CampoUnico($sql);
		
		$obj = new clsPmieducarDispensaDisciplina( $this->ref_cod_matricula, $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_disciplina, null, $this->pessoa_logada, $this->ref_cod_tipo_dispensa, null, null, 1, $this->observacao, $max_cod_dispensa );
		if($obj->existe())
		{
			$obj = new clsPmieducarDispensaDisciplina( $this->ref_cod_matricula, $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_disciplina, $this->pessoa_logada, null, $this->ref_cod_tipo_dispensa, null, null, 1, $this->observacao );
			$obj->edita();
			header( "Location: educar_dispensa_disciplina_lst.php?ref_cod_matricula={$this->ref_cod_matricula}" );
			die();
		}
			
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_dispensa_disciplina_lst.php?ref_cod_matricula={$this->ref_cod_matricula}" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarDispensaDisciplina\nvalores obrigatorios\n is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_disciplina ) && is_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_cod_tipo_dispensa )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_dispensa_disciplina_lst.php?ref_cod_matricula={$this->ref_cod_matricula}" );

		$obj = new clsPmieducarDispensaDisciplina( $this->ref_cod_matricula, $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_disciplina, $this->pessoa_logada, null, $this->ref_cod_tipo_dispensa, null, null, 1, $this->observacao );
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_dispensa_disciplina_lst.php?ref_cod_matricula={$this->ref_cod_matricula}" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarDispensaDisciplina\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_disciplina ) && is_numeric( $this->pessoa_logada ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 578, $this->pessoa_logada, 7,  "educar_dispensa_disciplina_lst.php?ref_cod_matricula={$this->ref_cod_matricula}" );


		$obj = new clsPmieducarDispensaDisciplina( $this->ref_cod_matricula, $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_disciplina, $this->pessoa_logada, null, $this->ref_cod_tipo_dispensa, null, null, 0, $this->observacao );
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_dispensa_disciplina_lst.php?ref_cod_matricula={$this->ref_cod_matricula}" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarDispensaDisciplina\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_disciplina ) && is_numeric( $this->pessoa_logada ) )\n-->";
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
var campoTurma = document.getElementById('ref_cod_turma');
var campoDisciplina = document.getElementById('ref_cod_disciplina');
campoTurma.onchange = function(){
						campoDisciplina.options.length = 1;
						for(var ct=0;ct<disciplina.length;ct++){
							if((campoTurma.options[campoTurma.selectedIndex].value.split("-"))[0] == disciplina[ct][0])
								campoDisciplina.options[campoDisciplina.length] = new Option(disciplina[ct][2],disciplina[ct][1],false,false);
						}
					  };
</script>