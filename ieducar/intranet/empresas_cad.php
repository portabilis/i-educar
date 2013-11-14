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
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Empresas!" );
		$this->processoAp = array("41", "649");
	}
}

class indice extends clsCadastro
{

	// Dados do Juridico
	var $cod_pessoa_fj,
		$razao_social,
		$cnpj,
		$fantasia,
		$capital_social,
		$insc_est;

	// Dados da Pessoa
	var	$email,
		$tipo_pessoa,
		$idpes_cad,
		$url;


	//Telefones
	var $ddd_telefone_1,
		$telefone_1,
		$ddd_telefone_2,
		$telefone_2,
		$ddd_telefone_mov,
		$telefone_mov,
		$ddd_telefone_fax,
		$telefone_fax;

	// Variáveis relativas ao endereco
	var  $bairro,
		 $idbai,
		 $cidade,
		 $logradouro,
		 $idlog,
		 $idtlog,
		 $cep,
		 $cep_,
		 $sigla_uf,
		 $complemento,
		 $letra,
		 $numero;

	// Variaveis de Controle
	var $busca_empresa,
		$retorno;

	function Inicializar()
	{
		$this->busca_empresa = $_POST['busca_empresa'];
		$this->cod_pessoa_fj = $_GET['idpes'];
		$this->idpes_cad = $_SESSION['id_pessoa'];

		if($this->busca_empresa)
		{
			$this->cnpj = $this->busca_empresa;
			$this->busca_empresa = idFederal2int($this->busca_empresa);
			$this->retorno = "Novo";
			$objPessoa = new clsPessoaJuridica();
			list($this->cod_pessoa_fj) = $objPessoa->queryRapidaCNPJ($this->busca_empresa, "idpes");
		}

		if ($this->cod_pessoa_fj)
		{
			$this->busca_empresa = true;
			$objPessoaJuridica = new clsPessoaJuridica($this->cod_pessoa_fj);
			$detalhePessoaJuridica = $objPessoaJuridica->detalhe();
			//echo "<pre>";
			//print_r($detalhePessoaJuridica);
			//die();
			$this->email = $detalhePessoaJuridica['email'];
			$this->url = $detalhePessoaJuridica['url'];
			$this->insc_est = $detalhePessoaJuridica['insc_estadual'];
			$this->capital_social = $detalhePessoaJuridica['capital_social'];
			$this->razao_social = $detalhePessoaJuridica['nome'];
			$this->fantasia = $detalhePessoaJuridica['fantasia'];
			$this->cnpj = int2CNPJ($detalhePessoaJuridica['cnpj']);
			$this->ddd_telefone_1 = $detalhePessoaJuridica['ddd_1'];
			$this->telefone_1 = $detalhePessoaJuridica['fone_1'];
			$this->ddd_telefone_2 = $detalhePessoaJuridica['ddd_2'];
			$this->telefone_2 = $detalhePessoaJuridica['fone_2'];
			$this->ddd_telefone_mov = $detalhePessoaJuridica['ddd_mov'];
			$this->telefone_mov = $detalhePessoaJuridica['fone_mov'];
			$this->ddd_telefone_fax = $detalhePessoaJuridica['ddd_fax'];
			$this->telefone_fax = $detalhePessoaJuridica['fone_fax'];
			$this->cidade = $detalhePessoaJuridica['cidade'];
			$this->bairro = $detalhePessoaJuridica['bairro'];
			$this->logradouro = $detalhePessoaJuridica['logradouro'];
			$this->cep = int2CEP($detalhePessoaJuridica['cep']);
			$this->idlog = $detalhePessoaJuridica['idlog'];
			$this->idbai = $detalhePessoaJuridica['idbai'];
			$this->idtlog = $detalhePessoaJuridica['idtlog'];
			$this->sigla_uf = $detalhePessoaJuridica['sigla_uf'];

			$this->complemento = $detalhePessoaJuridica['complemento'];
			$this->numero = $detalhePessoaJuridica['numero'];
			$this->letra = $detalhePessoaJuridica['letra'];


			
			$this->retorno = "Editar";
		}

		$this->nome_url_cancelar = "Cancelar";

		return $this->retorno;
	}

