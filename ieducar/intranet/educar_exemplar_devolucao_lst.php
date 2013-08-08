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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Exemplar Devolu&ccedil;&atilde;o" );
		$this->processoAp = "628";
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

	var $cod_emprestimo;
	var $ref_usuario_devolucao;
	var $ref_usuario_cad;
	var $ref_cod_cliente;
	var $ref_cod_exemplar;
	var $data_retirada;
	var $data_devolucao;
	var $valor_multa;

	var $nm_cliente;
	var $nm_obra;
	var $ref_cod_biblioteca;
	var $ref_cod_acervo;
	var $ref_cod_instituicao;
	var $ref_cod_escola;
	var $cod_biblioteca;

	function Gerar()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Exemplar Devolu&ccedil;&atilde;o - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$lista_busca = array(
			"Cliente",
			"Código exemplar",
			"Tombo",
			"Exemplar",
			"Data Retirada"
		);

		// Filtros de Foreign Keys
		$get_escola = true;
		$get_biblioteca = true;
		$get_cabecalho = "lista_busca";
		include("include/pmieducar/educar_campo_lista.php");

		$this->addCabecalhos($lista_busca);

		// Filtros de Foreign Keys
		$this->campoTexto("nm_cliente", "Cliente", $this->nm_cliente, 30, 255, false, false, false, "", "<img border=\"0\" onclick=\"pesquisa_cliente();\" id=\"ref_cod_cliente_lupa\" name=\"ref_cod_cliente_lupa\" src=\"imagens/lupa.png\"\/>");
		$this->campoOculto("ref_cod_cliente", $this->ref_cod_cliente);

		$this->campoTexto("nm_obra","Obra", $this->nm_obra, 30, 255, false, false, false, "", "<img border=\"0\" onclick=\"pesquisa_obra();\" id=\"ref_cod_exemplar_lupa\" name=\"ref_cod_exemplar_lupa\" src=\"imagens/lupa.png\"\/>");
		$this->campoOculto("ref_cod_acervo", $this->ref_cod_acervo);

		$this->campoNumero("ref_cod_exemplar","Código exemplar", $this->ref_cod_exemplar, 15, 10);
		$this->campoNumero("tombo","Tombo", $this->tombo, 15, 10);

		if ($this->ref_cod_biblioteca)
		{
			$this->cod_biblioteca = $this->ref_cod_biblioteca;
			$this->campoOculto("cod_biblioteca", $this->cod_biblioteca);
		}
		else
		{
			$this->cod_biblioteca = null;
			$this->campoOculto("cod_biblioteca", $this->cod_biblioteca);
		}

		// outros Filtros
		$this->campoData( "data_retirada", "Data Retirada", $this->data_retirada, false );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_exemplar_emprestimo = new clsPmieducarExemplarEmprestimo();
		$obj_exemplar_emprestimo->setOrderby( "data_retirada ASC" );
		$obj_exemplar_emprestimo->setLimite( $this->limite, $this->offset );

		$lista = $obj_exemplar_emprestimo->lista(
			null,
			null,
			null,
			$this->ref_cod_cliente,
			$this->ref_cod_exemplar,
			$this->data_retirada,
			$this->data_retirada,
			null,
			null,
			null,
			false,
			$this->ref_cod_biblioteca,
			false,
			$this->ref_cod_instituicao,
			$this->ref_cod_escola,
			$this->nm_obra
		);

		$total = $obj_exemplar_emprestimo->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				// muda os campos data
				$registro["data_retirada_time"] = strtotime( substr( $registro["data_retirada"], 0, 16 ) );
				$registro["data_retirada_br"] = date( "d/m/Y", $registro["data_retirada_time"] );

				// pega detalhes de foreign_keys
				if( class_exists( "clsPmieducarExemplar" ) )
				{
					$obj_exemplar = new clsPmieducarExemplar($registro["ref_cod_exemplar"]);
					$det_exemplar = $obj_exemplar->detalhe();
					$acervo = $det_exemplar["ref_cod_acervo"];
					$obj_acervo = new clsPmieducarAcervo($acervo);
					$det_acervo = $obj_acervo->detalhe();
					$registro["titulo"] = $det_acervo["titulo"];
				}
				else
				{
					$registro["ref_cod_exemplar"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarExemplar\n-->";
				}

				if( class_exists( "clsPmieducarCliente" ) )
				{
					$obj_cliente = new clsPmieducarCliente($registro["ref_cod_cliente"]);
					$det_cliente = $obj_cliente->detalhe();
					$ref_idpes = $det_cliente["ref_idpes"];
					$obj_pessoa = new clsPessoa_($ref_idpes);
					$det_pessoa = $obj_pessoa->detalhe();
					$registro["ref_cod_cliente"] = $det_pessoa["nome"];
				}
				else
				{
					$registro["ref_cod_cliente"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarCliente\n-->";
				}

				if( class_exists( "clsPmieducarBiblioteca" ) )
				{
					$obj_ref_cod_biblioteca = new clsPmieducarBiblioteca( $registro["ref_cod_biblioteca"] );
					$det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
					$registro["ref_cod_biblioteca"] = $det_ref_cod_biblioteca["nm_biblioteca"];
				}
				if( $registro["ref_cod_instituicao"] )
				{
					$obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
					$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
					$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
				}
				if( $registro["ref_cod_escola"] )
				{
					$obj_ref_cod_escola = new clsPmieducarEscola();
					$det_ref_cod_escola = array_shift($obj_ref_cod_escola->lista($registro["ref_cod_escola"]));
					$registro["ref_cod_escola"] = $det_ref_cod_escola["nome"];
				}

				$lista_busca = array(
					"<a href=\"educar_exemplar_devolucao_det.php?cod_emprestimo={$registro["cod_emprestimo"]}\">{$registro["ref_cod_cliente"]}</a>",
					"<a href=\"educar_exemplar_devolucao_det.php?cod_emprestimo={$registro["cod_emprestimo"]}\">{$registro["ref_cod_exemplar"]}</a>",
					"<a href=\"educar_exemplar_devolucao_det.php?cod_emprestimo={$registro["cod_emprestimo"]}\">{$det_exemplar["tombo"]}</a>",
					"<a href=\"educar_exemplar_devolucao_det.php?cod_emprestimo={$registro["cod_emprestimo"]}\">{$registro["titulo"]}</a>",
					"<a href=\"educar_exemplar_devolucao_det.php?cod_emprestimo={$registro["cod_emprestimo"]}\">{$registro["data_retirada_br"]}</a>"
				);

				if ($qtd_bibliotecas > 1 && ($nivel_usuario == 4 || $nivel_usuario == 8))
					$lista_busca[] = "<a href=\"educar_exemplar_devolucao_det.php?cod_emprestimo={$registro["cod_emprestimo"]}\">{$registro["ref_cod_biblioteca"]}</a>";
				else if ($nivel_usuario == 1 || $nivel_usuario == 2 || $nivel_usuario == 4)
					$lista_busca[] = "<a href=\"educar_exemplar_devolucao_det.php?cod_emprestimo={$registro["cod_emprestimo"]}\">{$registro["ref_cod_biblioteca"]}</a>";
				if ($nivel_usuario == 1 || $nivel_usuario == 2)
					$lista_busca[] = "<a href=\"educar_exemplar_devolucao_det.php?cod_emprestimo={$registro["cod_emprestimo"]}\">{$registro["ref_cod_escola"]}</a>";
				if ($nivel_usuario == 1)
					$lista_busca[] = "<a href=\"educar_exemplar_devolucao_det.php?cod_emprestimo={$registro["cod_emprestimo"]}\">{$registro["ref_cod_instituicao"]}</a>";

				$this->addLinhas($lista_busca);
			}
		}
		$this->addPaginador2( "educar_exemplar_devolucao_lst.php", $total, $_GET, $this->nome, $this->limite );
		$this->largura = "100%";
                
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    "educar_biblioteca_index.php"                  => "Biblioteca",
                    ""                                  => "Lista de Devoluções"
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

function pesquisa_cliente()
{
	var campoBiblioteca = document.getElementById('cod_biblioteca').value;
	pesquisa_valores_popless('educar_pesquisa_cliente_lst.php?campo1=ref_cod_cliente&campo2=nm_cliente&ref_cod_biblioteca='+campoBiblioteca)
}

function pesquisa_obra()
{
	var campoBiblioteca = document.getElementById('cod_biblioteca').value;
	pesquisa_valores_popless('educar_pesquisa_obra_lst.php?campo1=ref_cod_acervo&campo2=nm_obra&campo3='+campoBiblioteca)
}

</script>
