<?php


header( 'Content-type: text/xml' );

require_once( "include/clsBanco.inc.php" );
require_once( "include/funcoes.inc.php" );
require_once( "include/pmieducar/geral.inc.php" );
if ($_GET['cpf'] || $_GET['idpes'])
{
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?>\n<query xmlns=\"sugestoes\">\n";
	$xml .= "<dados>\n";

	$cpf = $_GET['cpf'];

	if ($_GET['idpes'])
	{
		$ref_idpes = $_GET['idpes'];
	}
	else 
	{
		$cpf = idFederal2int($_GET['cpf']);
		$obj_pessoa_fisica = new clsPessoaFisica(null, $cpf);
		$lst_pessoa_fisica = $obj_pessoa_fisica->lista(null, $cpf);
		
		if (!$lst_pessoa_fisica)
		{
			echo $xml."</dados>\n</query>";
			die();
		}
		
		$ref_idpes = $lst_pessoa_fisica[0]['idpes'];
		
		$xml .= "<ref_idpes>{$ref_idpes}</ref_idpes>\n";
		$xml .= "<cpf>{$cpf}</cpf>\n";
		
	}
	
	if( $cod_aluno)
	{
		$obj_matricula = new clsPmieducarMatricula();
		$lst_matricula = $obj_matricula->lista( null, null, null, null, null, null, $cod_aluno );
	}
	if(!empty($ref_idpes))
	{
		$obj_aluno = new clsPmieducarAluno();
		$lista_aluno = $obj_aluno->lista(null,null,null,null,null,$ref_idpes,null,null,null,null);
		if($lista_aluno)
		{
			$det_aluno = array_shift($lista_aluno);
		}
	}
	if($det_aluno['cod_aluno'] )
	{
		$cod_aluno = $det_aluno['cod_aluno'];
		$ref_cod_aluno_beneficio = $det_aluno['ref_cod_aluno_beneficio'];
		$ref_cod_religiao = $det_aluno['ref_cod_religiao'];
		$caminho_foto = $det_aluno['caminho_foto'];
	}
	
	$xml .= "<cod_aluno>{$cod_aluno}</cod_aluno>\n";
	$xml .= "<ref_cod_aluno_beneficio>{$ref_cod_aluno_beneficio}</ref_cod_aluno_beneficio>\n";
	$xml .= "<ref_cod_religiao>{$ref_cod_religiao}</ref_cod_religiao>\n";
	$xml .= "<caminho_foto>{$caminho_foto}</caminho_foto>\n";
	$xml .= "<idpes>{$ref_idpes}</idpes>\n";
		
	if($ref_idpes != "NULL")
	{
		if( $ref_idpes)
		{
			$obj_pessoa = new clsPessoaFj($ref_idpes);
			$det_pessoa = $obj_pessoa->detalhe();
		
			$obj_fisica = new clsFisica($ref_idpes);
			$det_fisica = $obj_fisica->detalhe();
		
			$obj_fisica_raca = new clsCadastroFisicaRaca( $ref_idpes );
			$det_fisica_raca = $obj_fisica_raca->detalhe();
			$ref_cod_raca = $det_fisica_raca['ref_cod_raca'];
		
			$nome  = $det_pessoa["nome"];
		
			$email =  $det_pessoa["email"];
		
		 	$ideciv = $det_fisica["ideciv"]->ideciv;
		
			$data_nascimento = dataToBrasil($det_fisica["data_nasc"]);
		
			$cpf = $det_fisica["cpf"];
		
			$xml .= "<ref_cod_raca>{$ref_cod_raca}</ref_cod_raca>\n";
			$xml .= "<nome>{$nome}</nome>\n";
			$xml .= "<email>{$email}</email>\n";
			$xml .= "<ideciv>{$ideciv}</ideciv>\n";
			$xml .= "<data_nascimento>{$data_nascimento}</data_nascimento>\n";
			$xml .= "<cpf>{$cpf}</cpf>\n";
			$cpf2 = int2CPF($cpf);
			$xml .= "<cpf_2>{$cpf2}</cpf_2>\n";
			
			$obj_documento = new clsDocumento($ref_idpes);
			$obj_documento_det = $obj_documento->detalhe();
		
			$ddd_fone_1 = $det_pessoa["ddd_1"];
			$fone_1 = $det_pessoa["fone_1"];
		
			$ddd_mov = $det_pessoa["ddd_mov"];
			$fone_mov = $det_pessoa["fone_mov"];
		
			$email = 	$det_pessoa["email"];
			$url = 	$det_pessoa["url"];
		
			$sexo = $det_fisica["sexo"];
		
			$nacionalidade = $det_fisica["nacionalidade"];
			$idmun_nascimento = $det_fisica["idmun_nascimento"]->idmun;
			
			$xml .= "<ddd_fone_1>{$ddd_fone_1}</ddd_fone_1>\n";
			$xml .= "<fone_1>{$fone_1}</fone_1>\n";
			$xml .= "<ddd_mov>{$ddd_mov}</ddd_mov>\n";				
			$xml .= "<fone_mov>{$fone_mov}</fone_mov>\n";				
			$xml .= "<email>{$email}</email>\n";				
			$xml .= "<url>{$url}</url>\n";				
			$xml .= "<sexo>{$sexo}</sexo>\n";				
			$xml .= "<nacionalidade>{$nacionalidade}</nacionalidade>\n";				
			$xml .= "<idmun_nascimento>{$idmun_nascimento}</idmun_nascimento>\n";		
			
			$detalhe_pais_origem  = $det_fisica["idpais_estrangeiro"]->detalhe();
		 	$pais_origem = $detalhe_pais_origem["idpais"];
		
			$ref_idpes_responsavel = $det_fisica["idpes_responsavel"];
			$idpes_pai = $det_fisica["idpes_pai"];
			$idpes_mae = $det_fisica["idpes_mae"];
			
			$xml .= "<idpes_pai>{$idpes_pai}</idpes_pai>\n";
			$xml .= "<idpes_mae>{$idpes_mae}</idpes_mae>\n";
		
			$obj_aluno = new clsPmieducarAluno(null,null,null,null,null,$ref_idpes );
			$detalhe_aluno = $obj_aluno->detalhe();
			if( $detalhe_aluno )
			{
				$nm_pai = $detalhe_aluno["nm_pai"];
				$nm_mae = $detalhe_aluno["nm_mae"];
				
				$xml .= "<nm_pai>{$nm_pai}</nm_pai>\n";
				$xml .= "<nm_mae>{$nm_mae}</nm_mae>\n";
			}
		
			$obj_endereco = new clsPessoaEndereco($ref_idpes);
		
			if($obj_endereco_det = $obj_endereco->detalhe())
			{
		
				$isEnderecoExterno = 0;
		
				$id_cep       	= $obj_endereco_det['cep']->cep;
				$id_bairro    	= $obj_endereco_det['idbai']->idbai;
				$id_logradouro	= $obj_endereco_det['idlog']->idlog;
				$numero    		= $obj_endereco_det['numero'];
				$letra    		= $obj_endereco_det['letra'];
				$complemento  	= $obj_endereco_det['complemento'];
				$andar    		= $obj_endereco_det['andar'];
				$apartamento  	= $obj_endereco_det['apartamento'];
				$bloco	    	= $obj_endereco_det['bloco'];
		
				$ref_idtlog	    = $obj_endereco_det['idtlog'];
		
				$nm_bairro =  $obj_endereco_det['bairro'];
				$nm_logradouro =  $obj_endereco_det['logradouro'];
		
				$cep_ = int2CEP($id_cep);
		
				$xml .= "<id_cep>{$id_cep}</id_cep>\n";
				$xml .= "<id_bairro>{$id_bairro}</id_bairro>\n";
				$xml .= "<id_logradouro>{$id_logradouro}</id_logradouro>\n";
				$xml .= "<numero>{$numero}</numero>\n";
				$xml .= "<letra>{$letra}</letra>\n";
				$xml .= "<complemento>{$complemento}</complemento>\n";
				$xml .= "<andar>{$andar}</andar>\n";
				$xml .= "<apartamento>{$apartamento}</apartamento>\n";
				$xml .= "<bloco>{$bloco}</bloco>\n";;
				$xml .= "<ref_idtlog>{$ref_idtlog}</ref_idtlog>\n";;
				$xml .= "<nm_bairro>{$nm_bairro}</nm_bairro>\n";
				$xml .= "<nm_logradouro>{$nm_logradouro}</nm_logradouro>\n";
				
		
			}
			else
			{
		
				$obj_endereco = new clsEnderecoExterno($ref_idpes);
		
				if($obj_endereco_det = $obj_endereco->detalhe())
				{
					$isEnderecoExterno = 1;
		
					$id_cep         = $obj_endereco_det['cep'];
					$cidade =  $obj_endereco_det['cidade'];
					$nm_bairro =  $obj_endereco_det['bairro'];
					$nm_logradouro =  $obj_endereco_det['logradouro'];
		
					$id_bairro    = null;
					$id_logradouro    = null;
					$numero    	= $obj_endereco_det['numero'];
					$letra    	= $obj_endereco_det['letra'];
					$complemento  = $obj_endereco_det['complemento'];
					$andar    	= $obj_endereco_det['andar'];
					$apartamento  = $obj_endereco_det['apartamento'];
					$bloco	    = $obj_endereco_det['bloco'];
		
					$ref_idtlog = $idtlog	    = $obj_endereco_det['idtlog']->idtlog;
			 		$ref_sigla_uf = $ref_sigla_uf_ =  $obj_endereco_det['sigla_uf']->sigla_uf;
					$cep_ = int2CEP($id_cep);
					
					$xml .= "<id_cep>{$id_cep}</id_cep>\n";
					$xml .= "<cidade>{$cidade}</cidade>\n";
					$xml .= "<nm_bairro>{$nm_bairro}</nm_bairro>\n";
					$xml .= "<nm_logradouro>{$nm_logradouro}</nm_logradouro>\n";
					$xml .= "<numero>{$numero}</numero>\n";
					$xml .= "<letra>{$letra}</letra>\n";
					$xml .= "<complemento>{$complemento}</complemento>\n";
					$xml .= "<andar>{$andar}</andar>\n";
					$xml .= "<apartamento>{$apartamento}</apartamento>\n";
					$xml .= "<bloco>{$bloco}</bloco>\n";
					$xml .= "<ref_idtlog>{$ref_idtlog}</ref_idtlog>\n";
					$xml .= "<idtlog>{$idtlog}</idtlog>\n";
					$xml .= "<ref_sigla_uf>{$ref_sigla_uf}</ref_sigla_uf>\n";
					$xml .= "<ref_sigla_uf_>{$ref_sigla_uf_}</ref_sigla_uf_>\n";
					$xml .= "<cep_>{$cep_}</cep_>\n";
					
				}
			}
		}
	}
	
	if($isEnderecoExterno == 0)
	{
		$obj_bairro = new clsBairro($id_bairro);
		$cep_ = int2CEP($id_cep);
	
		$xml .= "<cep_>{$cep_}</cep_>\n";
		
		$obj_bairro_det = $obj_bairro->detalhe();
	
		if($obj_bairro_det)
		{
	
			$nm_bairro = $obj_bairro_det["nome"];
			
			$xml .= "<nm_bairro>{$nm_bairro}</nm_bairro>\n";
			
		}
	
		$obj_log = new clsLogradouro($id_logradouro);
		$obj_log_det = $obj_log->detalhe();
	
		if($obj_log_det)
		{
	
			$nm_logradouro = $obj_log_det["nome"];
	
			$ref_idtlog = $obj_log_det["idtlog"]->idtlog;
			$xml .= "<nm_logradouro>{$nm_logradouro}</nm_logradouro>\n";
			$xml .= "<ref_idtlog>{$ref_idtlog}</ref_idtlog>\n";
			
			$obj_mun = new clsMunicipio( $obj_log_det["idmun"]);
			$det_mun = $obj_mun->detalhe();
	
			if($det_mun)
			{
				$cidade = ucfirst(strtolower($det_mun["nome"]));
				
				$xml .= "<cidade>{$cidade}</cidade>\n";
			}
	
			$ref_sigla_uf = $ref_sigla_uf_ =  $det_mun['sigla_uf']->sigla_uf;
			
			$xml .= "<ref_sigla_uf>{$ref_sigla_uf}</ref_sigla_uf>\n";
			$xml .= "<ref_sigla_uf_>{$ref_sigla_uf_}</ref_sigla_uf_>\n";
			
		}
	
		$obj_bairro = new clsBairro($obj_endereco_det["ref_idbai"]);
		$obj_bairro_det = $obj_bairro->detalhe();
	
		if($obj_bairro_det)
		{
			$nm_bairro = $obj_bairro_det["nome"];
			
			$xml .= "<nm_bairro>{$nm_bairro}</nm_bairro>\n";
			
		}
	}
	if($idpes_pai)
	{
		$obj_pessoa_pai = new clsPessoaFj($idpes_pai);
		$det_pessoa_pai = $obj_pessoa_pai->detalhe();
		if($det_pessoa_pai)
		{
			$nm_pai = $det_pessoa_pai["nome"];
			
			$xml .= "<nm_pai>{$nm_pai}</nm_pai>\n";
			
			$obj_cpf = new clsFisica($idpes_pai);
			$det_cpf = $obj_cpf->detalhe();
			if( $det_cpf["cpf"] )
			{
				$cpf_pai = int2CPF( $det_cpf["cpf"] );
				
				$xml .= "<cpf_pai>{$cpf_pai}</cpf_pai>\n";
				
			}
			
		}
	}
	if($idpes_mae)
	{
		$obj_pessoa_mae = new clsPessoaFj($idpes_mae);
		$det_pessoa_mae = $obj_pessoa_mae->detalhe();
		if($det_pessoa_mae)
		{
			$nm_mae = $det_pessoa_mae["nome"];
			
			$xml .= "<nm_mae>{$nm_mae}</nm_mae>\n";
			
			//cpf
			$obj_cpf = new clsFisica($idpes_mae);
			$det_cpf = $obj_cpf->detalhe();
			if( $det_cpf["cpf"] )
			{
				$cpf_mae = int2CPF( $det_cpf["cpf"] );
				
				$xml .= "<cpf_mae>{$cpf_mae}</cpf_mae>\n";
				
			}
		}
	}
	if(!$tipo_responsavel)
	{
		if($nm_pai)
			$tipo_responsavel = 'p';
		elseif($nm_mae)
			$tipo_responsavel = 'm';
		elseif($ref_idpes_responsavel)
			$tipo_responsavel = 'r';
			
		$xml .= "<tipo_responsavel>{$tipo_responsavel}</tipo_responsavel>\n";
			
	}
	
	if($ref_idpes)
	{
		$ObjDocumento = new clsDocumento($ref_idpes);
		$detalheDocumento = $ObjDocumento->detalhe();
	
		$rg = $detalheDocumento['rg'];
		
		$xml.= "<rg>{$rg}</rg>\n";
	
		if($detalheDocumento['data_exp_rg'])
		{
			$data_exp_rg = date( "d/m/Y", strtotime( substr($detalheDocumento['data_exp_rg'],0,19) ) );
			
			$xml.= "<data_exp_rg>{$data_exp_rg}</data_exp_rg>\n";
			
		}
	
		$sigla_uf_exp_rg = $detalheDocumento['sigla_uf_exp_rg'];
		$tipo_cert_civil = $detalheDocumento['tipo_cert_civil'];
		$num_termo = $detalheDocumento['num_termo'];
		$num_livro = $detalheDocumento['num_livro'];
		$num_folha = $detalheDocumento['num_folha'];
	
		$xml .= "<sigla_uf_exp_rg>{$sigla_uf_exp_rg}</sigla_uf_exp_rg>\n";
		$xml .= "<tipo_cert_civil>{$tipo_cert_civil}</tipo_cert_civil>\n";
		$xml .= "<num_termo>{$num_termo}</num_termo>\n";
		$xml .= "<num_livro>{$num_livro}</num_livro>\n";
		$xml .= "<num_folha>{$num_folha}</num_folha>\n";
		
		if($detalheDocumento['data_emissao_cert_civil'])
		{
			$data_emissao_cert_civil = date( "d/m/Y", strtotime( substr($detalheDocumento['data_emissao_cert_civil'],0,19) ) );
			
			$xml .= "<data_emissao_cert_civil>{$data_emissao_cert_civil}</data_emissao_cert_civil>\n";
		}
	
		$sigla_uf_cert_civil = $detalheDocumento['sigla_uf_cert_civil'];
	
		$cartorio_cert_civil = $detalheDocumento['cartorio_cert_civil'];
		$num_cart_trabalho = $detalheDocumento['num_cart_trabalho'];
		$serie_cart_trabalho = $detalheDocumento['serie_cart_trabalho'];
		
		$xml .= "<sigla_uf_cert_civil>{$sigla_uf_cert_civil}</sigla_uf_cert_civil>\n";
		$xml .= "<cartorio_cert_civil>{$cartorio_cert_civil}</cartorio_cert_civil>\n";
		$xml .= "<num_cart_trabalho>{$num_cart_trabalho}</num_cart_trabalho>\n";
		$xml .= "<serie_cart_trabalho>{$serie_cart_trabalho}</serie_cart_trabalho>\n";
	
		if($detalheDocumento['data_emissao_cart_trabalho'])
		{
			$data_emissao_cart_trabalho = date( "d/m/Y", strtotime( substr($detalheDocumento['data_emissao_cart_trabalho'],0,19) ) );
		
			$xml .= "<data_emissao_cart_trabalho>{$data_emissao_cart_trabalho}</data_emissao_cart_trabalho>\n";
		}
	
		$sigla_uf_cart_trabalho = $detalheDocumento['sigla_uf_cart_trabalho'];
		$num_tit_eleitor = $detalheDocumento['num_tit_eleitor'];
		$zona_tit_eleitor = $detalheDocumento['zona_tit_eleitor'];
		$secao_tit_eleitor = $detalheDocumento['secao_tit_eleitor'];
		$idorg_exp_rg = $detalheDocumento['idorg_exp_rg'];
		
		$xml .= "<sigla_uf_cart_trabalho>{$sigla_uf_cart_trabalho}</sigla_uf_cart_trabalho>\n";
		$xml .= "<num_tit_eleitor>{$num_tit_eleitor}</num_tit_eleitor>\n";
		$xml .= "<zona_tit_eleitor>{$zona_tit_eleitor}</zona_tit_eleitor>\n";
		$xml .= "<secao_tit_eleitor>{$secao_tit_eleitor}</secao_tit_eleitor>\n";
		$xml .= "<idorg_exp_rg>{$idorg_exp_rg}</idorg_exp_rg>\n";
		
	}
	
	$xml .= "</dados>\n";
$xml .= "</query>";
echo $xml;
}

?>