	function Gerar()
	{
		if(!$this->busca_empresa)
		{
			$this->campoCnpj("busca_empresa","CNPJ",$this->busca_empresa,true);
		}
		else
		{
			$this->url_cancelar = ($this->retorno == "Editar") ? "empresas_det.php?cod_empresa={$this->cod_pessoa_fj}" : "empresas_lst.php";

			$this->campoOculto( "cod_pessoa_fj", $this->cod_pessoa_fj );
			$this->campoOculto( "idpes_cad", $this->idpes_cad );

			// Dados da Empresa
			$this->campoTexto( "fantasia", "Nome Fantasia",  $this->fantasia, "50", "255", true );
			$this->campoTexto( "razao_social", "Raz&atilde;o Social",  $this->razao_social, "50", "255", true );
			$this->campoTexto( "capital_social", "Capital Social",  $this->capital_social, "50", "255" );
			
			if($this->cnpj)
			{
				$this->campoRotulo("cnpj_","CNPJ", $this->cnpj);	
				$this->campoOculto("cnpj", $this->cnpj);
			}else 
			{
				$this->campoCnpj( "cnpj", "CNPJ",  $this->cnpj, true );	
			}
		
			


			// Detalhes do Endereço da empresa
			$objTipoLog = new clsTipoLogradouro();
			$listaTipoLog = $objTipoLog->lista();
			$lista = array(""=>"Selecione");
			if($lista)
			{
				foreach ($listaTipoLog as $tipoLog) {
					$lista[$tipoLog['idtlog']] = $tipoLog['descricao'];
				}
			}

			$objUf = new clsUf();
			$listauf = $objUf->lista();
			$listaEstado = array(""=>"Selecione");
			if($listauf)
			{
				foreach ($listauf as $uf) {
					$listaEstado[$uf['sigla_uf']] = $uf['sigla_uf'];
				}
			}

			$this->campoOculto( "idbai", $this->idbai );
			$this->campoOculto( "idlog", $this->idlog );
			$this->campoOculto( "cep", $this->cep );


			if($this->idlog  && $this->idbai && $this->cep && $this->cod_pessoa_fj)
			{
				$this->campoCep("cep_", "CEP", $this->cep, true, "-", "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro.php?campo1=bairro&campo2=idbai&campo3=cep&campo4=logradouro&campo5=idlog&campo6=sigla_uf&campo7=cidade&campo8=idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=sigla_uf&campo12=idtlog&campo13=id_cidade\'></iframe>');\">", true);
				$this->campoLista("idtlog","Tipo Logradouro",$lista,$this->idtlog,false,false,false,false,true);
				$this->campoTextoInv( "logradouro", "Logradouro",  $this->logradouro, "50", "255", false );
				$this->campoTextoInv( "cidade", "Cidade",  $this->cidade, "50", "255", false );
				$this->campoTextoInv( "bairro", "Bairro",  $this->bairro, "50", "255", false );
				$this->campoTexto( "complemento", "Complemento",  $this->complemento, "50", "255", false );
				$this->campoTexto( "numero", "Número",  $this->numero, "10", "10", false );
				$this->campoTexto( "letra", "Letra",  $this->letra, "1", "1", false );
				$this->campoLista("sigla_uf","Estado",$listaEstado,$this->sigla_uf,false,false,false,false,true);
			}
			elseif($this->cod_pessoa_fj)
			{
				$this->campoCep("cep_", "CEP", $this->cep, true, "-", "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro.php?campo1=bairro&campo2=idbai&campo3=cep&campo4=logradouro&campo5=idlog&campo6=sigla_uf&campo7=cidade&campo8=idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=sigla_uf&campo12=idtlog&campo13=id_cidade\'></iframe>');\">", false);
				$this->campoLista("idtlog","Tipo Logradouro",$lista,$this->idtlog);
				$this->campoTexto( "logradouro", "Logradouro",  $this->logradouro, "50", "255", false );
				$this->campoTexto( "cidade", "Cidade",  $this->cidade, "50", "255", false );
				$this->campoTexto( "bairro", "Bairro",  $this->bairro, "50", "255", false );
				$this->campoTexto( "complemento", "Complemento",  $this->complemento, "50", "255", false );
				$this->campoTexto( "numero", "Número",  $this->numero, "10", "10", false );
				$this->campoTexto( "letra", "Letra",  $this->letra, "1", "1", false );
				$this->campoLista("sigla_uf","Estado",$listaEstado,$this->sigla_uf);

			}
			else
			{
				$this->campoCep("cep_", "CEP", $this->cep, true, "-", "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro.php?campo1=bairro&campo2=idbai&campo3=cep&campo4=logradouro&campo5=idlog&campo6=sigla_uf&campo7=cidade&campo8=idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=sigla_uf&campo12=idtlog&campo13=id_cidade\'></iframe>');\">", true);
				$this->campoLista("idtlog","Tipo Logradouro",$lista,$this->idtlog,false,false,false,false,true);
				$this->campoTextoInv( "logradouro", "Logradouro",  $this->logradouro, "50", "255",true );
				$this->campoTextoInv( "cidade", "Cidade",  $this->cidade, "50", "255", true );
				$this->campoTextoInv( "bairro", "Bairro",  $this->bairro, "50", "255", true );
				$this->campoTexto( "complemento", "Complemento",  $this->complemento, "50", "255", false );
				$this->campoTexto( "numero", "Número",  $this->numero, "10", "10", false );
				$this->campoTexto( "letra", "Letra",  $this->letra, "1", "1", false );
				$this->campoLista("sigla_uf","Estado",$listaEstado,$this->sigla_uf,false,false,false,false,true);
			}



			// Telefones


		    $this->inputTelefone('1', 'Telefone 1');
            $this->inputTelefone('2', 'Telefone 2');
            $this->inputTelefone('mov', 'Celular');
            $this->inputTelefone('fax', 'Fax');

			// Dados da Empresa

			$this->campoTexto( "url", "Site",  $this->url, "50", "255", false );
			$this->campoTexto( "email", "E-mail",  $this->email, "50", "255", false );
			$this->campoTexto( "insc_est", "Inscri&ccedil;&atilde;o Estadual",  $this->insc_est, "20", "30", false );
		}

	}

