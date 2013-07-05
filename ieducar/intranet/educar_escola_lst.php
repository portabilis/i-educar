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
require_once ("include/localizacaoSistema.php");

class clsIndexBase extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Escola" );
		$this->processoAp = "561";
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

	var $cod_escola;
	var $ref_usuario_cad;
	var $ref_usuario_exc;
	var $ref_cod_instituicao;
	var $ref_cod_escola_localizacao;
	var $ref_cod_escola_rede_ensino;
	var $ref_idpes;
	var $sigla;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $nm_escola;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Escola - Listagem";

		$obj_permissoes = new clsPermissoes();

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$cabecalhos = array("Escola");
		$nivel = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		if( $nivel == 1 )
		{
			$cabecalhos[] = "Institui&ccedil;&atilde;o";
			$objInstituicao = new clsPmieducarInstituicao();
			$opcoes = array( "" => "Selecione" );
			$objInstituicao->setOrderby( "nm_instituicao ASC" );
			$lista = $objInstituicao->lista();
			if( is_array( $lista ) )
			{
				foreach ( $lista AS $linha )
				{
					$opcoes[$linha["cod_instituicao"]] = $linha["nm_instituicao"];
				}
			}
			$this->campoLista( "ref_cod_instituicao", "Institui&ccedil;&atilde;o", $opcoes, $this->ref_cod_instituicao, false, false, false, false, false, false );
		}
		else
		{
			$this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada );
			if( $this->ref_cod_instituicao )
			{
				$this->campoOculto( "ref_cod_instituicao", $this->ref_cod_instituicao );
			}
			else
			{
				die( "Erro: Usuário não é do nivel poli-institucional e não possui uma instituição" );
			}
		}
		$this->addCabecalhos( $cabecalhos );

		$this->campoTexto( "nm_escola", "Escola", $this->nm_escola, 30, 255, false );

		// Filtros de Foreign Keys
		$this->limite = 10;
		$obj_escola = new clsPmieducarEscola();
		//$obj_escola->setOrderby( "nome ASC" );
		$obj_escola->setLimite( $this->limite, ( $this->pagina_formulario - 1 ) * $this->limite );

		$cod_escola = $obj_permissoes->getEscola($this->pessoa_logada);

		$lista = $obj_escola->lista(
			$cod_escola,
			null,
			null,
			$this->ref_cod_instituicao,
			null,
			null,
			null,
			null,
			null,
			null,
			1,
			$this->nm_escola
		);

		$total = $obj_escola->_total;
		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{

				$linha = array();

				$linha[] = "<a href=\"educar_escola_det.php?cod_escola={$registro["cod_escola"]}\">{$registro["nome"]}</a>";
				if( $nivel == 1 )
				{
					$objInstituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
					$detInstituicao = $objInstituicao->detalhe();

					$linha[] = "<a href=\"educar_escola_det.php?cod_escola={$registro["cod_escola"]}\">{$detInstituicao["nm_instituicao"]}</a>";
				}
				$this->addLinhas( $linha );
			}
		}
		$this->addPaginador2( "educar_escola_lst.php", $total, $_GET, $this->nome, $this->limite );
		if( $obj_permissoes->permissao_cadastra( 561, $this->pessoa_logada, 3 ) )
		{
			$this->acao = "go(\"educar_escola_cad.php\")";
			$this->nome_acao = "Novo";
		}
		$this->largura = "100%";
                
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    "educar_index.php"                  => "Escola",
                    ""                                  => "Lista de Escola"
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
