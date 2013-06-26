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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Biblioteca" );
		$this->processoAp = "591";
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

	var $cod_biblioteca;
	var $ref_cod_instituicao;
	var $ref_cod_escola;
	var $nm_biblioteca;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Biblioteca - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$lista_busca = array(
			"Biblioteca",
			"Escola"
		);

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
			$lista_busca[] = "Institui&ccedil;&atilde;o";

		// Filtros de Foreign Keys
		$get_escola = true;
		include("include/pmieducar/educar_campo_lista.php");

		$this->addCabecalhos($lista_busca);

		// outros Filtros
		$this->campoTexto( "nm_biblioteca", "Biblioteca", $this->nm_biblioteca, 30, 255, false );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_biblioteca = new clsPmieducarBiblioteca();
		$obj_biblioteca->setOrderby( "nm_biblioteca ASC" );
		$obj_biblioteca->setLimite( $this->limite, $this->offset );

		$lista = $obj_biblioteca->lista(
			null,
			$this->ref_cod_instituicao,
			$this->ref_cod_escola,
			$this->nm_biblioteca,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			1
		);

		$total = $obj_biblioteca->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				// pega detalhes de foreign_keys
				if( class_exists( "clsPmieducarInstituicao" ) )
				{
					$obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
					$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
					$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
				}
				else
				{
					$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
				}

				if( class_exists( "clsPmieducarEscola" ) )
				{
					$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
					$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
					$idpes = $det_ref_cod_escola["ref_idpes"];
					if ($idpes)
					{
						$obj_escola = new clsPessoaJuridica( $idpes );
						$obj_escola_det = $obj_escola->detalhe();
						$registro["ref_cod_escola"] = $obj_escola_det["fantasia"];
					}
					else
					{
						$obj_escola = new clsPmieducarEscolaComplemento( $registro["ref_cod_escola"] );
						$obj_escola_det = $obj_escola->detalhe();
						$registro["ref_cod_escola"] = $obj_escola_det["nm_escola"];
					}
				}
				else
				{
					$registro["ref_cod_escola"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
				}

				$lista_busca = array(
					"<a href=\"educar_biblioteca_det.php?cod_biblioteca={$registro["cod_biblioteca"]}\">{$registro["nm_biblioteca"]}</a>",
					"<a href=\"educar_biblioteca_det.php?cod_biblioteca={$registro["cod_biblioteca"]}\">{$registro["ref_cod_escola"]}</a>"
				);

				if ($nivel_usuario == 1)
					$lista_busca[] = "<a href=\"educar_biblioteca_det.php?cod_biblioteca={$registro["cod_biblioteca"]}\">{$registro["ref_cod_instituicao"]}</a>";
				$this->addLinhas($lista_busca);
			}
		}
		
		$this->addPaginador2( "educar_biblioteca_lst.php", $total, $_GET, $this->nome, $this->limite );
		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 591, $this->pessoa_logada, 3 ) )
		{
			$this->acao = "go(\"educar_biblioteca_cad.php\")";
			$this->nome_acao = "Novo";
		}

		$this->largura = "100%";
                
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    "educar_biblioteca_index.php"       => "Biblioteca",
                    ""                                  => "Lista de Biblioteca"
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