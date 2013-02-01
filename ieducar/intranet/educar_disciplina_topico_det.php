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

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Disciplina T&oacute;pico" );
		$this->processoAp = "565";
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
	
	var $cod_disciplina_topico;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_topico;
	var $desc_topico;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	
	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();
		
		$this->titulo = "Disciplina T&oacute;pico - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_disciplina_topico=$_GET["cod_disciplina_topico"];

		$tmp_obj = new clsPmieducarDisciplinaTopico( $this->cod_disciplina_topico );
		$registro = $tmp_obj->detalhe();
		
		if( ! $registro )
		{
			header( "location: educar_disciplina_topico_lst.php" );
			die();
		}
		
		if( $registro["nm_topico"] )
		{
			$this->addDetalhe( array( "Nome T&oacute;pico", "{$registro["nm_topico"]}") );
		}
		if( $registro["desc_topico"] )
		{
			$this->addDetalhe( array( "Descri&ccedil;&atilde;o T&oacute;pico", "{$registro["desc_topico"]}") );
		}

		$objPermissao = new clsPermissoes();
		if( $objPermissao->permissao_cadastra( 565, $this->pessoa_logada,7 ) ) {		
			$this->url_novo = "educar_disciplina_topico_cad.php";
			$this->url_editar = "educar_disciplina_topico_cad.php?cod_disciplina_topico={$registro["cod_disciplina_topico"]}";
		}
		$this->url_cancelar = "educar_disciplina_topico_lst.php";
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