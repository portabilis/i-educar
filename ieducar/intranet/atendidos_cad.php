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
require_once ("include/clsBanco.inc.php");
require_once ("include/clsCadastro.inc.php");



class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Pessoas F&iacute;sicas - Cadastro!" );
		$this->processoAp = "43";
	}
}

class indice extends clsCadastro
{
	var $cod_pessoa_fj, $nm_pessoa, $id_federal, $data_nasc, $endereco, $cep, $idlog, $idbai, $sigla_uf, $ddd_telefone_1, $telefone_1, $ddd_telefone_2, $telefone_2, $ddd_telefone_mov, $telefone_mov, $ddd_telefone_fax, $telefone_fax, $email, $http, $tipo_pessoa, $sexo;
	var $busca_pessoa;
	var $complemento;
	var $apartamento;
	var $bloco;
	var $andar;
	var $numero;
	var $retorno;

	var $caminho_det, $caminho_lst;
	
	var $alterado;

	function Inicializar()
	{
		if( $_REQUEST['busca_pessoa'])
		{
			
			$this->retorno = "Novo";
			$cpf = idFederal2int( $_REQUEST['busca_pessoa'] );
			$this->busca_pessoa = $cpf;
			$this->id_federal =  $cpf;
			$objPessoa = new clsPessoaFisica( false, $cpf );
			$detalhePessoa = $objPessoa->detalhe();
			
			$this->cod_pessoa_fj = $detalhePessoa["idpes"];
		}elseif( $_REQUEST['cod_pessoa_fj'] != "")
		{
			$this->busca_pessoa	= true;
			if($_REQUEST['cod_pessoa_fj'] != 0)
			{
				$this->cod_pessoa_fj = $_REQUEST['cod_pessoa_fj'];
			}
			else
			{
				$this->retorno = "Novo";
			}
		}

		if( $this->cod_pessoa_fj )
		{
			$this->cod_pessoa_fj = (@$_GET['cod_pessoa']) ? @$_GET['cod_pessoa'] : $this->cod_pessoa_fj;
			$db = new clsBanco();
			$objPessoa = new clsPessoaFisica();
			list( $this->nm_pessoa,$this->id_federal, $this->data_nasc, $this->ddd_telefone_1, $this->telefone_1, $this->ddd_telefone_2, $this->telefone_2, $this->ddd_telefone_mov, $this->telefone_mov, $this->ddd_telefone_fax, $this->telefone_fax, $this->email, $this->http, $this->tipo_pessoa, $this->sexo, $this->cidade, $this->bairro, $this->logradouro, $this->cep, $this->idlog, $this->idbai, $this->idtlog, $this->sigla_uf, $this->complemento, $this->numero, $this->bloco, $this->apartamento, $this->andar) = $objPessoa->queryRapida( $this->cod_pessoa_fj, "nome", "cpf", "data_nasc", "ddd_1", "fone_1", "ddd_2", "fone_2", "ddd_mov", "fone_mov", "ddd_fax", "fone_fax", "email", "url", "tipo", "sexo", "cidade", "bairro", "logradouro", "cep", "idlog", "idbai", "idtlog", "sigla_uf", "complemento", "numero", "bloco", "apartamento", "andar");
			$this->cep = int2Cep($this->cep);
			$this->retorno = "Editar";
		}
		$this->nome_url_cancelar = "Cancelar";

		return $this->retorno;
	}

