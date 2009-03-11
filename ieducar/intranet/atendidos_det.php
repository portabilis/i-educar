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

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Pessoa" );
		$this->processoAp = "43";
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
		$this->titulo = "Detalhe da Pessoa";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$cod_pessoa = @$_GET['cod_pessoa'];

		$objPessoa = new clsPessoaFisica($cod_pessoa);
		$db = new clsBanco();
		$detalhe = $objPessoa->queryRapida($cod_pessoa, "idpes", "complemento","nome", "cpf", "data_nasc", "logradouro", "idtlog", "numero", "apartamento","cidade","sigla_uf", "cep", "ddd_1", "fone_1", "ddd_2", "fone_2", "ddd_mov", "fone_mov", "ddd_fax", "fone_fax", "email", "url", "tipo", "sexo");
		$this->addDetalhe( array("Nome", $detalhe['nome']) );
		$this->addDetalhe( array("CPF", int2cpf( $detalhe['cpf'] ) ) );
		if($detalhe['data_nasc'])
		{
			$this->addDetalhe( array("Data de Nascimento", dataFromPgToBr($detalhe['data_nasc'])) );
		}
		if($detalhe['logradouro'])
		{
			if($detalhe['numero'])
			{
				$end = " nº {$detalhe['numero']}";
			}
			if($detalhe['apartamento'])
			{
				$end .= " apto {$detalhe['apartamento']}";
			}
			$this->addDetalhe( array("Endere&ccedil;o", strtolower($detalhe['idtlog']).": {$detalhe['logradouro']} $end") );
		}
		if($detalhe['complemento'])
		{
			$this->addDetalhe( array("Complemento", $detalhe['complemento']  ) );
		}
		if($detalhe['cidade'])
		{
			$this->addDetalhe( array("Cidade", strtolower($detalhe['cidade'])  ) );
		}
		if($detalhe['sigla_uf'])
		{
			$this->addDetalhe( array("Estado", strtolower($detalhe['sigla_uf'])  ) );
		}

		if($detalhe['cep'])
		{
			$this->addDetalhe( array("CEP", int2cep( $detalhe['cep'] ) ) );
		}

		if($detalhe['fone_1'])
		{
			$this->addDetalhe( array("Telefone 1", "({$detalhe['ddd_1'] }) {$detalhe['fone_1'] }") );
		}
		if($detalhe['fone_2'])
		{
			$this->addDetalhe( array("Telefone 2", "({$detalhe['ddd_2'] }) {$detalhe['fone_2'] }") );
		}
		if($detalhe['fone_mov'])
		{
			$this->addDetalhe( array("Celular", "({$detalhe['ddd_mov'] }) {$detalhe['fone_mov'] }") );
		}
		if($detalhe['fone_fax'])
		{
			$this->addDetalhe( array("Fax", "({$detalhe['ddd_fax'] }) {$detalhe['fone_fax'] }") );
		}

		if($detalhe['url'])
		{
			$this->addDetalhe( array("Site", $detalhe['url']) );
		}
		if($detalhe['email'])
		{
			$this->addDetalhe( array("E-mail", $detalhe['email']) );
		}

		$sexo = ($detalhe['sexo'] == "M") ? "Masculino" : "Feminino";
		$this->addDetalhe( array("Sexo", $sexo) );
		$this->url_novo = "atendidos_cad.php";
		$this->url_editar = "atendidos_cad.php?cod_pessoa_fj={$detalhe['idpes']}";
		$this->url_cancelar = "atendidos_lst.php";

		$this->largura = "100%";
	}
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>