	function Novo()
	{
 		$this->cnpj = idFederal2int( urldecode($this->cnpj ));
 		$objJuridica = new clsJuridica( false, $this->cnpj );
 		$detalhJuridica = $objJuridica->detalhe();
 		if( ! $detalhJuridica )
 		{

	 		$this->insc_est = idFederal2int($this->insc_est);

	 		$this->idpes_cad = $_SESSION["id_pessoa"];

	 		$objPessoa = new clsPessoa_( false, $this->razao_social, $this->idpes_cad, $this->url, "J", false, false, $this->email );
	 		$this->cod_pessoa_fj = $objPessoa->cadastra();


	 		$objJuridica = new clsJuridica( $this->cod_pessoa_fj, $this->cnpj, $this->fantasia, $this->insc_est, $this->capital_social );
			$objJuridica->cadastra();

	 		if( $this->telefone_1 )
			{
				$this->telefone_1 = str_replace( "-", "", $this->telefone_1 );
				if( is_numeric( $this->telefone_1 ) )
				{
					$objTelefone = new clsPessoaTelefone($this->cod_pessoa_fj,1,$this->telefone_1,$this->ddd_telefone_1);
					$objTelefone->cadastra();
				}
			}
			if( $this->telefone_2 )
			{
				$this->telefone_2 = str_replace( "-", "", $this->telefone_2 );
				if( is_numeric( $this->telefone_2 ) )
				{
					$objTelefone = new clsPessoaTelefone($this->cod_pessoa_fj,2,$this->telefone_2,$this->ddd_telefone_2);
					$objTelefone->cadastra();
				}
			}
			if( $this->telefone_mov )
			{
				$this->telefone_mov = str_replace( "-", "", $this->telefone_mov );
				if( is_numeric( $this->telefone_mov ) )
				{
					$objTelefone = new clsPessoaTelefone($this->cod_pessoa_fj,3,$this->telefone_mov,$this->ddd_telefone_mov);
					$objTelefone->cadastra();
				}
			}
			if( $this->telefone_fax )
			{
				$this->telefone_fax = str_replace( "-", "", $this->telefone_fax );
				if( is_numeric( $this->telefone_fax ) )
				{
					$objTelefone = new clsPessoaTelefone($this->cod_pessoa_fj,4,$this->telefone_fax,$this->ddd_telefone_fax);
					$objTelefone->cadastra();
				}
			}

			if($this->cep && $this->idbai && $this->idlog)
			{
				$this->cep = idFederal2Int($this->cep);
				$objEndereco = new clsPessoaEndereco($this->cod_pessoa_fj,$this->cep,$this->idlog,$this->idbai,$this->numero,$this->complemento,false,$this->letra);
				$objEndereco->cadastra();
			}
			else
			{
				$this->cep_ = idFederal2int($this->cep_);
				$objEnderecoExterno = new clsEnderecoExterno($this->cod_pessoa_fj,"1",$this->idtlog,$this->logradouro,$this->numero,$this->letra,$this->complemento,$this->bairro,$this->cep_,$this->cidade,$this->sigla_uf,false);
				$objEnderecoExterno->cadastra();
			}
			header("Location: empresas_lst.php");
			return true;
 		}
 		
		$this->mensagem = "Ja existe uma empresa cadastrada com este CNPJ. ";
		return false;
	}

