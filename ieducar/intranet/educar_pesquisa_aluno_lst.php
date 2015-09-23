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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Pesquisa Aluno" );
		$this->processoAp = "578";
		$this->renderBanner = false;
		$this->renderMenu = false;
		$this->renderMenuSuspenso = false;
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

	//var $cod_aluno;
	//var $ref_idpes_responsavel;
	/*var $ref_cod_pessoa_educ;
	var $ref_cod_aluno_beneficio;
	var $ref_cod_religiao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_idpes;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	*/

	var $nome_aluno;
	var $cpf_aluno;
	var $nome_responsavel;
	var $cpf_responsavel;
	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		$_SESSION["campo1"] = $_GET["campo1"] ? $_GET["campo1"] : $_SESSION["campo1"];
		//$_SESSION["campo2"] = $_GET["campo2"] ? $_GET["campo2"] : $_SESSION["campo2"];
		$_SESSION["campo3"] = $_GET["campo3"] ? $_GET["campo3"] : $_SESSION["campo3"];
		$_SESSION["campo4"] = $_GET["campo4"] ? $_GET["campo4"] : $_SESSION["campo4"];
		session_write_close();

		$this->titulo = "Aluno - Listagem";

		 // passa todos os valores obtidos no GET para atributos do objeto
		foreach( $_GET AS $var => $val ){
			$this->$var = ( $val === "" ) ? null: $val;

		}

		$this->addCabecalhos( array(
			"Nome",
			"CPF",
			"Nome Respons&aacute;vel",
			"CPF Respons&aacute;vel"
		) );


		$this->campoTexto("nome_aluno","Nome do Aluno",$this->nome_aluno,20,255,false);
		$this->campoCpf("cpf_aluno","CPF do Aluno",$this->cpf_aluno,false);
		$this->campoTexto("nome_responsavel","Nome do Respons&aacute;vel",$this->nome_responsavel,20,false);
		$this->campoCpf("cpf_responsavel","CPF do Respons&aacute;vel",$this->cpf_responsavel,false);

		// Paginador
		$this->limite = 10;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;


		$obj_aluno = new clsPmieducarAlunoCMF();
		$obj_aluno->setLimite( $this->limite, $this->offset );
		$lista_aluno = $obj_aluno->lista($this->nome_aluno,idFederal2int($this->cpf_aluno),$this->nome_responsavel,idFederal2int($this->cpf_responsavel));
		$total = $obj_aluno->_total;

		if($lista_aluno)
		{
			foreach ($lista_aluno as $registro)
			{
				if($registro["cpf_aluno"])
					$registro["cpf_aluno_"] = int2CPF($registro["cpf_aluno"]);

				if($registro["cpf_responsavel"])
					$registro["cpf_responsavel_"] = int2CPF($registro["cpf_responsavel"]);
			//addSel1('{$_SESSION['campo3']}','{$registro['nome_aluno']}','{$registro["nome_aluno"]}');
					//if($registro["tipo"] == 1)
						$script = " onclick=\"addVal1('{$_SESSION['campo3']}','{$registro['cpf_aluno']}'); addVal1('{$_SESSION['campo1']}','{$registro['cod_aluno']}');  addVal1('{$_SESSION['campo4']}','{$registro['cpf_aluno_']}'); fecha();\"";
					//elseif($registro["tipo"] == 2)
						//$script = " onclick=\"addVal1('{$_SESSION['campo3']}','{$registro['cpf_aluno']}');  addVal1('{$_SESSION['campo2']}','{$registro['cod_aluno']}'); addVal1('{$_SESSION['campo4']}','{$registro['cpf_aluno_']}');  fecha();\"";
				$obj_det = "";
				$obj_cpf_det = "";
				if($registro["idpes_responsavel"])
				{
					$obj_resp = new clsPessoa_($registro["idpes_responsavel"]);
					$obj_det = $obj_resp->detalhe();

					$obj_cpf = new clsFisica($registro["idpes_responsavel"]);
					$obj_cpf_det = $obj_cpf->detalhe();
					if($obj_cpf_det["cpf"])
						$obj_cpf_det["cpf"] = int2IdFederal($obj_cpf_det["cpf"]);
				}
				$this->addLinhas( array(
					"<a href=\"javascript:void( 0 );\" $script>{$registro["nome_aluno"]}</a>",
					"<a href=\"javascript:void( 0 );\" $script>{$registro["cpf_aluno_"]}</a>",
					"<a href=\"javascript:void( 0 );\" $script>{$obj_det["nome"]}</a>",
					"<a href=\"javascript:void( 0 );\" $script>{$obj_cpf_det["cpf"]}</a>"
				) );
			}

		}

		$this->addPaginador2( "educar_pesquisa_aluno_lst.php", $total, $_GET, $this->nome, $this->limite );

		//verifica se foi realizado pesquisa
		if(isset($_GET["nome_aluno"]) || isset($_GET["nome_responsavel"]) || isset($_GET["cpf_aluno"]) || isset($_GET["cpf_responsavel"]) )
			$ok = true;

		//** Verificacao de permissao para cadastro
		$obj_permissao = new clsPermissoes();

		if($obj_permissao->permissao_cadastra(578, $this->pessoa_logada,7) && $ok)
		{
			$this->acao = "window.parent.document.getElementById(\"cpf_\").disabled = true; window.parent.document.getElementById(\"ref_idpes\").value = \"\"; window.parent.document.getElementById(\"cpf_\").value = \"\";fecha();";
			//$this->acao = "window.parent.document.getElementById(\"cpf_\").disabled = false; window.parent.fechaExpansivel(\"div_dinamico_\"+(parent.DOM_divs.length*1-1));";
			$this->nome_acao = "Novo";
		}
		//**
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
function addSel1( campo, valor, texto )
{
	obj = window.parent.document.getElementById( campo );
	novoIndice = obj.options.length;
	obj.options[novoIndice] = new Option( texto );
	opcao = obj.options[novoIndice];
	opcao.value = valor;
	opcao.selected = true;
	setTimeout( "obj.onchange", 100 );
}

function addVal1( campo,valor )
{

	obj =  window.parent.document.getElementById( campo );
	obj.value = valor;
}

function fecha()
{
	window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));
	window.parent.document.forms[0].submit();
}
</script>