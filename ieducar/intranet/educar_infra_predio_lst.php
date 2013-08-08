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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Infra Predio" );
		$this->processoAp = "567";
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

	var $cod_infra_predio;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_escola;
	var $nm_predio;
	var $desc_predio;
	var $endereco;
	var $data_cadastro;
	var $data_descricao;
	var $ativo;

	var $ref_cod_instituicao;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();
		//** 2 - Escola 1 - institucional 0 - poli-institucional
		$obj_permissao = new clsPermissoes();
		$nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);

		//busca instituicao e escola do usuario
		$obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
		$obj_usuario->setCamposLista("ref_cod_instituicao,ref_cod_escola");
		$det_obj_usuario = $obj_usuario->detalhe();

		$instituicao_usuario = $det_obj_usuario["ref_cod_instituicao"];

		$escola_usuario = $det_obj_usuario["ref_cod_escola"];

		$obj_infra_predio = new clsPmieducarInfraPredio();
		$obj_infra_predio->setOrderby( "nm_predio ASC" );
		$obj_infra_predio->setLimite( $this->limite, $this->offset );

		$this->ref_cod_escola = $_GET["ref_cod_escola"];
		$this->ref_cod_instituicao = $_GET["ref_cod_instituicao"];
		$this->nm_predio = $_GET["nm_predio"];

		/*filtro escola-instituicao*/
		$obrigatorio = false;
		include("include/pmieducar/educar_pesquisa_instituicao_escola.php");

		/*		if(isset($_GET["ref_cod_instituicao"]) &&  !empty($_GET["ref_cod_instituicao"]) && is_array($opcoes_instituicao) && array_key_exists($_GET["ref_cod_instituicao"],$opcoes_instituicao) )
				{
					$this->ref_cod_instituicao = $_GET["ref_cod_instituicao"];
				}
				else
				{
					$this->ref_cod_instituicao = null;
				}*/
		switch ($nivel_usuario) {
			case 4:
				//busca escola do usuario


				$this->addCabecalhos( array(
					"Escola",
					"Nome Predio",
				) );


				$this->ref_cod_escola = $escola_usuario;

				$lista = $obj_infra_predio->lista(
					$this->cod_infra_predio,
					null,
					null,
					$this->ref_cod_escola,
					$this->nm_predio,
					null,
					null,
					null,
					null,
					null,
					null,
					1,
					$escola_in
					,null
				);
				break;

			case 2:


				$this->addCabecalhos( array(
					"Institui&ccedil;&atilde;o",
					"Escola",
					"Nome Predio",
				) );

				$this->ref_cod_escola = $_GET["ref_cod_escola"];
				$obj_escola = new clsPmieducarEscola($this->ref_cod_escola,null,null,$instituicao_usuario,null,null,null,null,null,null,1);
				$obj_escola->setCamposLista("cod_escola,nm_escola");

				if(!$obj_escola->detalhe())
					$this->ref_cod_escola = null;

			//	$obj_infra_predio->setOrderby("escola.nm_escola");
				$lista = $obj_infra_predio->lista(
					$this->cod_infra_predio,
					null,
					null,
					$this->ref_cod_escola,
					$this->nm_predio,
					null,
					null,
					null,
					null,
					null,
					null,
					1,
					$escola_in,
					$instituicao_usuario
				);
				break;

			case 1:
				//poli-institucional

				$this->addCabecalhos( array(
					"Institui&ccedil;&atilde;o",
					"Escola",
					"Nome Predio",
				) );



				$obj_escola = new clsPmieducarEscola($this->ref_cod_escola,null,null,ref_cod_instituicao,null,null,null,null,null,null,1);
				$obj_escola->setCamposLista("cod_escola,nm_escola");

				if(!$obj_escola->detalhe() && !empty($this->ref_cod_escola) && !empty($this->ref_cod_instituicao))
					$this->ref_cod_instituicao = $this->ref_cod_escola = null;

				$lista = $obj_infra_predio->lista(
					$this->cod_infra_predio,
					null,
					null,
					$this->ref_cod_escola,
					$this->nm_predio,
					null,
					null,
					null,
					null,
					null,
					null,
					1,
					$escola_in,
					$this->ref_cod_instituicao
				);
				break;
			default:
				break;
		}
		$this->titulo = "Infra Predio - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );





		// outros Filtros
		$this->campoTexto( "nm_predio", "Nome Pr&eacute;dio", $this->nm_predio, 30, 255, false );



		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;



		$total = $obj_infra_predio->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{

				if( class_exists( "clsPmieducarInstituicao" )  && class_exists( "clsPmieducarEscola" ) )
				{
					$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
					$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
					$registro["ref_cod_instituicao"] = $det_ref_cod_escola["ref_cod_instituicao"];

					$obj_ref_cod_intituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
					$det_ref_cod_intituicao = $obj_ref_cod_intituicao->detalhe();
					$registro["ref_cod_instituicao"] = $det_ref_cod_intituicao["nm_instituicao"];
				}
				else
				{
					$registro["ref_cod_instituicao"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarIntituicao\n-->";
				}

				// pega detalhes de foreign_keys
				if( class_exists( "clsPmieducarEscola" ) )
				{
					$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
					$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
					$registro["ref_cod_escola"] = $det_ref_cod_escola["nome"];
				}
				else
				{
					$registro["ref_cod_escola"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarEscola\n-->";
				}



				switch ($nivel_usuario) {
					case 4:
						$this->addLinhas( array(
							"<a href=\"educar_infra_predio_det.php?cod_infra_predio={$registro["cod_infra_predio"]}\">{$registro["ref_cod_escola"]}</a>",
							"<a href=\"educar_infra_predio_det.php?cod_infra_predio={$registro["cod_infra_predio"]}\">{$registro["nm_predio"]}</a>"
							) );
						break;
					case 2:
					$this->addLinhas( array(
						"<a href=\"educar_infra_predio_det.php?cod_infra_predio={$registro["cod_infra_predio"]}\">{$registro["ref_cod_instituicao"]}</a>",
						"<a href=\"educar_infra_predio_det.php?cod_infra_predio={$registro["cod_infra_predio"]}\">{$registro["ref_cod_escola"]}</a>",
						"<a href=\"educar_infra_predio_det.php?cod_infra_predio={$registro["cod_infra_predio"]}\">{$registro["nm_predio"]}</a>"
						) );
						break;
					case 1:
						$this->addLinhas( array(
						"<a href=\"educar_infra_predio_det.php?cod_infra_predio={$registro["cod_infra_predio"]}\">{$registro["ref_cod_instituicao"]}</a>",
						"<a href=\"educar_infra_predio_det.php?cod_infra_predio={$registro["cod_infra_predio"]}\">{$registro["ref_cod_escola"]}</a>",
						"<a href=\"educar_infra_predio_det.php?cod_infra_predio={$registro["cod_infra_predio"]}\">{$registro["nm_predio"]}</a>"
						) );
						break;
					default:
						break;
				}

			}
		}
		$this->addPaginador2( "educar_infra_predio_lst.php", $total, $_GET, $this->nome, $this->limite );


		//** Verificacao de permissao para cadastro

		if($obj_permissao->permissao_cadastra(567, $this->pessoa_logada,7))
		{
			$this->acao = "go(\"educar_infra_predio_cad.php\")";
			$this->nome_acao = "Novo";
		}
		//**
		$this->largura = "100%";
                
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    "educar_index.php"                  => "Escola",
                    ""                                  => "Lista de Prédios"
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