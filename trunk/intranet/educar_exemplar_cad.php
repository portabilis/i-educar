<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

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
require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/View/Helper/DynamicSelectMenusHelper.php';

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Exemplar" );
		$this->processoAp = "606";
	}
}

class indice extends clsCadastro
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	var $cod_exemplar;
	var $ref_cod_fonte;
	var $ref_cod_motivo_baixa;
	var $ref_cod_acervo;
	var $ref_cod_situacao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $permite_emprestimo;
	var $preco;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $data_aquisicao;

	var $ref_cod_instituicao;
	var $ref_cod_escola;
	var $ref_cod_biblioteca;

	var $tombo_automarico;
	var $combo_manual;
	var $qtd_livros;
	var $eh_manual;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_exemplar=$_GET["cod_exemplar"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 606, $this->pessoa_logada, 11,  "educar_exemplar_lst.php" );

		if( is_numeric( $this->cod_exemplar ) )
		{

			$obj = new clsPmieducarExemplar( $this->cod_exemplar );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$obj_obra = new clsPmieducarAcervo($this->ref_cod_acervo);
				$det_obra = $obj_obra->detalhe();

				$obj_biblioteca = new clsPmieducarBiblioteca($det_obra["ref_cod_biblioteca"]);
				$obj_det = $obj_biblioteca->detalhe();

				$this->ref_cod_instituicao = $obj_det["ref_cod_instituicao"];
				$this->ref_cod_escola = $obj_det["ref_cod_escola"];
				$this->ref_cod_biblioteca = $obj_det["cod_biblioteca"];


				$this->data_aquisicao = dataFromPgToBr( $this->data_aquisicao );

				if( $obj_permissoes->permissao_excluir( 606, $this->pessoa_logada, 11 ) )
				{
					$this->fexcluir = true;
				}

				$retorno = "Editar";
			}
		}

		$this->url_cancelar = ($retorno == "Editar") ? "educar_exemplar_det.php?cod_exemplar={$registro["cod_exemplar"]}" : "educar_exemplar_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "cod_exemplar", $this->cod_exemplar );

    // Dynamic Select Menus
    $dynamicSelectMenusHelper = new DynamicSelectMenusHelper($this);

    $dynamicSelectMenusHelper->instituicao(array('value' => $this->ref_cod_instituicao));

    $dynamicSelectMenusHelper->escola(array('value' => $this->ref_cod_escola));

    $dynamicSelectMenusHelper->biblioteca(array('value' => $this->ref_cod_biblioteca));

    $dynamicSelectMenusHelper->bibliotecaSituacao($this->ref_cod_biblioteca, array('value' => $this->ref_cod_situacao));

    $dynamicSelectMenusHelper->bibliotecaFonte($this->ref_cod_biblioteca, array('value' => $this->ref_cod_fonte));

		$opcoes = array();
		if( $this->ref_cod_acervo && $this->ref_cod_acervo != "NULL")
		{
			$objTemp = new clsPmieducarAcervo($this->ref_cod_acervo);
			$detalhe = $objTemp->detalhe();
			if ( $detalhe )
			{
				$opcoes["{$detalhe['cod_acervo']}"] = "{$detalhe['titulo']}";
			}
		}else{
			$opcoes = array( "" => "Selecione" );
		}

		$this->campoLista("ref_cod_acervo","Obra",$opcoes,$this->ref_cod_acervo,"",false,"","<img border=\"0\" onclick=\"pesquisa();\" id=\"ref_cod_acervo_lupa\" name=\"ref_cod_acervo_lupa\" src=\"imagens/lupa.png\"\/>",false,true);

		$opcoes = array( "" => "Selecione", "2" => "Sim", "1" => "N&atilde;o" );
		$this->campoLista( "permite_emprestimo", "Permite Emprestimo", $opcoes, $this->permite_emprestimo );

		$this->preco = is_numeric($this->preco) ? number_format($this->preco, 2, ",", ".") : "";
		$this->campoMonetario( "preco", "Preco", $this->preco, 10, 20, true );

		// data
		if(!$this->data_aquisicao)
			$this->data_aquisicao = date("d/m/Y");

		$this->campoData( "data_aquisicao", "Data Aquisic&atilde;o", $this->data_aquisicao, true );

		if (!is_numeric($this->cod_exemplar))
		{
			$this->campoNumero("qtd_livros", "Quantidade de Livros", 1, 5, 5, true);
      // $this->campoBoolLista("tombo_automarico", "Tombo Automático", "t");
			$this->campoNumero("combo_manual", "Combo", "", 5, 5, false);
			$this->campoOculto("eh_manual", 0);
		}
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 606, $this->pessoa_logada, 11,  "educar_exemplar_lst.php" );

		$this->preco = str_replace(".","",$this->preco);
		$this->preco = str_replace(",",".",$this->preco);

    $this->data_aquisicao = dataToBanco($this->data_aquisicao);

		if (!$this->combo_manual)
		{
			$obj_exemplar = new clsPmieducarExemplar();
			$max_tombo = $obj_exemplar->retorna_tombo_maximo() + 1;
		}
		else
		{
			$max_tombo = $this->combo_manual;
		}

		for ($i = 0; $i < $this->qtd_livros; $i++)
		{
			$obj = new clsPmieducarExemplar( $this->cod_exemplar, $this->ref_cod_fonte, $this->ref_cod_motivo_baixa, $this->ref_cod_acervo, $this->ref_cod_situacao, $this->pessoa_logada, $this->pessoa_logada, $this->permite_emprestimo, $this->preco, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->data_aquisicao, $max_tombo );
			$cadastrou = $obj->cadastra();
			if (!$cadastrou)
			{
				$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
				echo "<!--\nErro ao cadastrar clsPmieducarExemplar\nvalores obrigatorios\nis_numeric( $this->ref_cod_fonte ) && is_numeric( $this->ref_cod_acervo ) && is_numeric( $this->ref_cod_situacao ) && is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->permite_emprestimo ) && is_numeric( $this->preco )\n-->";
				return false;
			}
			$max_tombo++;
		}
		$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_exemplar_lst.php" );
			die();
			return true;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();


		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 606, $this->pessoa_logada, 11,  "educar_exemplar_lst.php" );

		$this->preco = str_replace(".","",$this->preco);
		$this->preco = str_replace(",",".",$this->preco);

    $this->data_aquisicao = dataToBanco($this->data_aquisicao);

		$obj = new clsPmieducarExemplar($this->cod_exemplar, $this->ref_cod_fonte, $this->ref_cod_motivo_baixa, $this->ref_cod_acervo, $this->ref_cod_situacao, $this->pessoa_logada, $this->pessoa_logada, $this->permite_emprestimo, $this->preco, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->data_aquisicao);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_exemplar_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarExemplar\nvalores obrigatorios\nif( is_numeric( $this->cod_exemplar ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 606, $this->pessoa_logada, 11,  "educar_exemplar_lst.php" );


		$obj = new clsPmieducarExemplar($this->cod_exemplar, $this->ref_cod_fonte, $this->ref_cod_motivo_baixa, $this->ref_cod_acervo, $this->ref_cod_situacao, $this->pessoa_logada, $this->pessoa_logada, $this->permite_emprestimo, $this->preco, $this->data_cadastro, $this->data_exclusao, 0, $this->data_aquisicao);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_exemplar_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarExemplar\nvalores obrigatorios\nif( is_numeric( $this->cod_exemplar ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
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

function pesquisa()
{
	var biblioteca = document.getElementById('ref_cod_biblioteca').value;
	if(!biblioteca)
	{
		alert('Por favor,\nselecione uma biblioteca!');
		return;
	}
	pesquisa_valores_popless('educar_pesquisa_acervo_lst.php?campo1=ref_cod_acervo&ref_cod_biblioteca=' + biblioteca , 'ref_cod_acervo');
}

//pesquisa_valores_popless('educar_pesquisa_acervo_lst.php?campo1=ref_cod_acervo', 'ref_cod_acervo')
</script>