	function Gerar()
	{
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet",false );

		if( !$this->busca_pessoa )
		{
			$this->campoOculto("cod_pessoa_fj","");
			$parametros = new clsParametrosPesquisas();
			$parametros->setSubmit( 1 );
			$parametros->adicionaCampoTexto( "busca_pessoa", "id_federal" );
			$parametros->adicionaCampoTexto( "cod_pessoa_fj", "idpes" );
			$parametros->setPessoa( 'F' );
			$parametros->setPessoaCampo( "cod_pessoa_fj" );
			$parametros->setPessoaNovo( 'S' );
			$parametros->setPessoaTela( "window" );
			$this->campoCpf("busca_pessoa", "CPF", $this->ref_cod_pessoa_fj, true, "<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'pesquisa_pessoa_lst.php?campos=".$parametros->serializaCampos()."\'></iframe>' );\">", false, true );

		}
		else
		{
			$this->campoOculto("busca_pessoa",$this->busca_pessoa);
			$this->url_cancelar = ( $this->retorno == "Editar" ) ? "atendidos_det.php?cod_pessoa={$this->cod_pessoa_fj}" : "atendidos_lst.php";
			$this->campoOculto( "cod_pessoa_fj", $this->cod_pessoa_fj );
			$this->campoTexto( "nm_pessoa", "Nome",  $this->nm_pessoa, "50", "255", true );
			if($this->id_federal)
			{
				$this->campoRotulo( "id_federal", "CPF", int2CPF( $this->id_federal ));
			}else
			{
				$this->campoCpf( "id_federal", "CPF","", false);
			}
			
			if( $this->data_nasc ) 
			{
				$this->data_nasc = dataFromPgToBr($this->data_nasc);
			}
			$this->campoData( "data_nasc", "Data de Nascimento", $this->data_nasc );
			
			$lista_sexos = array();
			$lista_sexos[""] = "Escolha uma op&ccedil;&atilde;o...";
			$lista_sexos["M"] = "Masculino";
			$lista_sexos["F"] = "Feminino";
			$this->campoLista( "sexo", "Sexo", $lista_sexos, $this->sexo);

			// Detalhes do Endereï¿½o
			$objTipoLog = new clsTipoLogradouro();
			$listaTipoLog = $objTipoLog->lista();
			$listaTLog = array("0"=>"Selecione");
			if($listaTipoLog)
			{
				foreach ($listaTipoLog as $tipoLog) {
					$listaTLog[$tipoLog['idtlog']] = $tipoLog['descricao'];
				}
			}

			$objUf = new clsUf();
			$listauf = $objUf->lista();
			$listaEstado = array("0"=>"Selecione");
			if($listauf)
			{
				foreach ($listauf as $uf) {
					$listaEstado[$uf['sigla_uf']] = $uf['sigla_uf'];
				}
			}

			$this->campoOculto( "idbai", $this->idbai );
			$this->campoOculto( "idlog", $this->idlog );
			$this->campoOculto( "cep", $this->cep );
			$this->campoOculto( "ref_sigla_uf", $this->sigla_uf);
			$this->campoOculto( "ref_idtlog", $this->idtlog);
			$this->campoOculto( "id_cidade", $this->cidade);

			if($this->idlog && $this->idbai && $this->cep && $this->cod_pessoa_fj)
			{
			  	$this->campoCep("cep_", "CEP", $this->cep, true, "-", "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro.php?campo1=bairro&campo2=idbai&campo3=cep&campo4=logradouro&campo5=idlog&campo6=ref_sigla_uf&campo7=cidade&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=sigla_uf&campo12=idtlog&campo13=id_cidade\'></iframe>');\">", true);
				$this->campoLista("idtlog","Tipo Logradouro",$listaTLog,$this->idtlog,false,false,false,false,true);
				$this->campoTextoInv( "logradouro", "Logradouro",  $this->logradouro, "50", "255", false );
				$this->campoTextoInv( "cidade", "Cidade",  $this->cidade, "50", "255", false );
				$this->campoTextoInv( "bairro", "Bairro",  $this->bairro, "50", "255", false );
				$this->campoTexto( "complemento", "Complemento",  $this->complemento, "50", "255", false );
				$this->campoTexto( "numero", "N&uacute;mero",  $this->numero, "10", "10" );
				$this->campoTexto( "letra", "Letra",  $this->letra, "1", "1", false );
				$this->campoTexto( "apartamento", "N&uacute;mero Apartametno",  $this->apartamento, "6", "6", false );
				$this->campoTexto( "bloco", "Bloco",  $this->bloco, "20", "20", false );
				$this->campoTexto( "andar", "Andar",  $this->andar, "2", "2", false );
				$this->campoLista("sigla_uf","Estado",$listaEstado,$this->sigla_uf,false,false,false,false,true);
			}
			elseif($this->cod_pessoa_fj && $this->cep)
			{
			  	$this->campoCep("cep_", "CEP", $this->cep, true, "-", "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro.php?campo1=bairro&campo2=idbai&campo3=cep&campo4=logradouro&campo5=idlog&campo6=ref_sigla_uf&campo7=cidade&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=sigla_uf&campo12=idtlog&campo13=id_cidade\'></iframe>');\">", $disabled);
				$this->campoLista("idtlog","Tipo Logradouro",$listaTLog,$this->idtlog);
				$this->campoTexto( "logradouro", "Logradouro",  $this->logradouro, "50", "255", false );
				$this->campoTexto( "cidade", "Cidade",  $this->cidade, "50", "255", false );
				$this->campoTexto( "bairro", "Bairro",  $this->bairro, "50", "255", false );
				$this->campoTexto( "complemento", "Complemento",  $this->complemento, "50", "255", false );
				$this->campoTexto( "numero", "N&uacute;mero",  $this->numero, "10", "10" );
				$this->campoTexto( "letra", "Letra",  $this->letra, "1", "1", false );
				$this->campoTexto( "apartamento", "N&uacute;mero Apartametno",  $this->apartamento, "6", "6", false );
				$this->campoTexto( "bloco", "Bloco",  $this->bloco, "20", "20", false );
				$this->campoTexto( "andar", "Andar",  $this->andar, "2", "2", false );
				$this->campoLista("sigla_uf","Estado",$listaEstado,$this->sigla_uf);
			}
			else
			{
			  	$this->campoCep("cep_", "CEP", $this->cep, true, "-", "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro.php?campo1=bairro&campo2=idbai&campo3=cep&campo4=logradouro&campo5=idlog&campo6=ref_sigla_uf&campo7=cidade&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=sigla_uf&campo12=idtlog&campo13=id_cidade\'></iframe>');\">", false/*$disabled*/);

				$this->campoLista("idtlog","Tipo Logradouro",$listaTLog,$this->idtlog,false,false,false,false,false);
				$this->campoTexto( "logradouro", "Logradouro",  $this->logradouro, "50", "255" );
				$this->campoTexto( "cidade", "Cidade",  $this->cidade, "50", "255");
				$this->campoTexto( "bairro", "Bairro",  $this->bairro, "50", "255" );
				$this->campoTexto( "complemento", "Complemento",  $this->complemento, "50", "255", false );
				$this->campoTexto( "numero", "N&uacute;mero",  $this->numero, "10", "10" );
				$this->campoTexto( "letra", "Letra",  $this->letra, "1", "1", false );
				$this->campoTexto( "apartamento", "N&uacute;mero Apartametno",  $this->apartamento, "6", "6", false );
				$this->campoTexto( "bloco", "Bloco",  $this->bloco, "20", "20", false );
				$this->campoTexto( "andar", "Andar",  $this->andar, "2", "2", false );
				$this->campoLista("sigla_uf","Estado",$listaEstado,$this->sigla_uf,false,false,false,false,false);
			}

			$this->campoTexto( "ddd_telefone_1", "DDD Telefone 1",  $this->ddd_telefone_1, "3", "2", false );
			$this->campoTexto( "telefone_1", "Telefone 1",  $this->telefone_1, "10", "15", false );
			$this->campoTexto( "ddd_telefone_2", "DDD Telefone 2",  $this->ddd_telefone_2, "3", "2", false );
			$this->campoTexto( "telefone_2", "Telefone 2",  $this->telefone_2, "10", "15", false );
			$this->campoTexto( "ddd_telefone_mov", "DDD Celular",  $this->ddd_telefone_mov, "3", "2", false );
			$this->campoTexto( "telefone_mov", "Celular",  $this->telefone_mov, "10", "15", false );
			$this->campoTexto( "ddd_telefone_fax", "DDD Fax",  $this->ddd_telefone_fax, "3", "2", false );
			$this->campoTexto( "telefone_fax", "Fax",  $this->telefone_fax, "10", "15", false );

			$this->campoTexto( "http", "Site",  $this->http, "50", "255", false );
			$this->campoTexto( "email", "E-mail",  $this->email, "50", "255", false );

			/***********************
			Documentos
			***********************/
			if($this->cod_pessoa_fj)
			{
				$this->campoRotulo("documentos","<b><i>Documentos</i></b>","<a href='#' onclick=\" openPage('adicionar_documentos_cad.php?id_pessoa={$this->cod_pessoa_fj}','400','400','yes', '10','10'); \"><img src='imagens/nvp_bot_ad_doc.png' border='0'></a>");

				$this->campoCheck("alterado", "Alterado", $this->alterado);
			}
		}
	}

