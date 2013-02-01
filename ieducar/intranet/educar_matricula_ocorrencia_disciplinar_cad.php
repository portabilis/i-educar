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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Ocorr&ecirc;ncia Disciplinar" );
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

	var $ref_cod_matricula;
	var $ref_cod_tipo_ocorrencia_disciplinar;
	var $sequencial;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $observacao;
	var $data_exclusao;
	var $ativo;

	var $data_cadastro;
	var $ref_cod_instituicao;
	var $ref_cod_escola;

	var $hora_cadastro;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->ref_cod_matricula = $_GET["ref_cod_matricula"];
		if(!$this->ref_cod_matricula)
			header("location: educar_matricula_lst.php");

		$obj_permissoes = new clsPermissoes();

		$obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_matricula_lst.php" );

		$data = getdate();

		$data['mday'] = sprintf("%02d",$data['mday']);
		$data['mon'] = sprintf("%02d",$data['mon']);
		$data['hours'] = sprintf("%02d",$data['hours']);
		$data['minutes'] = sprintf("%02d",$data['minutes']);

		$this->data_cadastro = "{$data['mday']}/{$data['mon']}/{$data['year']}";
		$this->hora_cadastro = "{$data['hours']}:{$data['minutes']}";

		$this->sequencial=$_GET["sequencial"];
		$this->ref_cod_matricula=$_GET["ref_cod_matricula"];
		$this->ref_cod_tipo_ocorrencia_disciplinar=$_GET["ref_cod_tipo_ocorrencia_disciplinar"];

		if (is_numeric($this->ref_cod_matricula) &&
		    is_numeric($this->ref_cod_tipo_ocorrencia_disciplinar) &&
		    is_numeric($this->sequencial))
		{
			$obj = new clsPmieducarMatriculaOcorrenciaDisciplinar($this->ref_cod_matricula, $this->ref_cod_tipo_ocorrencia_disciplinar, $this->sequencial);
			$registro = $obj->detalhe();
			if ($registro)
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$this->hora_cadastro = dataFromPgToBr($this->data_cadastro,'H:i');
				$this->data_cadastro = dataFromPgToBr($this->data_cadastro);

			  $obj_permissoes = new clsPermissoes();
			  if( $obj_permissoes->permissao_excluir( 578, $this->pessoa_logada, 7 ) )
			  {
				  $this->fexcluir = true;
			  }

				$retorno = "Editar";
			}
		}

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
			$this->ref_cod_escola = $detalhe_aluno['ref_ref_cod_escola'];

			$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
			$det_escola = $obj_escola->detalhe();
			$this->ref_cod_instituicao = $det_escola['ref_cod_instituicao'];



		$this->url_cancelar = ($retorno == "Editar") ? "educar_matricula_ocorrencia_disciplinar_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_tipo_ocorrencia_disciplinar={$registro["ref_cod_tipo_ocorrencia_disciplinar"]}&sequencial={$registro["sequencial"]}" : "educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$this->ref_cod_matricula}";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		/**
		 * Busca nome do aluno
		 */
			if( class_exists( "clsPmieducarMatricula" ) )
			{
				$obj_ref_cod_matricula = new clsPmieducarMatricula();
				$detalhe_aluno = $obj_ref_cod_matricula->lista($this->ref_cod_matricula);
				if($detalhe_aluno)
					$detalhe_aluno = array_shift($detalhe_aluno);
			}
			else
			{
				$registro["ref_cod_matricula"] = "Erro na geracao";
				echo "<!--\nErro\nClasse nao existente: clsPmieducarMatricula\n-->";
			}

			$obj_aluno = new clsPmieducarAluno();
			$det_aluno = array_shift($det_aluno = $obj_aluno->lista($detalhe_aluno['ref_cod_aluno'],null,null,null,null,null,null,null,null,null,1));

			$this->campoRotulo("nm_pessoa","Nome do Aluno",$det_aluno['nome_aluno']);
		/**
		 *
		 */

		// primary keys
		$this->campoOculto( "ref_cod_matricula", $this->ref_cod_matricula );
		$this->campoOculto( "ref_cod_tipo_ocorrencia_disciplinar", $this->ref_cod_tipo_ocorrencia_disciplinar );
		$this->campoOculto( "sequencial", $this->sequencial );

		$this->campoData("data_cadastro","Data Atual",$this->data_cadastro,true);
		$this->campoHora("hora_cadastro","Horas",$this->hora_cadastro,true);

		// foreign keys
	/*	$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarMatricula" ) )
		{
			$objTemp = new clsPmieducarMatricula();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_matricula']}"] = "{$registro['ref_ref_cod_escola']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarMatricula nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_matricula", "Matricula", $opcoes, $this->ref_cod_matricula );
		*/

		//$opcoes = array('' => 'Selecione um aluno clicando na lupa');
		//$this->campoListaPesq("nm_aluno", "Aluno", $opcoes,$this->ref_cod_matricula,"educar_pesquisa_matricula_lst.php","",false,"","",null,"","",true);
		//$this->campoOculto("ref_cod_aluno", $this->ref_cod_aluno);



		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarTipoOcorrenciaDisciplinar" ) )
		{
			$objTemp = new clsPmieducarTipoOcorrenciaDisciplinar();
			$lista = $objTemp->lista(null,null,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao);
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_tipo_ocorrencia_disciplinar']}"] = "{$registro['nm_tipo']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTipoOcorrenciaDisciplinar nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_tipo_ocorrencia_disciplinar", "Tipo Ocorrencia Disciplinar", $opcoes, $this->ref_cod_tipo_ocorrencia_disciplinar );


		// text
		$this->campoMemo( "observacao", "Observac&atilde;o", $this->observacao, 60, 10, true );

		$this->campoCheck("visivel_pais", 
						  "Visível aos pais",
						  $this->visivel_pais,
						  "Marque este campo, caso deseje que os pais do aluno possam visualizar tal ocorrência disciplinar.");

		// data

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_matricula_ocorrencia_disciplinar_lst.php" );

		$this->visivel_pais = is_null($this->visivel_pais) ? 0 : 1;

		$obj = new clsPmieducarMatriculaOcorrenciaDisciplinar( $this->ref_cod_matricula, $this->ref_cod_tipo_ocorrencia_disciplinar, null, $this->pessoa_logada, $this->pessoa_logada, $this->observacao, $this->getDataHoraCadastro(), $this->data_exclusao, $this->ativo, $this->visivel_pais);
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$this->ref_cod_matricula}" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarMatriculaOcorrenciaDisciplinar\nvalores obrigatorios\nis_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_tipo_ocorrencia_disciplinar ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_usuario_cad ) && is_string( $this->observacao )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_matricula_ocorrencia_disciplinar_lst.php" );

		$this->visivel_pais = is_null($this->visivel_pais) ? 0 : 1;

    	$obj = new clsPmieducarMatriculaOcorrenciaDisciplinar($this->ref_cod_matricula, $this->ref_cod_tipo_ocorrencia_disciplinar, $this->sequencial, $this->pessoa_logada, $this->pessoa_logada, $this->observacao, $this->getDataHoraCadastro(), $this->data_exclusao, $this->ativo, $this->visivel_pais);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$this->ref_cod_matricula}" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarMatriculaOcorrenciaDisciplinar\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_tipo_ocorrencia_disciplinar ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 578, $this->pessoa_logada, 7,  "educar_matricula_ocorrencia_disciplinar_lst.php" );


		$obj = new clsPmieducarMatriculaOcorrenciaDisciplinar($this->ref_cod_matricula, $this->ref_cod_tipo_ocorrencia_disciplinar, $this->sequencial, $this->pessoa_logada, $this->pessoa_logada, $this->observacao, $this->data_cadastro, $this->data_exclusao, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$this->ref_cod_matricula}" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarMatriculaOcorrenciaDisciplinar\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_tipo_ocorrencia_disciplinar ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
	}

  protected function getDataHoraCadastro() {
    return $this->data_cadastro = dataToBanco($this->data_cadastro) . " " . $this->hora_cadastro;
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
