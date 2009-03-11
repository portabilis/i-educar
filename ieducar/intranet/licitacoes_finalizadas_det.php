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
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/pessoa/clsPessoaFj.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Licita&ccedil;&otilde;es" );
		$this->processoAp = "159";
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
		$this->titulo = "Detalhe da licita&ccedil;&atilde;o finalizada";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$id_licitacao = @$_GET['id_licitacao'];

		$db = new clsBanco();
		$db->Consulta( "
			SELECT 
				m.nm_modalidade, 
				p.nm_pessoa, 
				l.numero, 
				l.objeto, 
				l.data_hora, 
				l.cod_compras_licitacoes, 
				e.ref_pregoeiro, 
				e.ref_equipe1, 
				e.ref_equipe2, 
				e.ref_equipe3, 
				e.ano_processo, 
				e.mes_processo, 
				e.seq_processo, 
				e.seq_portaria, 
				e.ano_portaria, 
				e.valor_referencia, 
				e.valor_real, 
				e.ref_cod_compras_final_pregao 
			FROM 
				compras_licitacoes l, 
				pessoa_fj p, 
				compras_modalidade m, 
				compras_pregao_execucao e
			WHERE 
				p.cod_pessoa_fj=l.ref_ref_cod_pessoa_fj AND 
				m.cod_compras_modalidade=l.ref_cod_compras_modalidade AND 
				cod_compras_licitacoes={$id_licitacao} AND 
				ref_cod_compras_licitacoes = cod_compras_licitacoes
		" );
		$db2 = new clsBanco();
		if ($db->ProximoRegistro())
		{
			list ($nm, $nome, $numero, $objeto, $data_c, $cod_licitacao, $ref_pregoeiro, $ref_equipe1, $ref_equipe2, $ref_equipe3, $ano_processo, $mes_processo, $seq_processo, $seq_portaria, $ano_portaria, $valor_referencia, $valor_real, $ref_final ) = $db->Tupla();
			$hora = date('H:i', strtotime(substr($data_c,0,19)));
			$data_c = date('d/m/Y', strtotime(substr($data_c,0,19)));
			$this->addDetalhe( array("Modalidade", $nm." ".$numero) );
			$this->addDetalhe( array("Objeto", $objeto) );
			$this->addDetalhe( array("Data", "{$data_c}") );
			$this->addDetalhe( array("Hora", $hora) );
			$pessoa = new clsPessoaFj( $ref_pregoeiro );
			$det = $pessoa->detalhe();
			$this->addDetalhe( array("Pregoeiro", $det["nm_pessoa"]) );
			$pessoa = new clsPessoaFj( $ref_equipe1 );
			$det = $pessoa->detalhe();
			$this->addDetalhe( array("Equipe 1", $det["nm_pessoa"]) );
			$pessoa = new clsPessoaFj( $ref_equipe2 );
			$det = $pessoa->detalhe();
			$this->addDetalhe( array("Equipe 2", $det["nm_pessoa"]) );
			$pessoa = new clsPessoaFj( $ref_equipe3 );
			$det = $pessoa->detalhe();
			$this->addDetalhe( array("Equipe 3", $det["nm_pessoa"]) );
			$this->addDetalhe( array("Ano do Processo", $ano_processo ) );
			$this->addDetalhe( array("Mes do Processo", $mes_processo ) );
			$this->addDetalhe( array("Sequencia Processo", $seq_processo ) );
			$this->addDetalhe( array("Sequencia Portaria", $seq_portaria ) );
			$this->addDetalhe( array("Ano da Portaria", $ano_portaria ) );
			$this->addDetalhe( array("Valor de Ref.", number_format( $valor_referencia, 2, ",", "." ) ) );
			$this->addDetalhe( array("Valor Real", number_format( $valor_real, 2, ",", "." ) ) );
			$this->addDetalhe( array("Diferença", number_format( ( $valor_referencia - $valor_real ), 2, ",", "." ) ) );
			$this->addDetalhe( array("Diferença em %", number_format( 100 - ( ( $valor_real / $valor_referencia ) * 100 ), 2, ",", "." ) ) );
			$nmFinal = $db2->UnicoCampo( "SELECT nm_final FROM compras_final_pregao WHERE cod_compras_final_pregao = '{$ref_final}'" );
			$this->addDetalhe( array("Status Final", $nmFinal ) );
		}
		$this->url_editar = "licitacoes_funcionarios_cad.php?id_licitacao=$id_licitacao";
		$this->url_cancelar = "licitacoes_finalizadas_lst.php";

		$this->largura = "100%";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>