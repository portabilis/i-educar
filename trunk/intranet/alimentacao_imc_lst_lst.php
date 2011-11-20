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
require_once( "include/alimentacao/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - IMC " );
		$this->processoAp = "10006";
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
	
	var $ref_aluno;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "IMC - Listagem";
		
		$this->ref_aluno = $_GET['ref_aluno'];

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$obj_permissao = new clsPermissoes();
		
		$lista_busca = array(
					"Escola",
					"Série",
					"Data Cadastro",
					"Altura",
					"Peso",
					"IMC"
		);

		$this->addCabecalhos($lista_busca);

		$obj_imc = new clsAlimentacaoIMC();
		
		$lista = $obj_imc->listaAluno($this->ref_aluno);
		if(is_array($lista))
		{
			$nm_aluno = $lista[0]["nome"];
		}
		$this->campoOculto( "ref_aluno", $this->ref_aluno );
		$this->campoRotulo("nm_aluno","Aluno","{$nm_aluno}");

		$filtro_aluno = null;
		if($this->ref_aluno > 0)
		{
			$filtro_aluno = $this->ref_aluno;
		}
		$lista = $obj_imc->lista(null,$filtro_aluno);
		
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_escola"] );
				$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
				$nm_escola = $det_ref_cod_escola["nome"];
				
				$obj_serie = new clsPmieducarSerie( $registro["ref_serie"] );
				$det_serie = $obj_serie->detalhe();
				$nm_serie = $det_serie["nm_serie"];
				
				$lista_busca = array();
				$lista_busca[] = "<a href=\"alimentacao_imc_det.php?idimc={$registro["idimc"]}&ref_aluno={$this->ref_aluno}\">{$nm_escola}</a>";
				$lista_busca[] = "<a href=\"alimentacao_imc_det.php?idimc={$registro["idimc"]}&ref_aluno={$this->ref_aluno}\">{$nm_serie}</a>";
				$lista_busca[] = "<a href=\"alimentacao_imc_det.php?idimc={$registro["idimc"]}&ref_aluno={$this->ref_aluno}\">".date('d/m/Y',strtotime($registro["dt_cadastro"]))."</a>";
				$lista_busca[] = "<a href=\"alimentacao_imc_det.php?idimc={$registro["idimc"]}&ref_aluno={$this->ref_aluno}\">{$registro["altura"]}</a>";
				$lista_busca[] = "<a href=\"alimentacao_imc_det.php?idimc={$registro["idimc"]}&ref_aluno={$this->ref_aluno}\">{$registro["peso"]}</a>";
				$lista_busca[] = "<a href=\"alimentacao_imc_det.php?idimc={$registro["idimc"]}&ref_aluno={$this->ref_aluno}\">{$registro["imc"]}</a>";
				$this->addLinhas($lista_busca);
			}
			
		}

		if( $obj_permissao->permissao_cadastra( 10006, $this->pessoa_logada, 3 ) )
		{		
			$this->acao = "go(\"alimentacao_imc_cad.php?ref_aluno=".$this->ref_aluno."\")";
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