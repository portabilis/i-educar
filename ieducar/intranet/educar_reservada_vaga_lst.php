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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Vagas Reservadas" );
		$this->processoAp = "639";
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

	var $cod_reserva_vaga;
	var $ref_ref_cod_escola;
	var $ref_ref_cod_serie;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_aluno;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	var $ref_cod_escola;
	var $ref_cod_curso;
	var $ref_cod_instituicao;
	var $nm_aluno;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Vagas Reservadas - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$lista_busca = array(
			"Aluno",
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

		$get_escola = true;
//		$get_escola_curso = true;
		$get_curso = true;
		$get_escola_curso_serie = true;
		include("include/pmieducar/educar_campo_lista.php");

		if ( $this->ref_cod_escola )
		{
			$this->ref_ref_cod_escola = $this->ref_cod_escola;
		}

		$this->campoTexto("nm_aluno", "Aluno", $this->nm_aluno, 30, 255, false, false, false, "", "<img border=\"0\" onclick=\"pesquisa_aluno();\" id=\"ref_cod_aluno_lupa\" name=\"ref_cod_aluno_lupa\" src=\"imagens/lupa.png\"\/>");
		$this->campoOculto("ref_cod_aluno", $this->ref_cod_aluno);

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_reserva_vaga = new clsPmieducarReservaVaga();
		$obj_reserva_vaga->setOrderby( "data_cadastro ASC" );
		$obj_reserva_vaga->setLimite( $this->limite, $this->offset );

		$lista = $obj_reserva_vaga->lista(
			$this->cod_reserva_vaga,
			$this->ref_ref_cod_escola,
			$this->ref_ref_cod_serie,
			null,
			null,
			$this->ref_cod_aluno,
			null,
			null,
			null,
			null,
			1,
			$this->ref_cod_instituicao,
			$this->ref_cod_curso
		);

		$total = $obj_reserva_vaga->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				if( class_exists( "clsPmieducarSerie" ) )
				{
					$obj_serie = new clsPmieducarSerie( $registro["ref_ref_cod_serie"] );
					$det_serie = $obj_serie->detalhe();
					$nm_serie = $det_serie["nm_serie"];
				}
				else
				{
					$registro["ref_ref_cod_serie"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarSerie\n-->";
				}
				if( class_exists( "clsPmieducarCurso" ) )
				{
					$obj_curso = new clsPmieducarCurso( $registro["ref_cod_curso"] );
					$det_curso = $obj_curso->detalhe();
					$registro["ref_cod_curso"] = $det_curso["nm_curso"];
				}
				else
				{
					$registro["ref_cod_serie"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarSerie\n-->";
				}
				if( class_exists( "clsPmieducarEscola" ) )
				{
					$obj_escola = new clsPmieducarEscola( $registro["ref_ref_cod_escola"] );
					$det_escola = $obj_escola->detalhe();
					$nm_escola = $det_escola["nome"];
				}
				else
				{
					$registro["ref_ref_cod_escola"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
				}
				if( class_exists( "clsPmieducarInstituicao" ) )
				{
					$obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
					$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
					$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
				}
				else
				{
					$registro["ref_cod_escola"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
				}
				if( class_exists( "clsPmieducarAluno" ) )
				{
					$obj_aluno = new clsPmieducarAluno( $registro["ref_cod_aluno"] );
					$det_aluno = $obj_aluno->detalhe();
					$ref_idpes = $det_aluno["ref_idpes"];

					if( class_exists( "clsPessoa_" ) )
					{
						$obj_pessoa = new clsPessoa_( $ref_idpes );
						$det_pessoa = $obj_pessoa->detalhe();
						$registro["ref_cod_aluno"] = $det_pessoa["nome"];
					}
					else
					{
						$registro["ref_cod_aluno"] = "Erro na gera&ccedil;&atilde;o";
						echo "<!--\nErro\nClasse n&atilde;o existente: clsPessoa_\n-->";
					}
				}
				else
				{
					$registro["ref_cod_aluno"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarAluno\n-->";
				}

				$lista_busca = array(
					"<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro["cod_reserva_vaga"]}\">{$registro["ref_cod_aluno"]}</a>",
					"<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro["cod_reserva_vaga"]}\">{$nm_serie}</a>",
					"<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro["cod_reserva_vaga"]}\">{$registro["ref_cod_curso"]}</a>"
				);

				if ($nivel_usuario == 1)
				{
					$lista_busca[] = "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro["cod_reserva_vaga"]}\">{$nm_escola}</a>";
					$lista_busca[] = "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro["cod_reserva_vaga"]}\">{$registro["ref_cod_instituicao"]}</a>";
				}
				else if ($nivel_usuario == 2)
				{
					$lista_busca[] = "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro["cod_reserva_vaga"]}\">{$nm_escola}</a>";
				}
				$this->addLinhas($lista_busca);
			}
		}
		$this->addPaginador2( "educar_reservada_vaga_lst.php", $total, $_GET, $this->nome, $this->limite );
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

document.getElementById('ref_cod_escola').onchange = function()
{
	getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
	getEscolaCursoSerie();
}

function pesquisa_aluno()
{
	pesquisa_valores_popless('educar_pesquisa_aluno.php')
}

</script>