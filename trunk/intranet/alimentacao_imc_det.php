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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - IMC" );
		$this->processoAp = "10006";
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

	var $idimc;
	var $ref_aluno;
	
	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "IMC - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->idimc=$_GET["idimc"];		
		$this->ref_aluno=$_GET["ref_aluno"];
		
		$obj_imc = new clsAlimentacaoIMC();
		$lista = $obj_imc->lista($this->idimc);
		$registro = $lista[0];

		if( ! $registro )
		{
			header( "location: alimentacao_imc_lst.php" );
			die();
		}
		
		$lista = $obj_imc->listaAluno($registro["ref_aluno"]);
		if(is_array($lista))
		{
			$nm_aluno = $lista[0]["nome"];
			$this->ref_aluno = $registro["ref_aluno"];
		}
		
		$this->addDetalhe( array( "Aluno", $nm_aluno) );
		
		$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_escola"] );
		$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
		$nm_escola = $det_ref_cod_escola["nome"];
		
		$this->addDetalhe( array( "Escola", $nm_escola) );
		
		$obj_serie = new clsPmieducarSerie( $registro["ref_serie"] );
		$det_serie = $obj_serie->detalhe();
		$nm_serie = $det_serie["nm_serie"];
		
		$this->addDetalhe( array( "Série", $nm_serie) );
        $this->addDetalhe( array( "Data Cadastro", date('d/m/Y',strtotime($registro["dt_cadastro"]))) );
		
		
		$this->addDetalhe( array( "Altura", $registro["altura"]) );
		$this->addDetalhe( array( "Peso", $registro["peso"]) );
		$this->addDetalhe( array( "IMC", $registro["imc"]) );
		$this->addDetalhe( array( "Observações", $registro["observacao"]) );
		

		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 10006, $this->pessoa_logada, 3 ) )
		{
			$this->url_novo = "alimentacao_imc_cad.php?ref_aluno={$this->ref_aluno}";
            $this->url_editar = "alimentacao_imc_cad.php?idimc={$this->idimc}";
		}

		$this->url_cancelar = "alimentacao_imc_lst_lst.php?ref_aluno={$this->ref_aluno}";
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