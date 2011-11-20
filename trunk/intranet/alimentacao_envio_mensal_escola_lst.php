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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Envio Mensal Escola " );
		$this->processoAp = "10005";
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
	
	var $ref_escola;
	var $ano;
	var $mes;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Envio Mensal Escola - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$obj_permissao = new clsPermissoes();
		
		$lista_busca = array(
					"Escola",
					"Ano",
					"Mês",
					"Alunos",
					"Data Cadastro"
		);

		$this->addCabecalhos($lista_busca);

		$opcoes = array();
		$obj_escola = new clsPmieducarEscola();
		$lista = $obj_escola->lista();
		
		$opcoes["0"] = "Todas"; 
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				$opcoes[$registro["cod_escola"]] = $registro["nome"];
			}
			
		}
		
		$this->campoLista( "ref_escola", "Escola", $opcoes, $this->ref_escola,"",false,"","","",false );
		
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
		
		
		$filtro_escola = null;
		if($this->ref_escola > 0)
		{
			$filtro_escola = $this->ref_escola;
		}
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
		$lista = $obj_envio->lista(null,$filtro_escola,$filtro_ano,$filtro_mes);
		
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_escola"] );
				$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
				$nm_escola = $det_ref_cod_escola["nome"];
				
				
				$lista_busca = array();
				$lista_busca[] = "<a href=\"alimentacao_envio_mensal_escola_det.php?ideme={$registro["ideme"]}\">{$nm_escola}</a>";
				$lista_busca[] = "<a href=\"alimentacao_envio_mensal_escola_det.php?ideme={$registro["ideme"]}\">{$registro["ano"]}</a>";
				$lista_busca[] = "<a href=\"alimentacao_envio_mensal_escola_det.php?ideme={$registro["ideme"]}\">".$obj_envio->getMes($registro["mes"])."</a>";
				$lista_busca[] = "<a href=\"alimentacao_envio_mensal_escola_det.php?ideme={$registro["ideme"]}\">{$registro["alunos"]}</a>";
				$lista_busca[] = "<a href=\"alimentacao_envio_mensal_escola_det.php?ideme={$registro["ideme"]}\">".date('d/m/Y',strtotime($registro["dt_cadastro"]))."</a>";
				$this->addLinhas($lista_busca);
			}
			
		}

		if( $obj_permissao->permissao_cadastra( 10005, $this->pessoa_logada, 3 ) )
		{		
			$this->acao = "go(\"alimentacao_envio_mensal_escola_cad.php\")";
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