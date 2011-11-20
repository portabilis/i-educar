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
	
	var $nm_aluno;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "IMC - Listagem de alunos";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$obj_permissao = new clsPermissoes();
		
		$lista_busca = array(
					"Aluno",
					"Nome da mãe",
					"Data de Nascimento",
					"Sexo"
		);

		$this->addCabecalhos($lista_busca);

		$this->campoTexto( "nm_aluno", "Nome do aluno", $this->nm_aluno, 30, 255, false );

		$obj_cardapio = new clsAlimentacaoIMC();
		$filtro_aluno = null;
		if($this->nm_aluno != "")
		{
			$filtro_aluno = $this->nm_aluno;
		}
		
		if($filtro_aluno!="")
		{
			$lista = $obj_cardapio->listaAluno(null,$filtro_aluno);
			if( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista AS $registro )
				{
					$lista_busca = array();
					$lista_busca[] = "<a href=\"alimentacao_imc_lst_lst.php?ref_aluno={$registro["cod_aluno"]}\">{$registro["nome"]}</a>";
					$lista_busca[] = "<a href=\"alimentacao_imc_lst_lst.php?ref_aluno={$registro["cod_aluno"]}\">{$registro["nm_mae"]}</a>";
					$lista_busca[] = "<a href=\"alimentacao_imc_lst_lst.php?ref_aluno={$registro["cod_aluno"]}\">".date('d/m/Y',strtotime($registro["data_nasc"]))."</a>";
					$lista_busca[] = "<a href=\"alimentacao_imc_lst_lst.php?ref_aluno={$registro["cod_aluno"]}\">{$registro["sexo"]}</a>";
					$this->addLinhas($lista_busca);
				}
			}
		}

		/*
		if( $obj_permissao->permissao_cadastra( 10006, $this->pessoa_logada, 3 ) )
		{		
			$this->acao = "go(\"alimentacao_imc_cad.php\")";
			$this->nome_acao = "Novo";
		}*/
		
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