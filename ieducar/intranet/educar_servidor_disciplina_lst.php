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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Servidor Disciplina" );
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
	var $cursos_disciplina;


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
		$this->cursos_disciplina = $_SESSION['cursos_disciplina'];
		@session_write_close();

		if(!$this->cursos_disciplina)
		{
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

		}

		if($this->cursos_disciplina)
		{
			foreach ($this->cursos_disciplina as $curso => $disciplinas)
			{
				if($disciplinas)
				{
					foreach ($disciplinas as $disciplina)
					{
						$this->ref_cod_curso[] = $curso;
						$this->ref_cod_disciplina[] = $disciplina;
					}
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

		$obj_disciplina = new clsPmieducarDisciplina();
		$obj_disciplina->setOrderby("nm_disciplina");
		$lst_opcoes = array();
		$arr_valores = array();


		if($this->cursos_disciplina)
		{
			foreach ($this->cursos_disciplina as $curso => $disciplinas)
			{
				if($disciplinas)
				{
					foreach ($disciplinas as $disciplina)
					{
						$arr_valores[] = array($curso,$disciplina);
					}
				}
			}
		}


		if ($this->ref_cod_curso)
		{
			foreach ($this->ref_cod_curso as $curso)
			{
				$lst_disciplinas = $obj_disciplina->lista(null,null,null,null,null,null,null,null,null,null,null,null,1,null,$curso,$this->ref_cod_instituicao);
				$opcoes_disc = array();
				foreach ($lst_disciplinas as $disciplina)
				{

					$opcoes_disc[$disciplina['cod_disciplina']]	= $disciplina['nm_disciplina'];
				}
				$lst_opcoes[] = array($opcoes_curso,$opcoes_disc);
			}
		}

		$this->campoTabelaInicio("funcao","Disciplinas",array("Curso","Disciplina"),$arr_valores,"",$lst_opcoes);

			$this->campoLista( "ref_cod_curso", "Curso", $opcoes_curso, $this->ref_cod_curso,"trocaCurso(this)","","","" );
			$this->campoLista( "ref_cod_disciplina", "Disciplina", $opcoes, $this->ref_cod_disciplina,"","","","" );

		$this->campoTabelaFim();



	}

	function Novo()
	{


		$cursos_disciplina = array();
		@session_start();
		$curso_servidor = $_SESSION['cursos_servidor'];

		if ($this->ref_cod_curso)
		{
			foreach ($this->ref_cod_curso as $key => $curso)
			{
				$curso_servidor[$curso] = $curso;
				foreach ($this->ref_cod_disciplina as $disciplina)
				{
					$cursos_disciplina[$curso][$disciplina] = $disciplina;
				}
			}
		}

		$_SESSION['cursos_disciplina'] = $cursos_disciplina;
		$_SESSION['cod_servidor']      = $this->cod_servidor;
		$_SESSION['cursos_servidor']   = $curso_servidor;
		@session_write_close();

		echo "<script>parent.fechaExpansivel( '{$_GET['div']}');</script>";
		die;


		return true;
	}

	function Editar()
	{
		return false;
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
<script>

	function trocaCurso(id_campo)
	{

		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
		var campoCurso = document.getElementById(id_campo.id).value;
		var id = /[0-9]+/.exec(id_campo.id);
		var campoDisciplina	= document.getElementById('ref_cod_disciplina['+id+']');
		campoDisciplina.length = 1;

		if( campoDisciplina )
		{
			campoDisciplina.disabled = true;
			campoDisciplina.options[0].text = 'Carregando Disciplinas';

			var xml = new ajax(atualizaLstDisciplina,'ref_cod_disciplina['+id+']');
			xml.envia("educar_disciplina_xml.php?cur="+campoCurso);
		}
		else
		{
			campoFuncao.options[0].text = 'Selecione';
		}
	}

	function atualizaLstDisciplina(xml)
	{

		var campoDisciplina = document.getElementById(arguments[1]);

		campoDisciplina.length = 1;
		campoDisciplina.options[0].text = 'Selecione uma Disciplina';
		campoDisciplina.disabled = false;

		var disciplinas = xml.getElementsByTagName('disciplina');
		if(disciplinas.length)
		{
			for( var i = 0; i < disciplinas.length; i++ )
			{
				campoDisciplina.options[campoDisciplina.options.length] = new Option( disciplinas[i].firstChild.data, disciplinas[i].getAttribute('cod_disciplina'),false,false);
			}
		}
		else
		{
			campoDisciplina.options[0].text = 'A instituição não possui nenhuma disciplina';
		}


	}

	tab_add_1.afterAddRow = function () { }

	window.onload = function()
	{
		//trocaTodasfuncoes();
	}

	function trocaTodasfuncoes()
	{
		for(var ct=0;ct<tab_add_1.id;ct++)
		{
			getFuncao('ref_cod_funcao['+ct+']');
		}
	}

	/*if ( document.getElementById( 'ref_cod_instituicao' ) ) {
		var ref_cod_instituicao = document.getElementById( 'ref_cod_instituicao' );
		ref_cod_instituicao.onchange = function() { trocaTodasfuncoes(); }
	}*/



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