	function Novo()
	{
		@session_start();
		$pessoaFj = $_SESSION['id_pessoa'];
		session_write_close();
		$db = new clsBanco();
		$db2 = new clsBanco();

		$ref_cod_sistema = false;

		if($this->id_federal)
		{
			$this->id_federal = idFederal2int( $this->id_federal );
			$objCPF = new clsFisica( false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false,false, false, $this->id_federal );
			$detalhe_fisica = $objCPF->detalhe();
			if( $detalhe_fisica['cpf'] )
			{
				$this->erros['id_federal'] = "CPF j&aacute; cadastrado.";
				return false;
			}
		}

		$objPessoa = new clsPessoa_( false, $this->nm_pessoa, $pessoaFj, $this->http, "F", false, false, $this->email);
		$idpes = $objPessoa->cadastra();
		
		$this->data_nasc = dataToBanco($this->data_nasc);
		
		if($this->id_federal)
		{
			$objFisica = new clsFisica( $idpes, $this->data_nasc, $this->sexo,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,$ref_cod_sistema, $this->id_federal );			
		}else 
		{
			$objFisica = new clsFisica( $idpes, $this->data_nasc, $this->sexo,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,$ref_cod_sistema);			
		}
		$objFisica->cadastra();

		$objTelefone = new clsPessoaTelefone( $idpes, 1, $this->telefone_1, $this->ddd_telefone_1 );
		$objTelefone->cadastra();
		$objTelefone = new clsPessoaTelefone( $idpes, 2, $this->telefone_2, $this->ddd_telefone_2 );
		$objTelefone->cadastra();
		$objTelefone = new clsPessoaTelefone( $idpes, 3, $this->telefone_mov, $this->ddd_telefone_mov );
		$objTelefone->cadastra();
		$objTelefone = new clsPessoaTelefone( $idpes, 4, $this->telefone_fax, $this->ddd_telefone_fax );
		$objTelefone->cadastra();

		if($this->cep && $this->idbai && $this->idlog)
		{
			$this->cep = idFederal2Int($this->cep);
			$objEndereco = new clsPessoaEndereco( $idpes );
			$objEndereco2 = new clsPessoaEndereco($idpes, $this->cep,$this->idlog,$this->idbai,$this->numero,$this->complemento,false,$this->letra,$this->bloco,$this->apartamento,$this->andar);
			if( $objEndereco->detalhe() )
			{
				$objEndereco2->edita();
			}
			else
			{
				$objEndereco2->cadastra();
			}
		}
		elseif($this->cep_)
		{
			$this->cep_ = idFederal2int($this->cep_);
			$objEnderecoExterno = new clsEnderecoExterno( $idpes );
			$objEnderecoExterno2 = new clsEnderecoExterno( $idpes, "1",$this->idtlog,$this->logradouro,$this->numero,$this->letra,$this->complemento,$this->bairro,$this->cep_,$this->cidade,$this->sigla_uf,false,$this->bloco,$this->apartamento,$this->andar);
			if( $objEnderecoExterno->detalhe() )
			{
				$objEnderecoExterno2->edita();
			}
			else
			{
				$objEnderecoExterno2->cadastra();
			}
		}
		echo "<script>document.location='atendidos_lst.php';</script>";

		return true;
	}

