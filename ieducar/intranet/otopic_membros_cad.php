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
require_once ("include/otopic/otopicGeral.inc.php");

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Pauta - Atualiza Pessoa!" );
		$this->processoAp = "294";
	}
}

class indice extends clsCadastro
{
	var $cod_pessoa_fj, $nm_pessoa, $id_federal, $endereco, $cep, $ref_bairro, $ddd_telefone_1, $telefone_1, $ddd_telefone_2, $telefone_2, $ddd_telefone_mov, $telefone_mov, $ddd_telefone_fax, $telefone_fax, $email, $http, $tipo_pessoa, $sexo, $razao_social, $ins_est, $ins_mun;
	var $busca_pessoa;
	var $complemento;
	var $apartamento;
	var $bloco;
	var $andar;
	var $numero;
	var $retorno;
	var $cod_grupo;

	function Inicializar()
	{
		@session_start();
		$pessoaFj = $_SESSION['id_pessoa'];
		session_write_close();

		$this->cod_grupo = $_GET['cod_grupo'];
		$busca_por_cpf = false;


		// Verifica se o usuario é um moderador caso nao seja, redireciona para pagina de onde veio
		$obj_moderador = new clsGrupoModerador($pessoaFj,$this->cod_grupo);
		$detalhe_moderador = $obj_moderador->detalhe();
		if(!$detalhe_moderador || $detalhe_moderador['ativo']!=1)
		{
			header("Location: otopic_meus_grupos_det.php?cod_grupo=$this->cod_grupo");
		}

		if( $_REQUEST['busca_pessoa'])
		{
			$busca_por_cpf = true;
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
			if($this->cod_pessoa_fj == $pessoaFj || !$this->cod_grupo)
			{
				header("Location: otopic_meus_grupos_det.php?cod_grupo=$this->cod_grupo");
			}
			$this->cod_pessoa_fj = (@$_GET['cod_pessoa']) ? @$_GET['cod_pessoa'] : $this->cod_pessoa_fj;
			$db = new clsBanco();
			$objPessoa = new clsPessoaFisica();
			list( $this->nm_pessoa,$this->id_federal, $this->ddd_telefone_1, $this->telefone_1, $this->ddd_telefone_2, $this->telefone_2, $this->ddd_telefone_mov, $this->telefone_mov, $this->ddd_telefone_fax, $this->telefone_fax, $this->email, $this->http, $this->tipo_pessoa, $this->sexo, $this->cidade, $this->bairro, $this->logradouro, $this->cep, $this->idlog, $this->idbai, $this->idtlog, $this->sigla_uf, $this->complemento, $this->numero, $this->bloco, $this->apartamento, $this->andar) = $objPessoa->queryRapida( $this->cod_pessoa_fj, "nome", "cpf", "ddd_1", "fone_1", "ddd_2", "fone_2", "ddd_mov", "fone_mov", "ddd_fax", "fone_fax", "email", "url", "tipo", "sexo", "cidade", "bairro", "logradouro", "cep", "idlog", "idbai", "idtlog", "sigla_uf", "complemento", "numero", "bloco", "apartamento", "andar");
			$this->cep = int2Cep($this->cep);
			$this->fexcluir = true;
			$this->retorno = "Editar";
		}

		return $this->retorno;
	}

