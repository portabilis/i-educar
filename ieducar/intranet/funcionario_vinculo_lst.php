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

$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once 'include/localizacaoSistema.php';

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Vínculo Funcionários!" );
		$this->processoAp = "190";
                $this->addEstilo( "localizacaoSistema" );
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$this->titulo = "Vínculos";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );
		
		$nome_ = @$_GET['nome_'];
		
		$this->addCabecalhos( array( "Nome"));

		$this->campoTexto( "nome_", "Nome",  $nome_, "50", "255", true );

		$db = new clsBanco();
		$sql  = "SELECT cod_funcionario_vinculo, nm_vinculo FROM funcionario_vinculo";		
		$where = "";
		$where_and = "";
		if (!empty($nome_))
		{
			$where .= $where_and." nm_vinculo LIKE '%$nome_%' ";
			$where_and = " AND";
		}
		if($where)
		{
			$where = " WHERE $where";
		}
		$sql .= $where." ORDER BY nm_vinculo";
		$db->Consulta( "SELECT count(*) FROM funcionario_vinculo $where" );
		$db->ProximoRegistro();
		list ($total) = $db->Tupla();

		// Paginador
		$limite = 10;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;
		$sql .= " LIMIT $iniciolimit,$limite";
		$db->Consulta( $sql );

		while ($db->ProximoRegistro())
		{
			list ($cod_func_vinculo, $nome) = $db->Tupla();
			$this->addLinhas( array( "<img src='imagens/noticia.jpg' border=0> <a href='funcionario_vinculo_det.php?cod_func=$cod_func_vinculo'>$nome</a>") );
		}
		$this->largura = "100%";
                
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    ""                                  => "Vínculos"
                ));
                $this->enviaLocalizacao($localizacao->montar());
                
		// Paginador
		$this->addPaginador2( "funcionario_vinculo_lst.php", $total, $_GET, $this->nome, $limite );
		$this->acao = "go(\"funcionario_vinculo_cad.php\")";
		$this->nome_acao = "Novo";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>