	function Editar()
	{
		@session_start();
		$pessoaFj = $_SESSION['id_pessoa'];
		session_write_close();


		if($this->id_federal)
		{
			$ref_cod_sistema = "null";
			$this->id_federal = idFederal2int( $this->id_federal );
			$objFisicaCpf = new clsFisica($this->cod_pessoa_fj);
			$detalhe_fisica = $objFisicaCpf->detalhe();
			
			if(!$detalhe_fisica['cpf'])
			{

				$objCPF = new clsFisica( false, false, false,false, false, false, false,false, false, false, false,false, false, false, false,false, false, false, false,false, false, false, false,false, false, $this->id_federal );
				if( $objCPF->detalhe() )
				{
					$this->erros['id_federal'] = "CPF j&aacute; cadastrado.";
					return false;
				}
			}
		}


		$objPessoa = new clsPessoa_( $this->cod_pessoa_fj, $this->nm_pessoa, false, $this->p_http, false, $pessoaFj, date( "Y-m-d H:i:s", time() ), $this->email );
		$objPessoa->edita();

		$this->data_nasc = dataToBanco($this->data_nasc);

		if($this->id_federal)
		{
			$this->id_federal = idFederal2Int($this->id_federal);
			$objFisica = new clsFisica( $this->cod_pessoa_fj, $this->data_nasc, $this->sexo,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,$ref_cod_sistema, $this->id_federal );
		}else 
		{
			$objFisica = new clsFisica( $this->cod_pessoa_fj, $this->data_nasc, $this->sexo,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,$ref_cod_sistema);
		}
		$objFisica->edita();

		if($this->alterado)
		{
			$db = new clsBanco();
			$db->Consulta("UPDATE cadastro.fisica SET alterado = 'TRUE' WHERE idpes = '$this->cod_pessoa_fj'");
		}
		
		
		$objTelefone = new clsPessoaTelefone( $this->cod_pessoa_fj, 1, $this->telefone_1, $this->ddd_telefone_1 );
		$objTelefone->cadastra();
		$objTelefone = new clsPessoaTelefone( $this->cod_pessoa_fj, 2, $this->telefone_2, $this->ddd_telefone_2 );
		$objTelefone->cadastra();
		$objTelefone = new clsPessoaTelefone( $this->cod_pessoa_fj, 3, $this->telefone_mov, $this->ddd_telefone_mov );
		$objTelefone->cadastra();
		$objTelefone = new clsPessoaTelefone( $this->cod_pessoa_fj, 4, $this->telefone_fax, $this->ddd_telefone_fax );
		$objTelefone->cadastra();



			$objEndereco = new clsPessoaEndereco( $this->cod_pessoa_fj );
			$this->cep = idFederal2Int($this->cep);
			$objEndereco2 = new clsPessoaEndereco($this->cod_pessoa_fj,$this->cep,$this->idlog,$this->idbai,$this->numero,$this->complemento,false,$this->letra, $this->bloco, $this->apartamento,$this->andar);

			if( $objEndereco->detalhe() && $this->cep && $this->idlog && $this->idbai)
			{
				$objEndereco2->edita();
			}
			elseif($this->cep && $this->idlog && $this->idbai)
			{
				$objEndereco2->cadastra();
			}
			elseif($objEndereco->detalhe())
			{
				$objEndereco2->exclui();
			}
			else
			{
				$this->cep_ = idFederal2int($this->cep_);
				$objEnderecoExterno = new clsEnderecoExterno($this->cod_pessoa_fj );

				$objEnderecoExterno2 = new clsEnderecoExterno($this->cod_pessoa_fj,"1",$this->idtlog,$this->logradouro,$this->numero,$this->letra,$this->complemento,$this->bairro,$this->cep_,$this->cidade,$this->sigla_uf,false,$this->bloco,$this->apartamento,$this->andar);
				if( $objEnderecoExterno->detalhe() )
				{
					$objEnderecoExterno2->edita();
				}
				else
				{
					$objEnderecoExterno2->cadastra();
				}
			}
		echo "<script>document.location='atendidos_lst.php';</script>";

		return true;
	}

	function Excluir()
	{
		echo "<script>document.location='atendidos_lst.php';</script>";
		return true;
	}

}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
