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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Curso" );
		$this->processoAp = "566";
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

	var $cod_curso;
	var $ref_usuario_cad;
	var $ref_cod_tipo_regime;
	var $ref_cod_nivel_ensino;
	var $ref_cod_tipo_ensino;
	var $ref_cod_tipo_avaliacao;
	var $nm_curso;
	var $sgl_curso;
	var $qtd_etapas;
	var $frequencia_minima;
	var $media;
	var $media_exame;
	var $falta_ch_globalizada;
	var $carga_horaria;
	var $ato_poder_publico;
	var $habilitacao;
	var $edicao_final;
	var $objetivo_curso;
	var $publico_alvo;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_usuario_exc;
	var $ref_cod_instituicao;
	var $padrao_ano_escolar;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Curso - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$lista_busca = array(
			"Curso",
			"N&iacute;vel Ensino",
			"Tipo Ensino"
		);

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
			$lista_busca[] = "Institui&ccedil;&atilde;o";

		$this->addCabecalhos($lista_busca);

		// Filtros de Foreign Keys
//		if ($nivel_usuario == 1)
//		{
//			$opcoes = array( "" => "Selecione" );
//			if( class_exists( "clsPmieducarInstituicao" ) )
//			{
//				$obj_instituicao = new clsPmieducarInstituicao(null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,1);
//				$lista = $obj_instituicao->lista();
//				if ( is_array( $lista ) && count( $lista ) )
//				{
//					foreach ( $lista as $registro )
//					{
//						$opcoes["{$registro['cod_instituicao']}"] = "{$registro['nm_instituicao']}";
//					}
//				}
//			}
//			else
//			{
//				echo "<!--\nErro\nClasse clsPmieducarInstituicao n&atilde;o encontrada\n-->";
//				$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
//			}
//			$this->campoLista( "ref_cod_instituicao", "Institui&ccedil;&atilde;o", $opcoes, $this->ref_cod_instituicao, null, null, null, null, null, false );
//		}
//		else if ($nivel_usuario == 2)
//		{
//			$obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
//			$obj_usuario_det = $obj_usuario->detalhe();
//			$this->ref_cod_instituicao = $obj_usuario_det["ref_cod_instituicao"];
//		}
		include("include/pmieducar/educar_campo_lista.php");

		// outros Filtros
		$this->campoTexto( "nm_curso", "Curso", $this->nm_curso, 30, 255, false );


		// outros de Foreign Keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarNivelEnsino" ) )
		{
			$todos_niveis_ensino = "nivel_ensino = new Array();\n";
			$objTemp = new clsPmieducarNivelEnsino();
			$lista = $objTemp->lista( null,null,null,null,null,null,null,null,null,1 );
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$todos_niveis_ensino .= "nivel_ensino[nivel_ensino.length] = new Array({$registro["cod_nivel_ensino"]},'{$registro["nm_nivel"]}', {$registro["ref_cod_instituicao"]});\n";
				}
			}
			echo "<script>{$todos_niveis_ensino}</script>";

			// EDITAR
			if ($this->ref_cod_instituicao)
			{
				$objTemp = new clsPmieducarNivelEnsino();
				$lista = $objTemp->lista( null,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao );
				if ( is_array( $lista ) && count( $lista ) )
				{
					foreach ( $lista as $registro )
					{
						$opcoes["{$registro['cod_nivel_ensino']}"] = "{$registro['nm_nivel']}";
					}
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarNivelEnsino n&atilde;o encontrada\n-->";
			$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}
		$this->campoLista( "ref_cod_nivel_ensino", "N&iacute;vel Ensino", $opcoes, $this->ref_cod_nivel_ensino, null, null, null, null, null, false );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarTipoEnsino" ) )
		{
			$todos_tipos_ensino = "tipo_ensino = new Array();\n";
			$objTemp = new clsPmieducarTipoEnsino();
			$objTemp->setOrderby("nm_tipo");
			$lista = $objTemp->lista( null,null,null,null,null,null,1 );
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$todos_tipos_ensino .= "tipo_ensino[tipo_ensino.length] = new Array({$registro["cod_tipo_ensino"]},'{$registro["nm_tipo"]}', {$registro["ref_cod_instituicao"]});\n";
				}
			}
			echo "<script>{$todos_tipos_ensino}</script>";

			// EDITAR
			if ($this->ref_cod_instituicao)
			{
				$objTemp = new clsPmieducarTipoEnsino();
				$objTemp->setOrderby("nm_tipo");
				$lista = $objTemp->lista( null,null,null,null,null,null,1,$this->ref_cod_instituicao );
				if ( is_array( $lista ) && count( $lista ) )
				{
					foreach ( $lista as $registro )
					{
						$opcoes["{$registro['cod_tipo_ensino']}"] = "{$registro['nm_tipo']}";
					}
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTipoEnsino n&atilde;o encontrada\n-->";
			$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}
		$this->campoLista( "ref_cod_tipo_ensino", "Tipo Ensino", $opcoes, $this->ref_cod_tipo_ensino,"",false,"","","",false );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_curso = new clsPmieducarCurso();
		$obj_curso->setOrderby( "nm_curso ASC" );
		$obj_curso->setLimite( $this->limite, $this->offset );

		$lista = $obj_curso->lista(
			null,
			null,
			null,
			$this->ref_cod_nivel_ensino,
			$this->ref_cod_tipo_ensino,
			null,
			$this->nm_curso,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			1,
			null,
			$this->ref_cod_instituicao
		);

		$total = $obj_curso->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				if( class_exists( "clsPmieducarNivelEnsino" ) )
				{
					$obj_ref_cod_nivel_ensino = new clsPmieducarNivelEnsino( $registro["ref_cod_nivel_ensino"] );
					$det_ref_cod_nivel_ensino = $obj_ref_cod_nivel_ensino->detalhe();
					$registro["ref_cod_nivel_ensino"] = $det_ref_cod_nivel_ensino["nm_nivel"];
				}
				else
				{
					$registro["ref_cod_nivel_ensino"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarNivelEnsino\n-->";
				}

				if( class_exists( "clsPmieducarTipoEnsino" ) )
				{
					$obj_ref_cod_tipo_ensino = new clsPmieducarTipoEnsino( $registro["ref_cod_tipo_ensino"] );
					$det_ref_cod_tipo_ensino = $obj_ref_cod_tipo_ensino->detalhe();
					$registro["ref_cod_tipo_ensino"] = $det_ref_cod_tipo_ensino["nm_tipo"];
				}
				else
				{
					$registro["ref_cod_tipo_ensino"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarTipoEnsino\n-->";
				}
				if( class_exists( "clsPmieducarInstituicao" ) )
				{
					$obj_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
					$obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
					$registro["ref_cod_instituicao"] = $obj_cod_instituicao_det["nm_instituicao"];
				}
				else
				{
					$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
				}

				$lista_busca = array(
					"<a href=\"educar_curso_det.php?cod_curso={$registro["cod_curso"]}\">{$registro["nm_curso"]}</a>",
					"<a href=\"educar_curso_det.php?cod_curso={$registro["cod_curso"]}\">{$registro["ref_cod_nivel_ensino"]}</a>",
					"<a href=\"educar_curso_det.php?cod_curso={$registro["cod_curso"]}\">{$registro["ref_cod_tipo_ensino"]}</a>"
				);

				if ($nivel_usuario == 1)
					$lista_busca[] = "<a href=\"educar_curso_det.php?cod_curso={$registro["cod_curso"]}\">{$registro["ref_cod_instituicao"]}</a>";
				$this->addLinhas($lista_busca);

			}
		}
		$this->addPaginador2( "educar_curso_lst.php", $total, $_GET, $this->nome, $this->limite );

		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 566, $this->pessoa_logada, 3 ) ) {
			$this->acao = "go(\"educar_curso_cad.php\")";
			$this->nome_acao = "Novo";
		}
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

function getNivelEnsino()
{
	var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
	var campoNivelEnsino = document.getElementById('ref_cod_nivel_ensino');

	campoNivelEnsino.length = 1;
	for (var j = 0; j < nivel_ensino.length; j++)
	{
		if (nivel_ensino[j][2] == campoInstituicao)
		{
			campoNivelEnsino.options[campoNivelEnsino.options.length] = new Option( nivel_ensino[j][1], nivel_ensino[j][0],false,false);
		}
	}
}

function getTipoEnsino()
{
	var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
	var campoTipoEnsino = document.getElementById('ref_cod_tipo_ensino');

	campoTipoEnsino.length = 1;
	for (var j = 0; j < tipo_ensino.length; j++)
	{
		if (tipo_ensino[j][2] == campoInstituicao)
		{
			campoTipoEnsino.options[campoTipoEnsino.options.length] = new Option( tipo_ensino[j][1], tipo_ensino[j][0],false,false);
		}
	}
}

document.getElementById('ref_cod_instituicao').onchange = function()
{
	getNivelEnsino();
	getTipoEnsino();
}

</script>