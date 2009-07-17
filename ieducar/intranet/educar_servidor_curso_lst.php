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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Servidor Curso" );
		$this->processoAp = "0";
		$this->renderBanner = false;
		$this->renderMenu   = false;
		$this->renderMenuSuspenso = false;

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
	var $ref_cod_funcao;
	var $carga_horaria;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_curso;
	var $ref_cod_disciplina;
	var $cursos_servidor;


	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada 	   			= $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_servidor		   			= $_GET["ref_cod_servidor"];
		$this->ref_cod_instituicao 			= $_GET["ref_cod_instituicao"];

		$obj_permissoes = new clsPermissoes();

		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 3,  "educar_servidor_lst.php" );

		if( is_numeric( $this->cod_servidor ) && is_numeric( $this->ref_cod_instituicao ) )
		{

			$obj = new clsPmieducarServidor( $this->cod_servidor, null, null, null, null, null, null, $this->ref_cod_instituicao );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				//foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					//$this->$campo = $val;

				/*$obj_permissoes = new clsPermissoes();
				if( $obj_permissoes->permissao_excluir( 635, $this->pessoa_logada, 3 ) )
				{
					$this->fexcluir = true;
				}*/

				$retorno = "Editar";
			}
		}

		@session_start();
		$this->cursos_servidor = $_SESSION['cursos_servidor'];
		@session_write_close();

		if(!$this->cursos_servidor)
		{
			$obj_servidor_curso = new clsPmieducarServidorCursoMinistra();
			$lst_servidor_curso = $obj_servidor_curso->lista(null,$this->ref_cod_instituicao,$this->cod_servidor);

			if($lst_servidor_curso)
			{
				foreach ($lst_servidor_curso as $curso)
				{
					$this->cursos_servidor[$curso['ref_cod_curso']] = $curso['ref_cod_curso'];
				}
			}

		}


		//$this->script_cancelar = "parent.fechaExpansivel( \"{$_GET['div']}\");";
		//$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// foreign keys
		//$obrigatorio 	 = true;
	//	$get_instituicao = true;
		//$get_funcao		 = true;
		//include("include/pmieducar/educar_campo_lista.php");


		$this->campoOculto("ref_cod_instituicao",$this->ref_cod_instituicao);
		$opcoes = $opcoes_curso = array('' => "Selecione");

		$obj_cursos = new clsPmieducarCurso();
		$obj_cursos->setOrderby("nm_curso");
		$lst_cursos = $obj_cursos->lista(null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,1,null,$this->ref_cod_instituicao);
		if ($lst_cursos)
		{
			foreach ($lst_cursos as $curso)
			{
				$opcoes_curso[$curso['cod_curso']] = $curso['nm_curso'];
			}
		}

		$arr_valores = array();


		if($this->cursos_servidor)
		{
			foreach ($this->cursos_servidor as $curso)
			{
				$arr_valores[] = array($curso);
			}
		}


		$this->campoTabelaInicio("cursos_ministra","Cursos Ministrados",array("Curso"),$arr_valores,"");

			$this->campoLista( "ref_cod_curso", "Curso", $opcoes_curso, $this->ref_cod_curso,"","","","" );

		$this->campoTabelaFim();



	}

	function Novo()
	{

		$curso_servidor = array();
		if($this->ref_cod_curso)
		{
			foreach ($this->ref_cod_curso as $curso)
			{
				$curso_servidor[$curso] = $curso;
			}
		}

		@session_start();
		$_SESSION['cursos_servidor'] = $curso_servidor;
		$_SESSION['cod_servidor']    = $this->cod_servidor;
		@session_write_close();

		echo "<script>parent.fechaExpansivel( '{$_GET['div']}');</script>";
		die;


		return true;
	}

  public function Editar() {
    return $this->Novo();
  }

	function Excluir()
	{
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
