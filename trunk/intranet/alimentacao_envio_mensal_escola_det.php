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
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once( "include/alimentacao/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Envio Mensal Escola" );
		$this->processoAp = "10005";
	}
}

class indice extends clsDetalhe
{
	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	var $ideme;
	
	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Envio Mensal Escola - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->ideme=$_GET["ideme"];
		
		$obj_envio = new clsAlimentacaoEnvioMensalEscola();
		$lista = $obj_envio->lista($this->ideme);
		$registro = $lista[0];

		if( ! $registro )
		{
			header( "location: alimentacao_envio_mensal_escola_lst.php" );
			die();
		}
		
		
		$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_escola"] );
		$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
		$nm_escola = $det_ref_cod_escola["nome"];
				
		$this->addDetalhe( array( "Escola", $nm_escola) );
		
		$this->addDetalhe( array( "Ano", $registro["ano"]) );
		
		$this->addDetalhe( array( "Mês", $obj_envio->getMes($registro["mes"])) );
		
		$this->addDetalhe( array( "Alunos", $registro["alunos"]) );
				
		$this->addDetalhe( array( "Data Cadastro", date('d/m/Y',strtotime($registro["dt_cadastro"]))) );
		
		$this->addDetalhe( array( "Dias", $registro["dias"]) );
		
		$this->addDetalhe( array( "Refeições por dia", $registro["refeicoes"]) );

		$obj = new clsAlimentacaoEnvioMensalEscolaProduto();
		$registros = $obj->lista($this->ideme);
		
		$teste = "<table cellspacing='3'>";
		$teste .= "<tr><td class='formmdtd'><span class='form'><b>Produto</b></span></td><td class='formmdtd'><span class='form'><b>Quantidade</b></span></td></tr>";

		if( $registros )
		{
			foreach ( $registros AS $campo )
			{
				$prod_cad = "0";
				if ( $campo["ref_envio_mensal_escola"] != "" )
				{
					$prod_cad = "1";
					}
				$campo["pesoouvolume"] = number_format($campo["pesoouvolume"],2,",","");
				$teste .= "<tr><td class='formmdtd'><span class='form'>".$campo["nm_produto"]."</span></td><td class='formmdtd'><span class='form'>".$campo["pesoouvolume"]." ".$campo["unidade"]."</span></td></tr>";
				
			}
		}
		$teste .= "</table>";
		
		$this->addDetalhe( array("Quantidade por produto",$teste));
		

		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 10005, $this->pessoa_logada, 3 ) )
		{
			$this->url_novo = "alimentacao_envio_mensal_escola_cad.php";
            $this->url_editar = "alimentacao_envio_mensal_escola_cad.php?ideme={$this->ideme}";
		}

		$this->url_cancelar = "alimentacao_envio_mensal_escola_lst.php";
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