	function Gerar()
	{
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet",false );

		if ( !$this->busca_pessoa )
		{
			$parametros = new clsParametrosPesquisas();
			$parametros->setSubmit( 1 );
			$parametros->setPessoa( 'F' );
			$parametros->setPessoaCampo( "cod_pessoa_fj" );
			$parametros->setPessoaNovo( 'S' );
			$parametros->setPessoaTela( "window" );
			$parametros->setCodSistema(8);
			$parametros->adicionaCampoTexto( "busca_pessoa", "cpf" );
			$this->campoCpf( "busca_pessoa", "CPF", $this->busca_pessoa, true, "<img src=\"imagens/lupa.png\" border=\"0\" onclick=\"pesquisa_valores_popless( 'pesquisa_pessoa_lst.php?campos=".$parametros->serializaCampos()."', 'busca_pessoa' )\">" );
			$this->campoOculto( "cod_grupo", $this->cod_grupo );
			$this->campoOculto( "cod_pessoa_fj", "" );

		}
		else
		{
			$this->nome_url_cancelar = "Cancelar";
			$this->url_cancelar = "otopic_meus_grupos_det.php?cod_grupo=$this->cod_grupo";
			$this->campoOculto("cod_grupo",$this->cod_grupo);
			$this->campoOculto( "cod_pessoa_fj", $this->cod_pessoa_fj );
			$this->campoTexto( "nm_pessoa", "Nome",  $this->nm_pessoa, "50", "255", true );
			if($this->id_federal)
			{
				$this->campoCpf( "id_federal", "CPF", int2CPF( $this->id_federal ), "50", "", true );
			}
			$lista_sexos = array();
			$lista_sexos[""] = "Escolha uma op&ccedil;&atilde;o...";
			$lista_sexos["M"] = "Masculino";
			$lista_sexos["F"] = "Feminino";
			$this->campoLista( "sexo", "Sexo", $lista_sexos, $this->sexo);

			// Detalhes do Endereço
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


			if($this->idlog && $this->idbai && $this->cep && $this->cod_pessoa_fj)
			{
				$this->campoCep("cep_","CEP",$this->cep,false,"-","&nbsp;<img src=\"imagens/lupa.png\" border=\"0\" onclick=\"pesquisa_valores_f('pesquisa_cep.php', 'enderecos')\" style=\"cursor: hand;\">",true);
				$this->campoLista("idtlog","Tipo Logradouro",$listaTLog,$this->idtlog,false,false,false,false,true);
				$this->campoTextoInv( "logradouro", "Logradouro",  $this->logradouro, "50", "255", false );
				$this->campoTextoInv( "cidade", "Cidade",  $this->cidade, "50", "255", false );
				$this->campoTextoInv( "bairro", "Bairro",  $this->bairro, "50", "255", false );
				$this->campoTexto( "complemento", "Complemento",  $this->complemento, "50", "255", false );
				$this->campoTexto( "numero", "Número",  $this->numero, "10", "10", false );
				$this->campoTexto( "letra", "Letra",  $this->letra, "1", "1", false );
				$this->campoTexto( "apartamento", "Número Apartametno",  $this->apartamento, "6", "6", false );
				$this->campoTexto( "bloco", "Bloco",  $this->bloco, "20", "20", false );
				$this->campoTexto( "andar", "Andar",  $this->andar, "2", "2", false );
				$this->campoLista("sigla_uf","Estado",$listaEstado,$this->sigla_uf,false,false,false,false,true);
			}
			elseif($this->cod_pessoa_fj)
			{
				$this->campoCep("cep_","CEP",$this->cep,false,"-","&nbsp;<img src=\"imagens/lupa.png\" border=\"0\" onclick=\"pesquisa_valores_f('pesquisa_cep.php', 'enderecos')\" style=\"cursor: hand;\">",false);
				$this->campoLista("idtlog","Tipo Logradouro",$listaTLog,$this->idtlog);
				$this->campoTexto( "logradouro", "Logradouro",  $this->logradouro, "50", "255", false );
				$this->campoTexto( "cidade", "Cidade",  $this->cidade, "50", "255", false );
				$this->campoTexto( "bairro", "Bairro",  $this->bairro, "50", "255", false );
				$this->campoTexto( "complemento", "Complemento",  $this->complemento, "50", "255", false );
				$this->campoTexto( "numero", "Número",  $this->numero, "10", "10", false );
				$this->campoTexto( "letra", "Letra",  $this->letra, "1", "1", false );
				$this->campoTexto( "apartamento", "Número Apartametno",  $this->apartamento, "6", "6", false );
				$this->campoTexto( "bloco", "Bloco",  $this->bloco, "20", "20", false );
				$this->campoTexto( "andar", "Andar",  $this->andar, "2", "2", false );
				$this->campoLista("sigla_uf","Estado",$listaEstado,$this->sigla_uf);
			}
			else
			{
				$this->campoCep("cep_","CEP",$this->cep,false,"-","&nbsp;<img src=\"imagens/lupa.png\" border=\"0\" onclick=\"pesquisa_valores_f('pesquisa_cep.php', 'enderecos')\" style=\"cursor: hand;\">",true);
				$this->campoLista("idtlog","Tipo Logradouro",$listaTLog,$this->idtlog,false,false,false,false,true);
				$this->campoTextoInv( "logradouro", "Logradouro",  $this->logradouro, "50", "255",true );
				$this->campoTextoInv( "cidade", "Cidade",  $this->cidade, "50", "255", true );
				$this->campoTextoInv( "bairro", "Bairro",  $this->bairro, "50", "255", true );
				$this->campoTextoInv( "complemento", "Complemento",  $this->complemento, "50", "255", false );
				$this->campoTextoInv( "numero", "Número",  $this->numero, "10", "10", false );
				$this->campoTextoInv( "letra", "Letra",  $this->letra, "1", "1", false );
				$this->campoTexto( "apartamento", "Número Apartametno",  $this->apartamento, "6", "6", false );
				$this->campoTexto( "bloco", "Bloco",  $this->bloco, "20", "20", false );
				$this->campoTexto( "andar", "Andar",  $this->andar, "2", "2", false );
				$this->campoLista("sigla_uf","Estado",$listaEstado,$this->sigla_uf,false,false,false,false,true);
			}

			$this->campoTexto( "ddd_telefone_1", "DDD Telefone 1",  $this->ddd_telefone_1, "3", "2", false );
			$this->campoTexto( "telefone_1", "Telefone 1",  $this->telefone_1, "10", "15", false );
			$this->campoTexto( "ddd_telefone_2", "DDD Telefone 2",  $this->ddd_telefone_2, "3", "2", false );
			$this->campoTexto( "telefone_2", "Telefone",  $this->telefone_2, "10", "15", false );
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

		$this->id_federal = idFederal2int( $this->id_federal );
		$objCPF = new clsFisica( false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, $this->id_federal );
		if( $objCPF->detalhe() )
		{
			$this->erros['id_federal'] = "CPF j&aacute; cadastrado.";
			return false;
		}

		$objPessoa = new clsPessoa_( false, $this->nm_pessoa, $pessoaFj, $this->http, 'F', false, false, $this->email );
		$idpes = $objPessoa->cadastra();

		$objFisica = new clsFisica( $idpes, false, $this->sexo, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, $this->cpf );
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
		else
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

		
		$obj = new clsGrupoPessoa($idpes, $this->cod_grupo,$pessoaFj, false, $this->cod_grupo);
		if($obj->cadastra())
		{
			header("Location: otopic_meus_grupos_det.php?cod_grupo=$this->cod_grupo");
		}

		return false;
	}

	function Editar()
	{
		@session_start();
		$pessoaFj = $_SESSION['id_pessoa'];
		session_write_close();

				
		$obj = new clsGrupoModerador($this->cod_pessoa_fj,$this->cod_grupo);
		$detalhe =$obj->detalhe();
		if($detalhe && $detalhe['ativo'] == 1)
		{
			header("Location: otopic_meus_grupos_det.php?cod_grupo=$this->cod_grupo");
		}
		
		
		$objPessoa = new clsPessoa_( $this->cod_pessoa_fj, $this->p_nm_pessoa, false, $this->http, false, $pessoaFj, date( "Y-m-d H:i:s", time() ), $this->email );
		$objPessoa->edita();

		$objFisica = new clsFisica( $this->cod_pessoa_fj, false, $this->sexo );
		$objFisica->edita();

		$objTelefone = new clsPessoaTelefone( $this->cod_pessoa_fj, 1, $this->telefone_1, $this->ddd_telefone_1 );
		$objTelefone->cadastra();
		$objTelefone = new clsPessoaTelefone( $this->cod_pessoa_fj, 2, $this->telefone_2, $this->ddd_telefone_2 );
		$objTelefone->cadastra();
		$objTelefone = new clsPessoaTelefone( $this->cod_pessoa_fj, 3, $this->telefone_mov, $this->ddd_telefone_mov );
		$objTelefone->cadastra();
		$objTelefone = new clsPessoaTelefone( $this->cod_pessoa_fj, 4, $this->telefone_fax, $this->ddd_telefone_fax );
		$objTelefone->cadastra();

		if($this->cep && $this->idbai && $this->idlog)
		{

			$objEndereco = new clsPessoaEndereco( $this->cod_pessoa_fj );



			$objEndereco2 = new clsPessoaEndereco($this->cod_pessoa_fj,$this->cep,$this->idlog,$this->idbai,$this->numero,$this->complemento,false,$this->letra, $this->bloco, $this->apartamento,$this->andar);
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
		
		$obj = new clsGrupoPessoa($this->cod_pessoa_fj, $this->cod_grupo);
		if(!$obj->detalhe())
		{
			$obj = new clsGrupoPessoa($this->cod_pessoa_fj, $this->cod_grupo,$pessoaFj, false, $this->cod_grupo);
			if($obj->cadastra())
			{
				header("Location: otopic_meus_grupos_det.php?cod_grupo=$this->cod_grupo");
			}
		}else
		{
			$obj = new clsGrupoPessoa($this->cod_pessoa_fj, $this->cod_grupo,$pessoaFj, false, $this->cod_grupo,false,1);
			if($obj->edita())
			{
				header("Location: otopic_meus_grupos_det.php?cod_grupo=$this->cod_grupo");
			}
		}

		return true;
	}

	function Excluir()
	{
		@session_start();
		$pessoaFj = $_SESSION['id_pessoa'];
		session_write_close();

		$obj = new clsGrupoPessoa($this->cod_pessoa_fj, $this->cod_grupo,false,$pessoaFj, false,$this->cod_grupo,1);
		if($obj->exclui())
		{
			header("Location: otopic_meus_grupos_det.php?cod_grupo=$this->cod_grupo");
		}
		return false;
	}

}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
