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
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Faltas/Notas Aluno" );
		$this->processoAp = "642";
	}
}

class indice extends clsListagem
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	/**
	 * Quantidade de registros a ser apresentada em cada pagina
	 *
	 * @var int
	 */
	var $limite;

	/**
	 * Inicio dos registros a serem exibidos (limit)
	 *
	 * @var int
	 */
	var $offset;

	var $ref_ref_cod_escola;
	var $ref_cod_matricula;
	var $ref_cod_turma;

	var $ref_cod_instituicao;
	var $ref_cod_escola;
	var $ref_cod_curso;
	var $ref_ref_cod_serie;

	var $ref_cod_aluno;
	var $nm_aluno;
	var $aprovado;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();
/*
		$obj_func = new clsMenuFuncionario($this->pessoa_logada,false,false,0);
		$det_func = $obj_func->detalhe();
		if (!$det_func)
		{
			echo "Desculpe-nos o transtorno. Página fora do ar temporariamente!";
//			header("location:educar_index.php");
			die();
		}
*/

		$this->titulo = "Faltas/Notas Aluno - Listagem";

//		echo "<pre>";print_r($_GET);

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$lista_busca = array(
			"Aluno",
			"Matr&iacute;cula",
			"Turma",
			"S&eacute;rie",
			"Curso"
		);

		$obj_permissao = new clsPermissoes();
		$nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
		{
			$lista_busca[] = "Escola";
			$lista_busca[] = "Institui&ccedil;&atilde;o";
		}
		else if ($nivel_usuario == 2)
		{
			$lista_busca[] = "Escola";
		}
		$this->addCabecalhos($lista_busca);

		$this->campoTexto("nm_aluno", "Aluno", $this->nm_aluno, 30, 255, false, false, false, "", "<img border=\"0\" onclick=\"pesquisa_aluno();\" id=\"ref_cod_aluno_lupa\" name=\"ref_cod_aluno_lupa\" src=\"imagens/lupa.png\"\/>", "", "", true);
		$this->campoOculto("ref_cod_aluno", $this->ref_cod_aluno);

		$get_escola = true;
//		$get_escola_curso = true;
		$get_curso = true;
		$sem_padrao = true;
		$get_escola_curso_serie = true;
		$get_turma = true;
		include("include/pmieducar/educar_campo_lista.php");

		if ( $this->ref_cod_escola )
		{
			$this->ref_ref_cod_escola = $this->ref_cod_escola;
		}

		$opcoes = array( '' => 'Selecione', 1 => 'Aprovado', 2 => 'Reprovado', 3 => 'Em Andamento' );
		$this->campoLista( 'aprovado', 'Situa&ccedil;&atilde;o', $opcoes, $this->aprovado, '','','','',false,false );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_nota_aluno = new clsPmieducarMatriculaTurma();
		$obj_nota_aluno->setOrderby( "ref_cod_matricula ASC" );
		$obj_nota_aluno->setLimite( $this->limite, $this->offset );

//		if ($this->pessoa_logada==184580) {
			$aparece=true;
//		} else {
//			$aparece=false;
//		}
		$lista = $obj_nota_aluno->lista(
			$this->ref_cod_matricula,
			$this->ref_cod_turma,
			null,
			null,
			null,
			null,
			null,
			null,
			1,
			$this->ref_ref_cod_serie,
			$this->ref_cod_curso,
			$this->ref_ref_cod_escola,
			$this->ref_cod_instituicao,
			$this->ref_cod_aluno,
			null,
			$this->aprovado,
			null,
			null,
			null,
			true,
			false,
			null,
			1,
			true,
			true
			
			,null,null,null,null,$aparece
		);

		$total = $obj_nota_aluno->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			$ref_cod_serie= "";
			$nm_serie = "";
			$ref_cod_escola= "";
			$nm_escola = "";
			
			
			foreach ( $lista AS $registro )
			{
				
				if(  $registro["ref_ref_cod_serie"]  != '' && $ref_cod_serie !=  $registro["ref_ref_cod_serie"] )
				{
					$obj_ref_cod_serie = new clsPmieducarSerie( $registro["ref_ref_cod_serie"] );
					$det_ref_cod_serie = $obj_ref_cod_serie->detalhe();
					$ref_cod_serie = $registro["ref_ref_cod_serie"];
					$nm_serie = $det_ref_cod_serie["nm_serie"];
				}elseif ($registro["ref_ref_cod_serie"]  == '')
				{
					$ref_cod_serie = "";
					$nm_serie = "";
				}


				if(  $registro["ref_ref_cod_escola"]  != '' && $ref_cod_escola !=  $registro["ref_ref_cod_escola"]  )
				{
					$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_ref_cod_escola"] );
					$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
					$ref_cod_escola = $registro["ref_ref_cod_escola"];
					$nm_escola = $det_ref_cod_escola["nome"];
				}elseif ($registro["ref_ref_cod_escola"]  == '')
				{
					$ref_cod_escola = "";
					$nm_escola = "";
				}
				$lista_busca = array(
					"<a href=\"educar_falta_nota_aluno_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}\">{$registro["nome"]}</a>",
					"<a href=\"educar_falta_nota_aluno_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}\">{$registro["ref_cod_matricula"]}</a>",
					"<a href=\"educar_falta_nota_aluno_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}\">{$registro["nm_turma"]}</a>"
				);

				if ($registro["ref_ref_cod_serie"])
					$lista_busca[] = "<a href=\"educar_falta_nota_aluno_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}\">{$nm_serie}</a>";
				else
					$lista_busca[] = "<a href=\"educar_falta_nota_aluno_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}\">-</a>";

				$lista_busca[] = "<a href=\"educar_falta_nota_aluno_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}\">{$registro["nm_curso"]}</a>";

				if ($nivel_usuario == 1)
				{
					if ($registro["ref_ref_cod_escola"])
						$lista_busca[] = "<a href=\"educar_falta_nota_aluno_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}\">{$nm_escola}</a>";
					else
						$lista_busca[] = "<a href=\"educar_falta_nota_aluno_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}\">-</a>";

					$lista_busca[] = "<a href=\"educar_falta_nota_aluno_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}\">{$registro["nm_instituicao"]}</a>";
				}
				else if ($nivel_usuario == 2)
				{
					if ($registro["ref_ref_cod_escola"])
						$lista_busca[] = "<a href=\"educar_falta_nota_aluno_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}\">{$registro["ref_ref_cod_escola"]}</a>";
					else
						$lista_busca[] = "<a href=\"educar_falta_nota_aluno_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}&sequencial={$registro["sequencial"]}\">-</a>";
				}
				$this->addLinhas($lista_busca);
			}
		}
		$this->addPaginador2( "educar_falta_nota_aluno_lst.php", $total, $_GET, $this->nome, $this->limite );
		$this->largura = "100%";
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

function pesquisa_aluno()
{
	pesquisa_valores_popless('educar_pesquisa_aluno.php')
}


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