	function Editar()
	{
 		$this->cnpj = idFederal2int($this->cnpj);
 		$this->insc_est = idFederal2int($this->insc_est);

 		$objPessoa = new clsPessoa_($this->cod_pessoa_fj,$this->razao_social,$this->idpes_cad,$this->url,"J",false,false,$this->email);
 		$objPessoa->edita();

 		$objJuridica = new clsJuridica($this->cod_pessoa_fj,$this->cnpj,$this->fantasia,$this->insc_est, $this->capital_social );
		$objJuridica->edita();

 		if($this->telefone_1)
		{
			$this->telefone_1 = str_replace( "-", "", $this->telefone_1 );
			$objTelefone = new clsPessoaTelefone($this->cod_pessoa_fj,1,$this->telefone_1,$this->ddd_telefone_1);
			if($objTelefone->detalhe())
			{
				$objTelefone->edita();
			}else
			{
				$objTelefone->cadastra();
			}
		}
		if($this->telefone_2)
		{
			$this->telefone_2 = str_replace( "-", "", $this->telefone_2 );
			$objTelefone = new clsPessoaTelefone($this->cod_pessoa_fj,2,$this->telefone_2,$this->ddd_telefone_2);
			if($objTelefone->detalhe())
			{
				$objTelefone->edita();
			}else
			{
				$objTelefone->cadastra();
			}
		}
		if($this->telefone_mov)
		{
			$this->telefone_mov = str_replace( "-", "", $this->telefone_mov );
			$objTelefone = new clsPessoaTelefone($this->cod_pessoa_fj,3,$this->telefone_mov,$this->ddd_telefone_mov);
			if($objTelefone->detalhe())
			{
				$objTelefone->edita();
			}else
			{
				$objTelefone->cadastra();
			}
		}
		if($this->telefone_fax)
		{
			$this->telefone_fax = str_replace( "-", "", $this->telefone_fax );
			$objTelefone = new clsPessoaTelefone($this->cod_pessoa_fj,4,$this->telefone_fax,$this->ddd_telefone_fax);
			if($objTelefone->detalhe())
			{
				$objTelefone->edita();
			}else
			{
				$objTelefone->cadastra();
			}
		}


		if($this->cep && $this->idbai && $this->idlog)
		{
			$this->cep = idFederal2Int($this->cep);

			$objEndereco = new clsPessoaEndereco($this->cod_pessoa_fj);
			$objEndereco2 = new clsPessoaEndereco($this->cod_pessoa_fj,$this->cep,$this->idlog,$this->idbai,$this->numero,$this->complemento,false,$this->letra);
			if( $objEndereco->detalhe() )
			{
				$objEndereco2->edita();
			}
			else
			{
				$objEndereco2->cadastra();
			}
		}
		else
		{
			$this->cep_ = idFederal2int($this->cep_);
			$objEnderecoExterno = new clsEnderecoExterno($this->cod_pessoa_fj);
			$objEnderecoExterno2 = new clsEnderecoExterno($this->cod_pessoa_fj,"1",$this->idtlog,$this->logradouro,$this->numero,$this->letra,$this->complemento,$this->bairro,$this->cep_,$this->cidade,$this->sigla_uf,false);
			if( $objEnderecoExterno->detalhe() )
			{
				$objEnderecoExterno2->edita();
			}
			else
			{
				$objEnderecoExterno2->cadastra();
			}
		}
		header("Location: empresas_lst.php");
		return true;
	}

	function Excluir()
	{
		header("LOCATION: empresas_lst.php");
		return true;
	}

  	protected function inputTelefone($type, $typeLabel = '') {
	    if (! $typeLabel)
	      $typeLabel = "Telefone {$type}";	 
	    // ddd	 
	    $options = array(
	      'required'    => false,
	      'label'       => "(ddd) / {$typeLabel}",
	      'placeholder' => 'ddd',
	      'value'       => $this->{"ddd_telefone_{$type}"},
	      'max_length'  => 3,
	      'size'        => 3,
	      'inline'      => true
	    );	 
	    $this->inputsHelper()->integer("ddd_telefone_{$type}", $options);	 	 
	   // telefone	 
	    $options = array(
	      'required'    => false,
	      'label'       => '',
	      'placeholder' => $typeLabel,
	      'value'       => $this->{"telefone_{$type}"},
	      'max_length'  => 11
	    );	 
	    $this->inputsHelper()->integer("telefone_{$type}", $options);
   }
 
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>