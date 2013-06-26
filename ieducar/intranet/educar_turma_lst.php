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
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once ("include/localizacaoSistema.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Turma" );
		$this->processoAp = "586";
                $this->addEstilo( "localizacaoSistema" );
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
	var $visivel;

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
			"Ano",
			"Turma",
      "Turno",
			"S&eacute;rie",
			"Curso"
		);


		$obj_permissao = new clsPermissoes();
		$nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
		{
			$lista_busca[] = "Escola";
			//$lista_busca[] = "Institui&ccedil;&atilde;o";
		}
		else if ($nivel_usuario == 2)
		{
			$lista_busca[] = "Escola";
		}
		$lista_busca[] = "Situação";
		$this->addCabecalhos($lista_busca);

		$get_escola = true;
//		$get_escola_curso = true;
		$get_escola_curso_serie = true;
		$sem_padrao = true;
		$get_curso = true;
		include("include/pmieducar/educar_campo_lista.php");

		if ( $this->ref_cod_escola )
		{
			$this->ref_ref_cod_escola = $this->ref_cod_escola;
		}

    $helperOptions = array();
    $this->inputsHelper()->dynamic('anoLetivo', array(), $helperOptions);

		$this->campoTexto( "nm_turma", "Turma", $this->nm_turma, 30, 255, false );
		$this->campoLista("visivel", "Situação", array("" => "Selecione", "1" => "Ativo", "2" => "Inativo"), $this->visivel);
		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_turma = new clsPmieducarTurma();
		$obj_turma->setOrderby( "nm_turma ASC" );
		$obj_turma->setLimite( $this->limite, $this->offset );

		if ($this->visivel == 1) {
			$visivel = true;
		} elseif ($this->visivel == 2) {
			$visivel = false;
		} else {
			$visivel = array("true", "false");
		}

		$lista = $obj_turma->lista2(
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
			null, null, null, null, null, $visivel, null, null, $this->ano
		);

		$total = $obj_turma->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			$ref_cod_escola = "";
			$nm_escola = "";
			foreach ( $lista AS $registro )
			{
				// pega detalhes de foreign_keys
			/*	if( class_exists( "clsPmieducarEscolaSerie" ) )
				{
					$obj_ref_ref_cod_serie = new clsPmieducarEscolaSerie( $registro["ref_ref_cod_escola"], $registro["ref_ref_cod_serie"] );
					$det_ref_ref_cod_serie = $obj_ref_ref_cod_serie->detalhe();
					$registro["ref_ref_cod_serie"] = $det_ref_ref_cod_serie["ref_cod_serie"];
				}
				else
				{
					$registro["ref_ref_cod_serie"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarEscolaSerie\n-->";
				}*/
				/*if( class_exists( "clsPmieducarCurso" ) )
				{
					$obj_ref_cod_curso = new clsPmieducarCurso( $registro["ref_cod_curso"] );
					$det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
					$registro["ref_cod_curso"] = $det_ref_cod_curso["nm_curso"];
				}
				else
				{
					$registro["ref_cod_curso"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarCurso\n-->";
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
				}*/
				if( class_exists( "clsPmieducarEscola" ) && $registro["ref_ref_cod_escola"] != $ref_cod_escola)
				{
					$ref_cod_escola = $registro["ref_ref_cod_escola"];
					$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_ref_cod_escola"] );
					$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
					$ref_cod_escola = $registro["ref_ref_cod_escola"] ;
					$nm_escola = $det_ref_cod_escola["nome"];
				}


				/*$obj_ser = new clsPmieducarSerie( $registro["ref_ref_cod_serie"], null, null, $this->ref_cod_curso );
				$det_ser = $obj_ser->detalhe();
				$obj_cur = new clsPmieducarCurso( $det_ser["ref_cod_curso"] );
				$det_cur = $obj_cur->detalhe();*/


				$lista_busca = array(
					"<a href=\"educar_turma_det.php?cod_turma={$registro["cod_turma"]}\">{$registro["ano"]}</a>",
					"<a href=\"educar_turma_det.php?cod_turma={$registro["cod_turma"]}\">{$registro["nm_turma"]}</a>"
				);

        if ($registro["turma_turno_id"]) {
        	$options = array('params' => $registro["turma_turno_id"], 'return_only' => 'first-field');
				  $turno   = Portabilis_Utils_Database::fetchPreparedQuery("select nome from pmieducar.turma_turno where id = $1", $options);

				  $lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro["cod_turma"]}\">$turno</a>";
        }
        else
				  $lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro["cod_turma"]}\"></a>";

				if ($registro["nm_serie"])
					$lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro["cod_turma"]}\">{$registro["nm_serie"]}</a>";
				else
					$lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro["cod_turma"]}\">-</a>";

				$lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro["cod_turma"]}\">{$registro["nm_curso"]}</a>";

				if ($nivel_usuario == 1)
				{
					if ($nm_escola)
						$lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro["cod_turma"]}\">{$nm_escola}</a>";
					else
						$lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro["cod_turma"]}\">-</a>";

					//$lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro["cod_turma"]}\">{$registro["nm_instituicao"]}</a>";
				}
				else if ($nivel_usuario == 2)
				{
					if ($nm_escola)
						$lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro["cod_turma"]}\">{$nm_escola}</a>";
					else
						$lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro["cod_turma"]}\">-</a>";
				}
				if (dbBool($registro["visivel"]))
				{
					$lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro["cod_turma"]}\">Ativo</a>";
				}
				else
				{
					$lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro["cod_turma"]}\">Inativo</a>";
				}
				$this->addLinhas($lista_busca);
			}
		}

		$this->addPaginador2( "educar_turma_lst.php", $total, $_GET, $this->nome, $this->limite );
		$obj_permissoes = new clsPermissoes();
		if ( $obj_permissoes->permissao_cadastra( 586, $this->pessoa_logada, 7 ) )
		{
			$this->acao = "go(\"educar_turma_cad.php\")";
			$this->nome_acao = "Novo";
		}
		$this->largura = "100%";
                
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    "educar_index.php"                  => "Escola",
                    ""                                  => "Lista de Turmas"
                ));
                $this->enviaLocalizacao($localizacao->montar());
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
