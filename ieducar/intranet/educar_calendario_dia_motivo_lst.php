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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Calend&aacute;rio Dia Motivo" );
		$this->processoAp = "576";
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

	var $cod_calendario_dia_motivo;
	var $ref_cod_escola;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $sigla;
	var $descricao;
	var $tipo;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $nm_motivo;

	var $ref_cod_instituicao;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Calend&aacute;rio Dia Motivo - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$lista_busca = array(
			"Motivo",
			"Tipo"
		);

		$obj_permissao = new clsPermissoes();
		$nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1) {
			$lista_busca[] = "Escola";
			$lista_busca[] = "Institui&ccedil;&atilde;o";
		}else if ($nivel_usuario == 2) {
			$lista_busca[] = "Escola";
		}
		$this->addCabecalhos($lista_busca);

		// Filtros de Foreign Keys
		$get_escola = true;
		include("include/pmieducar/educar_campo_lista.php");

		// outros Filtros
		$this->campoTexto( "nm_motivo", "Motivo", $this->tipo, 30, 255, false );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_calendario_dia_motivo = new clsPmieducarCalendarioDiaMotivo();
		$obj_calendario_dia_motivo->setOrderby( "nm_motivo ASC" );
		$obj_calendario_dia_motivo->setLimite( $this->limite, $this->offset );

		$obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
		$obj_usuario_det = $obj_usuario->detalhe();
		$this->ref_cod_escola = $obj_usuario_det["ref_cod_escola"];
		$this->ref_cod_instituicao = $obj_usuario_det["ref_cod_instituicao"];

		$lista = $obj_calendario_dia_motivo->lista(
			null,
			$this->ref_cod_escola,
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
			$this->nm_motivo,
			$this->ref_cod_instituicao
		);

		$total = $obj_calendario_dia_motivo->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{

				// pega detalhes de foreign_keys
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
				if( class_exists( "clsPmieducarEscola" ) )
				{
					$obj_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
					$obj_cod_escola_det = $obj_cod_escola->detalhe();
					$registro["ref_cod_escola"] = $obj_cod_escola_det["nome"];
				}
				else
				{
					$registro["ref_cod_escola"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
				}

				if ($registro["tipo"] == 'e')
				{
					$registro["tipo"] = 'extra';
				}
				else if ($registro["tipo"] == 'n')
				{
					$registro["tipo"] = 'n&atilde;o-letivo';
				}
				$lista_busca = array(
					"<a href=\"educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo={$registro["cod_calendario_dia_motivo"]}\">{$registro["nm_motivo"]}</a>",
					"<a href=\"educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo={$registro["cod_calendario_dia_motivo"]}\">{$registro["tipo"]}</a>"
				);

				if ($nivel_usuario == 1)
				{
					$lista_busca[] = "<a href=\"educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo={$registro["cod_calendario_dia_motivo"]}\">{$registro["ref_cod_escola"]}</a>";
					$lista_busca[] = "<a href=\"educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo={$registro["cod_calendario_dia_motivo"]}\">{$registro["ref_cod_instituicao"]}</a>";
				}
				else if ($nivel_usuario == 2)
				{
					$lista_busca[] = "<a href=\"educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo={$registro["cod_calendario_dia_motivo"]}\">{$registro["ref_cod_escola"]}</a>";
				}
				$this->addLinhas($lista_busca);
			}
		}
		$this->addPaginador2( "educar_calendario_dia_motivo_lst.php", $total, $_GET, $this->nome, $this->limite );

		if( $obj_permissao->permissao_cadastra( 576, $this->pessoa_logada,7 ) )
		{
			$this->acao = "go(\"educar_calendario_dia_motivo_cad.php\")";
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