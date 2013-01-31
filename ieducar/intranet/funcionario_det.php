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
		$this->SetTitulo( "{$this->_instituicao} Usu&aacute;rios" );
		$this->processoAp = "36";
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
		$this->titulo = "Detalhe do usu&aacute;rio";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$cod_pessoa = @$_GET['ref_pessoa'];

		$obj_pessoa = new clsPessoa_($cod_pessoa);
		$det_pessoa = $obj_pessoa->detalhe();

		$this->addDetalhe( array("Nome", $det_pessoa["nome"]) );

		$obj_fisica_cpf = new clsFisica($cod_pessoa);
		$det_fisica_cpf = $obj_fisica_cpf->detalhe();
		$this->addDetalhe( array("CPF", int2CPF($det_fisica_cpf["cpf"])) );

		$obj_endereco = new clsEndereco($cod_pessoa);
		$det_endereco = $obj_endereco->detalhe();

		if($det_endereco["tipo_origem"] == "endereco_pessoa")
		{
			$this->addDetalhe( array("CEP", int2CEP($det_endereco["cep"])) );

			$obj_bairro = new clsBairro($det_endereco["idbai"]);
			$det_bairro = $obj_bairro->detalhe();

			$this->addDetalhe( array("Bairro", $det_bairro["nome"]) );

			//echo "det: {$det_bairro["idmun"]}";
			$obj_municipio = $det_bairro["idmun"];
			$det_municipio = $obj_municipio->detalhe();

			$this->addDetalhe( array("Cidade", $det_municipio["nome"]) );
			for($i = 1; $i <= 4; $i++)
			{
				$obj_fone_pessoa = new clsPessoaTelefone($cod_pessoa, $i);
				$det_fone_pessoa = $obj_fone_pessoa->detalhe();

				if($det_fone_pessoa)
				{
					switch($i):
					case 1:
						$this->addDetalhe( array("Telefone 1", "({$det_fone_pessoa["ddd"]}) {$det_fone_pessoa["fone"]}") );
						break;
					case 2:
						$this->addDetalhe( array("Telefone 2", "({$det_fone_pessoa["ddd"]}) {$det_fone_pessoa["fone"]}") );
						break;
					case 3:
						$this->addDetalhe( array("Celular", "({$det_fone_pessoa["ddd"]}) {$det_fone_pessoa["fone"]}") );
						break;
					case 4:
						$this->addDetalhe( array("Fax", "({$det_fone_pessoa["ddd"]}) {$det_fone_pessoa["fone"]}") );
						break;
					endswitch;
				}
			}
		}
		elseif ($det_endereco["tipo_origem"] == "endereco_externo")
		{
			$this->addDetalhe( array("CEP", int2CEP($det_endereco["cep"])) );
			$this->addDetalhe( array("Bairro", $det_endereco["bairro"]) );
			$this->addDetalhe( array("Cidade", $det_endereco["cidade"]) );
			for($i = 1; $i <= 4; $i++)
			{
				$obj_fone_pessoa = new clsPessoaTelefone($cod_pessoa, $i);
				$det_fone_pessoa = $obj_fone_pessoa->detalhe();

				if($det_fone_pessoa)
				{
					switch($i):
					case 1:
						$this->addDetalhe( array("Telefone 1", "({$det_fone_pessoa["ddd"]}) {$det_fone_pessoa["fone"]}") );
						break;
					case 2:
						$this->addDetalhe( array("Telefone 2", "({$det_fone_pessoa["ddd"]}) {$det_fone_pessoa["fone"]}") );
						break;
					case 3:
						$this->addDetalhe( array("Celular", "({$det_fone_pessoa["ddd"]}) {$det_fone_pessoa["fone"]}") );
						break;
					case 4:
						$this->addDetalhe( array("Fax", "({$det_fone_pessoa["ddd"]}) {$det_fone_pessoa["fone"]}") );
						break;
					endswitch;
				}
			}
		}

		$obj_funcionario = new clsFuncionario($cod_pessoa);
		$det_funcionario = $obj_funcionario->detalhe();

		$this->addDetalhe( array("Ramal", $det_funcionario["ramal"]) );

		$this->addDetalhe( array("Site", $det_pessoa["url"]) );
		//$this->addDetalhe( array("E-mail", $det_pessoa["email"]) );
		$this->addDetalhe( array("E-mail usuário", $det_funcionario["email"]) );

		$obj_fisica = new clsFisica($cod_pessoa);
		$det_fisica = $obj_fisica->detalhe();

		$sexo = ($det_fisica["sexo"] == "M") ? "Masculino" : "Feminino";
		$this->addDetalhe( array("Sexo", $sexo) );

		$this->addDetalhe( array("Matricula", $det_funcionario["matricula"]) );
		$this->addDetalhe( array("Sequencial", $det_funcionario["sequencial"]) );
		$ativo_f = ($det_funcionario["ativo"] == '1') ? "Ativo" : "Inativo";
		$this->addDetalhe( array("Status", $ativo_f) );

		$dba = new clsBanco();
		$dba->Consulta( "SELECT ref_cod_menu_submenu FROM menu_funcionario WHERE ref_ref_cod_pessoa_fj={$cod_pessoa} " );
		$cod_menu = array();
		while ($dba->ProximoRegistro())
		{
			list ($cod_menu[]) = $dba->Tupla();
		}

		$super_user = false;
		foreach ($cod_menu as $cod)
		{
			if ($cod == "0")
			{
				$super_user = true;
				continue;
			}
		}

		if ( $det_funcionario["proibido"] )
		{
			$this->addDetalhe( array("M&oacute;dulos", "<b>Banido</b>") );
		}
		if ($super_user)
		{
			$this->addDetalhe( array("M&oacute;dulos", "<b>Super Usu&aacute;rio</b>") );
		}
		else
		{
			foreach ($cod_menu as $cod)
			{
				$dba->Consulta( "SELECT nm_submenu FROM menu_submenu WHERE cod_menu_submenu={$cod}" );
				$dba->ProximoRegistro();
				list($nm_item) = $dba->Tupla();
				$this->addDetalhe( array("M&oacute;dulos", $nm_item) );

			}
		}

		$this->url_novo = "funcionario_cad.php";
		$this->url_editar = "funcionario_cad.php?ref_pessoa={$cod_pessoa}";
		$this->url_cancelar = "funcionario_lst.php";
		$this->largura = "100%";
	}
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();

?>
