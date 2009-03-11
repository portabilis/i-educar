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
//die("Desativada temporiariamente!");
/**
 * @author Adriano Erik Weiguert Nagasava
 */
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Turma" );
		$this->processoAp = "650";
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

	var $cod_turma;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_ref_cod_serie;
	var $ref_ref_cod_escola;
	var $ref_cod_infra_predio_comodo;
	var $nm_turma;
	var $sgl_turma;
	var $max_aluno;
	var $multiseriada;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_turma_tipo;
	var $hora_inicial;
	var $hora_final;
	var $hora_inicio_intervalo;
	var $hora_fim_intervalo;

	var $ref_cod_instituicao;
	var $ref_cod_curso;
	var $ref_cod_escola;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Turma - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$lista_busca = array(
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

		$get_escola = true;
		$get_curso = true;
		$get_escola_curso_serie = true;
		include("include/pmieducar/educar_campo_lista.php");

		if ( $this->ref_cod_escola )
		{
			$this->ref_ref_cod_escola = $this->ref_cod_escola;
		}

		$this->campoTexto( "nm_turma", "Turma", $this->nm_turma, 30, 255, false );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_turma = new clsPmieducarTurma();
		$obj_turma->setOrderby( "nm_turma ASC" );
		$obj_turma->setLimite( $this->limite, $this->offset );

		$lista = $obj_turma->lista(
			null,
			null,
			null,
			$this->ref_ref_cod_serie,
			$this->ref_ref_cod_escola,
			null,
			$this->nm_turma,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			1,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			$this->ref_cod_curso,
			$this->ref_cod_instituicao,
			null,
			null,
			null,
			null,
			1,
			false,
			true
		);

		$total = $obj_turma->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				// pega detalhes de foreign_keys
//				$obj_ref_ref_cod_serie = new clsPmieducarEscolaSerie( $registro["ref_ref_cod_escola"], $registro["ref_ref_cod_serie"] );
//				$det_ref_ref_cod_serie = $obj_ref_ref_cod_serie->detalhe();
//				$registro["ref_ref_cod_serie"] = $det_ref_ref_cod_serie["ref_cod_serie"];

//				$obj_ref_cod_curso = new clsPmieducarCurso( $registro["ref_cod_curso"] );
//				$det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
//				$registro["ref_cod_curso"] = $det_ref_cod_curso["nm_curso"];

				$obj_ser = new clsPmieducarSerie( $registro["ref_ref_cod_serie"], null, null, $this->ref_cod_curso );
				$det_ser = $obj_ser->detalhe();
				$obj_cur = new clsPmieducarCurso( $det_ser["ref_cod_curso"] );
				$det_cur = $obj_cur->detalhe();
					
				$lista_busca = array(
					"<a href=\"educar_turma_mvto_det.php?cod_turma={$registro["cod_turma"]}\">{$registro["nm_turma"]}</a>",
					"<a href=\"educar_turma_mvto_det.php?cod_turma={$registro["cod_turma"]}\">{$det_ser["nm_serie"]}</a>",
					"<a href=\"educar_turma_mvto_det.php?cod_turma={$registro["cod_turma"]}\">{$det_cur["nm_curso"]}</a>"
				);

				if ($nivel_usuario == 1)
				{
					$obj_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
					$obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
					$registro["ref_cod_instituicao"] = $obj_cod_instituicao_det["nm_instituicao"];

					$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_ref_cod_escola"] );
					$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
					$registro["ref_ref_cod_escola"] = $det_ref_cod_escola["nome"];

					$lista_busca[] = "<a href=\"educar_turma_mvto_det.php?cod_turma={$registro["cod_turma"]}\">{$registro["ref_ref_cod_escola"]}</a>";
					$lista_busca[] = "<a href=\"educar_turma_mvto_det.php?cod_turma={$registro["cod_turma"]}\">{$registro["ref_cod_instituicao"]}</a>";
				}
				else if ($nivel_usuario == 2)
				{
					$lista_busca[] = "<a href=\"educar_turma_mvto_det.php?cod_turma={$registro["cod_turma"]}\">{$registro["ref_ref_cod_escola"]}</a>";
				}
				$this->addLinhas($lista_busca);
			}
		}

		$this->addPaginador2( "educar_turma_mvto_lst.php", $total, $_GET, $this->nome, $this->limite );
		$obj_permissoes = new clsPermissoes();
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

</script>