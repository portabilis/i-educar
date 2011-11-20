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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Envio Mensal Padrões " );
		$this->processoAp = "10011";
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
	
	var $ano;
	var $mes;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Envio Mensal Padrões - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$obj_permissao = new clsPermissoes();
		
		$lista_busca = array(
					"Ano",
					"Mês",
					"Dias",
					"Refeições por dia"
		);

		$this->addCabecalhos($lista_busca);


		$opcoes = array();
		$opcoes[0] = "Todos";
		for ($i = 2008; $i <= date("Y");$i++)
		{
			$opcoes[$i] = $i;
		}
		$this->campoLista( "ano", "Ano", $opcoes, $this->ano,"",false,"","","",false );
		
		
		$obj_envio = new clsAlimentacaoEnvioMensalEscola();
		
		$opcoes = array();
		$opcoes = $obj_envio->getArrayMes();
		$opcoes[0]  = "Todos"; 
		
		$this->campoLista( "mes", "Mês", $opcoes, $this->mes,"",false,"","","",false );


		$obj_envio_mensal_padroes = new clsAlimentacaoEnvioMensalPadroes();
		$filtro_ano = null;
		if($this->ano > 0)
		{
			$filtro_ano = $this->ano;
		}
		$filtro_mes = null;
		if($this->mes > 0)
		{
			$filtro_mes = $this->mes;
		}
		$lista = $obj_envio_mensal_padroes->lista(null,$filtro_ano,$filtro_mes);
		
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				
				$lista_busca = array();
				$lista_busca[] = "<a href=\"alimentacao_envio_mensal_padroes_det.php?idemp={$registro["idemp"]}\">{$registro["ano"]}</a>";
				$lista_busca[] = "<a href=\"alimentacao_envio_mensal_padroes_det.php?idemp={$registro["idemp"]}\">{$obj_envio_mensal_padroes->getMes($registro["mes"])}</a>";
				$lista_busca[] = "<a href=\"alimentacao_envio_mensal_padroes_det.php?idemp={$registro["idemp"]}\">{$registro["dias"]}</a>";
				$lista_busca[] = "<a href=\"alimentacao_envio_mensal_padroes_det.php?idemp={$registro["idemp"]}\">{$registro["refeicoes"]}</a>";
				$this->addLinhas($lista_busca);
			}
			
		}

		if( $obj_permissao->permissao_cadastra( 10011, $this->pessoa_logada, 3 ) )
		{		
			$this->acao = "go(\"alimentacao_envio_mensal_padroes_cad.php\")";
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