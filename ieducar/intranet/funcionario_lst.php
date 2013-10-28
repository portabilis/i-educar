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
require_once 'include/localizacaoSistema.php';

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Usu&aacute;rios!" );
		$this->processoAp = "36";
                $this->addEstilo( "localizacaoSistema" );
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$this->titulo = "Usu&aacute;rios";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );
		$this->addCabecalhos( array( "Nome", "Status") );

		// Filtros de Busca
		$this->campoTexto("nm_pessoa", "Nome", "", 50, 255);
		$this->campoTexto("matricula", "Matricula", "", 10, 15);

		// Paginador
		$limite = 10;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;

		$obj_func = new clsFuncionario();
		$obj_func->setOrderby("to_ascii(nome) ASC");
		$obj_func->setLimite($limite, $iniciolimit);
		$lst_func = $obj_func->lista($_GET["matricula"], $_GET['nm_pessoa']);

		if($lst_func)
		{
			foreach ( $lst_func AS $pessoa )
			{
				$ativo = ($pessoa['ativo'] == '1') ? "Ativo" : "Inativo";
				$total = $pessoa['_total'];
				$pessoa['nome']  = minimiza_capitaliza($pessoa['nome']);
				$this->addLinhas( array("<a href='funcionario_det.php?ref_pessoa={$pessoa['ref_cod_pessoa_fj']}'><img src='imagens/noticia.jpg' border=0>{$pessoa['nome']}</a>", $ativo) );
			}
		}

		$this->addPaginador2( "funcionario_lst.php", $total, $_GET, $this->nome, $limite );
		$this->acao = "go(\"funcionario_cad.php\")";
		$this->nome_acao = "Novo";

		$this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos(array($_SERVER['SERVER_NAME'] . '/intranet' => 'i-Educar', '' => 'Lista de Funcionários'));

    $this->enviaLocalizacao($localizacao->montar());
	}
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>
