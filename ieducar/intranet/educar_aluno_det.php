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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Aluno" );
		$this->processoAp = "578";
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

	var $cod_aluno;
	var $ref_idpes_responsavel;
	var $idpes_pai;
	var $idpes_mae;
	var $ref_cod_pessoa_educ;
	var $ref_cod_aluno_beneficio;
	var $ref_cod_religiao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_idpes;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $nm_pai;
	var $nm_mae;
	var $ref_cod_raca;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Aluno - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_aluno=$_GET["cod_aluno"];

		$tmp_obj = new clsPmieducarAluno( $this->cod_aluno );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_aluno_lst.php" );
			die();
		}
		else{

			foreach ($registro as $key => $value) {
				$this->$key = $value;
			}
		}

		if($this->ref_idpes){

			$obj_pessoa_fj = new clsPessoaFj($this->ref_idpes);
			$det_pessoa_fj = $obj_pessoa_fj->detalhe();

			$obj_fisica = new clsFisica($this->ref_idpes);
			$det_fisica = $obj_fisica->detalhe();

			$obj_fisica_raca = new clsCadastroFisicaRaca();
			$lst_fisica_raca = $obj_fisica_raca->lista( $this->ref_idpes );
			if ($lst_fisica_raca)
			{
				$det_fisica_raca = array_shift($lst_fisica_raca);

				$obj_raca = new clsCadastroRaca( $det_fisica_raca['ref_cod_raca'] );
				$det_raca = $obj_raca->detalhe();
			}

			$registro["nome_aluno"]  = $det_pessoa_fj["nome"];

			$registro["cpf"]  = int2IdFederal($det_fisica["cpf"]);


			$registro["data_nasc"]  = dataToBrasil($det_fisica["data_nasc"]);

			$registro["sexo"]  = $det_fisica["sexo"] == "F" ? "Feminino" : "Masculino";

			$obj_estado_civil = new clsEstadoCivil();
			$obj_estado_civil_lista = $obj_estado_civil->lista();

			$lista_estado_civil = array();

			if($obj_estado_civil_lista){

				foreach ($obj_estado_civil_lista as $estado_civil)
				{
					$lista_estado_civil[$estado_civil["ideciv"]] = $estado_civil["descricao"];
				}

			}

			$registro["ideciv"]  = $lista_estado_civil[$det_fisica["ideciv"]->ideciv ];

			$registro["email"] = 	$det_pessoa_fj["email"];


			$registro["url"] = 	$det_pessoa_fj["url"];

			$registro["nacionalidade"] = $det_fisica["nacionalidade"];

			$registro["naturalidade"] = $det_fisica["idmun_nascimento"]->detalhe();

			$registro["naturalidade"] = $registro["naturalidade"]["nome"];
			//$detalhe_pais_origem  = $det_fisica["idpais_estrangeiro"];

		 	$registro["pais_origem"] = $det_fisica["idpais_estrangeiro"]->detalhe();
			$registro["pais_origem"] =$registro["pais_origem"]["nome"];

			$registro["ref_idpes_responsavel"] = $det_fisica["idpes_responsavel"];

			$this->idpes_pai = $det_fisica["idpes_pai"];
			$this->idpes_mae = $det_fisica["idpes_mae"];


			$this->nm_pai = $detalhe_aluno["nm_pai"];
			$this->nm_mae = $detalhe_aluno["nm_mae"];


			if($this->idpes_pai)
			{
				$obj_pessoa_pai = new clsPessoaFj($this->idpes_pai);
				$det_pessoa_pai = $obj_pessoa_pai->detalhe();
				if($det_pessoa_pai)
				{
					$registro['nm_pai'] = $det_pessoa_pai["nome"];
					//cpf
					$obj_cpf = new clsFisica($this->idpes_pai);
					$det_cpf = $obj_cpf->detalhe();
					if( $det_cpf["cpf"] )
					{
						$this->cpf_pai = int2CPF( $det_cpf["cpf"] );
					}
				}
			}
			if($this->idpes_mae)
			{
				$obj_pessoa_mae = new clsPessoaFj($this->idpes_mae);
				$det_pessoa_mae = $obj_pessoa_mae->detalhe();
				if($det_pessoa_mae)
				{
					$registro['nm_mae'] = $det_pessoa_mae["nome"];
					//cpf
					$obj_cpf = new clsFisica($this->idpes_mae);
					$det_cpf = $obj_cpf->detalhe();
					if( $det_cpf["cpf"] )
					{
						$this->cpf_mae = int2CPF( $det_cpf["cpf"] );
					}
				}
			}

			//$registro["nm_pai"] = $det_fisica["nm_pai"];
			//$registro["nm_mae"] = $det_fisica["nm_mae"];

			$registro["ddd_fone_1"] = $det_pessoa_fj["ddd_1"];
			$registro["fone_1"] = $det_pessoa_fj["fone_1"];

			$registro["ddd_fone_2"] = $det_pessoa_fj["ddd_2"];
			$registro["fone_2"] = $det_pessoa_fj["fone_2"];

			$registro["ddd_fax"] = $det_pessoa_fj["ddd_fax"];
			$registro["fone_fax"] = $det_pessoa_fj["fone_fax"];

			$registro["ddd_mov"] = $det_pessoa_fj["ddd_mov"];
			$registro["fone_mov"] = $det_pessoa_fj["fone_mov"];



			$obj_deficiencia_pessoa = new clsCadastroFisicaDeficiencia();

			$obj_deficiencia_pessoa_lista = $obj_deficiencia_pessoa->lista($this->ref_idpes);

			if($obj_deficiencia_pessoa_lista)
			{
				$deficiencia_pessoa = array();
				foreach ($obj_deficiencia_pessoa_lista as $deficiencia)
				{
					$obj_def = new clsCadastroDeficiencia($deficiencia["ref_cod_deficiencia"]);
					$det_def =  $obj_def->detalhe();
					$deficiencia_pessoa[$deficiencia["ref_cod_deficiencia"]] = $det_def["nm_deficiencia"];
				}
			}


			$ObjDocumento = new clsDocumento($this->ref_idpes);
			$detalheDocumento = $ObjDocumento->detalhe();

			$registro["rg"] = $detalheDocumento['rg'];

			if($detalheDocumento['data_exp_rg'])
			{
				$registro["data_exp_rg"] = date( "d/m/Y", strtotime( substr($detalheDocumento['data_exp_rg'],0,19) ) );
			}

			$registro["sigla_uf_exp_rg"] = $detalheDocumento['sigla_uf_exp_rg'];
			$registro["tipo_cert_civil"] = $detalheDocumento['tipo_cert_civil'];
			$registro["num_termo"] = $detalheDocumento['num_termo'];
			$registro["num_livro"] = $detalheDocumento['num_livro'];
			$registro["num_folha"] = $detalheDocumento['num_folha'];

			if($detalheDocumento['data_emissao_cert_civil'])
			{
				$registro["data_emissao_cert_civil"] = date( "d/m/Y", strtotime( substr($detalheDocumento['data_emissao_cert_civil'],0,19) ) );
			}

			$registro["sigla_uf_cert_civil"] = $detalheDocumento['sigla_uf_cert_civil'];

			$registro["cartorio_cert_civil"] = $detalheDocumento['cartorio_cert_civil'];
			$registro["num_cart_trabalho"] = $detalheDocumento['num_cart_trabalho'];
			$registro["serie_cart_trabalho"] = $detalheDocumento['serie_cart_trabalho'];

			if($detalheDocumento['data_emissao_cart_trabalho'])
			{
				$registro["data_emissao_cart_trabalho"] = date( "d/m/Y", strtotime( substr($detalheDocumento['data_emissao_cart_trabalho'],0,19) ) );
			}

			$registro["sigla_uf_cart_trabalho"] = $detalheDocumento['sigla_uf_cart_trabalho'];
			$registro["num_tit_eleitor"] = $detalheDocumento['num_titulo_eleitor'];
			$registro["zona_tit_eleitor"] = $detalheDocumento['zona_titulo_eleitor'];
			$registro["secao_tit_eleitor"] = $detalheDocumento['secao_titulo_eleitor'];
			$registro["idorg_exp_rg"] = $detalheDocumento['ref_idorg_rg'];

			$obj_endereco = new clsPessoaEndereco($this->ref_idpes);

			if($obj_endereco_det = $obj_endereco->detalhe()){

				$registro["id_cep"]       = $obj_endereco_det['cep']->cep;
				//$this->cep_       = $obj_endereco_det['ref_cep'];
				$registro["id_bairro"]    = $obj_endereco_det['idbai']->idbai;
				$registro["id_logradouro"]    = $obj_endereco_det['idlog']->idlog;
				$registro["numero"]    	= $obj_endereco_det['numero'];
				$registro["letra"]    	= $obj_endereco_det['letra'];
				$registro["complemento"]  = $obj_endereco_det['complemento'];
				$registro["andar"]    	= $obj_endereco_det['andar'];
				$registro["apartamento"]  = $obj_endereco_det['apartamento'];
				$registro["bloco"]	    = $obj_endereco_det['bloco'];

				//$registro["ref_idtlog"]	    = $obj_endereco_det['idtlog'];

				//$registro["cidade =  $obj_endereco_det['cidade'];
				//$registro["nm_bairro"] =  $obj_endereco_det['bairro'];
				$registro["nm_logradouro"] =  $obj_endereco_det['logradouro'];

				//$registro["ref_sigla_uf = $this->ref_sigla_uf_ =  $obj_endereco_det['sigla_uf'];

				$registro["cep_"] = int2CEP($registro["id_cep"]);

					$obj_bairro = new clsBairro($registro["id_bairro"] );
						$obj_bairro_det = $obj_bairro->detalhe();

						if($obj_bairro_det){

							$registro["nm_bairro"]= $obj_bairro_det["nome"];
						}

						$obj_log = new clsLogradouro($registro["id_logradouro"] );
						$obj_log_det = $obj_log->detalhe();

						if($obj_log_det){

							$registro["nm_logradouro"] = $obj_log_det["nome"];

							$registro["idtlog"] = $obj_log_det["idtlog"]->detalhe();
							$registro["idtlog"] = 	$registro["idtlog"]["descricao"];
							$obj_mun = new clsMunicipio( $obj_log_det["idmun"]);
							$det_mun = $obj_mun->detalhe();
							if($det_mun)
								$registro["cidade"] = ucfirst(strtolower($det_mun["nome"]));
						}

						$obj_bairro = new clsBairro($registro["id_bairro"]);
						$obj_bairro_det = $obj_bairro->detalhe();

						if($obj_bairro_det){

							$registro["nm_bairro"] = $obj_bairro_det["nome"];
						}
			}
			else
			{

				$obj_endereco = new clsEnderecoExterno($this->ref_idpes);

				if($obj_endereco_det = $obj_endereco->detalhe()){

					$registro["id_cep"]         = $obj_endereco_det['cep'];
					$registro["cidade"] =  $obj_endereco_det['cidade'];
					$registro["nm_bairro"] =  $obj_endereco_det['bairro'];
					$registro["nm_logradouro"] =  $obj_endereco_det['logradouro'];

					$registro["numero"]    	= $obj_endereco_det['numero'];
					$registro["letra"]    	= $obj_endereco_det['letra'];
					$registro["complemento"]  = $obj_endereco_det['complemento'];
					$registro["andar"]    	= $obj_endereco_det['andar'];
					$registro["apartamento"]  = $obj_endereco_det['apartamento'];
					$registro["bloco"]	    = $obj_endereco_det['bloco'];

					$registro["idtlog"]    = $obj_endereco_det['idtlog']->detalhe();
					$registro["idtlog"]  = $registro["idtlog"]["descricao"]  ;

			 		$det_uf = $obj_endereco_det['sigla_uf']->detalhe();
			 		$registro["ref_sigla_uf"] =  $det_uf["nome"];

					$registro["cep_"]= int2CEP($registro["id_cep"]);

					//$this->id_cep = int2CEP($this->cep_);



				}
			}
		}

		if( $registro["cod_aluno"] )
		{
			$this->addDetalhe( array( "C&oacute;digo Aluno", "{$registro["cod_aluno"]}") );
		}

		if( $registro["nome_aluno"] )
		{
			$this->addDetalhe( array( "Nome Aluno", "{$registro["nome_aluno"]}") );
		}

		if( idFederal2int($registro["cpf"]) )
		{
			$this->addDetalhe( array( "CPF", "{$registro["cpf"]}") );
		}

		if( $registro["data_nasc"] )
		{
			$this->addDetalhe( array( "Data de Nascimento", "{$registro["data_nasc"]}") );
		}

		/**
		 * analfabeto
		 */
		$this->addDetalhe( array( "Analfabeto", $registro['analfabeto'] == 0 ? "N&atilde;o" : "Sim") );


		if( $registro["sexo"] )
		{
			$this->addDetalhe( array( "Sexo", "{$registro["sexo"]}") );
		}

		if( $registro["ideciv"] )
		{
			$this->addDetalhe( array( "Estado Civil", "{$registro["ideciv"]}") );
		}
		if( $registro["id_cep"] )
		{
			$this->addDetalhe( array( "CEP", "{$registro["cep_"]}") );
		}

		if( $registro["ref_sigla_uf"] )
		{
			$this->addDetalhe( array( "UF", "{$registro["ref_sigla_uf"]}") );
		}

		if( $registro["cidade"] )
		{
			$this->addDetalhe( array( "Cidade", "{$registro["cidade"]}") );
		}

		if( $registro["nm_bairro"] )
		{
			$this->addDetalhe( array( "Bairro", "{$registro["nm_bairro"]}") );
		}

		if( $registro["nm_logradouro"] )
		{
			$logradouro = "";
			if( $registro["idtlog"] )
			{
				$logradouro .= "{$registro["idtlog"]}: ";
			}
			$logradouro .= $registro["nm_logradouro"];
			$this->addDetalhe( array( "Logradouro", "{$logradouro}") );
		}
		if( $registro["numero"] )
		{
			$this->addDetalhe( array( "N&uacute;mero", "{$registro["numero"]}") );
		}

		if( $registro["letra"] )
		{
			$this->addDetalhe( array( "Letra", "{$registro["letra"]}") );
		}

		if( $registro["complemento"] )
		{
			$this->addDetalhe( array( "Complemento", "{$registro["complemento"]}") );
		}

		if( $registro["bloco"] )
		{
			$this->addDetalhe( array( "Bloco", "{$registro["bloco"]}") );
		}

		if( $registro["andar"] )
		{
			$this->addDetalhe( array( "Andar", "{$registro["andar"]}") );
		}

		if( $registro["apartamento"] )
		{
			$this->addDetalhe( array( "Apartamento", "{$registro["apartamento"]}") );
		}

		if( $registro["naturalidade"] )
		{
			$this->addDetalhe( array( "Naturalidade", "{$registro["naturalidade"]}") );
		}

		if( $registro["nacionalidade"] )
		{
			$lista_nacionalidade = array('NULL' => "Selecione", '1' => "Brasileiro", '2' => "Naturalizado Brasileiro", '3' => "Estrangeiro");
			$registro["nacionalidade"] = $lista_nacionalidade[$registro["nacionalidade"]];
			$this->addDetalhe( array( "Nacionalidade", "{$registro["nacionalidade"]}") );
		}

		if( $registro["pais_origem"] )
		{
			$this->addDetalhe( array( "Pa&iacute;s de Origem", "{$registro["pais_origem"]}") );
		}

		$responsavel = $tmp_obj->getResponsavelAluno();

		if( $responsavel )
		{

			$this->addDetalhe( array( "Respons&aacute;vel Aluno", "{$responsavel["nome_responsavel"]}") );
		}

		if( $registro["ref_idpes_responsavel"] )
		{
			$obj_pessoa_resp = new clsPessoaFj($registro["ref_idpes_responsavel"]);
			$det_pessoa_resp = $obj_pessoa_resp->detalhe();
			if($det_pessoa_resp)
			{
				$registro["ref_idpes_responsavel"] = $det_pessoa_resp["nome"];
			}
			$this->addDetalhe( array( "Respons&aacute;vel", "{$registro["ref_idpes_responsavel"]}") );
		}

		if( $registro["nm_pai"] )
		{
			$this->addDetalhe( array( "Pai", "{$registro["nm_pai"]}") );
		}

		if( $registro["nm_mae"] )
		{
			$this->addDetalhe( array( "M&atilde;e", "{$registro["nm_mae"]}") );
		}

		if( $registro["fone_1"] )
		{
			if($registro["ddd_fone_1"])
				$registro["ddd_fone_1"] = "({$registro["ddd_fone_1"]})&nbsp;";

			$this->addDetalhe( array( "Telefone 1", "{$registro["ddd_fone_1"]}{$registro["fone_1"]}") );
		}

		if( $registro["fone_2"] )
		{
			if($registro["ddd_fone_2"])
				$registro["ddd_fone_2"] = "({$registro["ddd_fone_2"]})&nbsp;";

			$this->addDetalhe( array( "Telefone 2", "{$registro["ddd_fone_2"]}{$registro["fone_2"]}") );
		}

		if( $registro["fone_mov"] )
		{
			if($registro["ddd_mov"])
				$registro["ddd_mov"] = "({$registro["ddd_mov"]})&nbsp;";

			$this->addDetalhe( array( "Celular", "{$registro["ddd_mov"]}{$registro["fone_mov"]}") );
		}


		if( $registro["fone_fax"] )
		{
			if($registro["ddd_fax"])
				$registro["ddd_fax"] = "({$registro["ddd_fax"]})&nbsp;";

			$this->addDetalhe( array( "Fax", "{$registro["ddd_fax"]}{$registro["fone_fax"]}") );
		}

		if( $registro["email"] )
		{
			$this->addDetalhe( array( "e-mail", "{$registro["email"]}") );
		}

		if( $registro["url"] )
		{
			$this->addDetalhe( array( "P&aacute;gina Pessoal", "{$registro["url"]}") );
		}

		if( $registro["ref_cod_aluno_beneficio"] )
		{
			$obj_beneficio = new clsPmieducarAlunoBeneficio($registro["ref_cod_aluno_beneficio"]);
			$obj_beneficio_det= $obj_beneficio->detalhe();

			$this->addDetalhe( array( "Benef&iacute;cio", "{$obj_beneficio_det["nm_beneficio"]}") );
		}
		if( $registro["ref_cod_religiao"] )
		{

			$obj_religiao = new clsPmieducarReligiao($registro["ref_cod_religiao"]);
			$obj_religiao_det= $obj_religiao->detalhe();

			$this->addDetalhe( array( "Religi&atilde;o", "{$obj_religiao_det["nm_religiao"]}") );
		}
		if( $det_raca["nm_raca"] )
		{
			$this->addDetalhe( array( "Ra&ccedil;a", "{$det_raca["nm_raca"]}") );
		}

		if($deficiencia_pessoa){

			$tabela = "<table border=0 width='300' cellpadding=3><tr bgcolor='A1B3BD' align='center'><td>Defici&ecirc;ncias</td></tr>";
			$cor = "#D1DADF";
			foreach ($deficiencia_pessoa as $indice=>$valor)
			{

				$cor = $cor == "#D1DADF" ? "#E4E9ED" : "#D1DADF";
				$tabela .= "<tr bgcolor=$cor align='center'><td>{$valor}</td></tr>";
			}
			$tabela .= "</table>";
			$this->addDetalhe(array("Defici&ecirc;ncias", $tabela));
		}
//******
		if( $registro["rg"] )
		{
			$this->addDetalhe(array("Rg", $registro["rg"]));
		}

		if( $registro["data_exp_rg"] )
		{
			$this->addDetalhe(array("Data Expedição RG", $registro["data_exp_rg"]));
		}

		if( $registro["idorg_exp_rg"] )
		{
			$this->addDetalhe(array("Órgão Expedição RG", $registro["idorg_exp_rg"]));
		}

		if( $registro["sigla_uf_exp_rg"] )
		{
			$this->addDetalhe(array("Estado Expedidor", $registro["sigla_uf_exp_rg"]));
		}

		if( $registro["tipo_cert_civil"] )
		{
			$lista_tipo_cert_civil = array();
			$lista_tipo_cert_civil["0"] = "Selecione";
			$lista_tipo_cert_civil[91] = "Nascimento";
			$lista_tipo_cert_civil[92] = "Casamento";

			$this->addDetalhe(array("Tipo Certificado Civil", $registro["tipo_cert_civil"]));
		}

		if( $registro["num_termo"] )
		{
			$this->addDetalhe(array("Termo", $registro["num_termo"]));
		}

		if( $registro["num_livro"] )
		{
			$this->addDetalhe(array("Livro", $registro["num_livro"]));
		}

		if( $registro["num_folha"] )
		{
			$this->addDetalhe(array("Folha", $registro["num_folha"]));
		}

		if( $registro["data_emissao_cert_civil"] )
		{
			$this->addDetalhe(array("Emissão Certidão Civil", $registro["data_emissao_cert_civil"]));
		}

		if( $registro["sigla_uf_cert_civil"] )
		{
			$this->addDetalhe(array("Sigla Certidão Civil", $registro["sigla_uf_cert_civil"]));
		}

		if( $registro["cartorio_cert_civil"] )
		{
			$this->addDetalhe(array("Cartório", $registro["cartorio_cert_civil"]));
		}

		if( $registro["num_tit_eleitor"] )
		{
			$this->addDetalhe(array("Título de Eleitor", $registro["num_tit_eleitor"]));
		}

		if( $registro["zona_tit_eleitor"] )
		{
			$this->addDetalhe(array("Zona", $registro["zona_tit_eleitor"]));
		}

		if( $registro["secao_tit_eleitor"] )
		{
			$this->addDetalhe(array("Seção", $registro["secao_tit_eleitor"]));
		}

		if( $registro["caminho_foto"] )
		{
			$this->addDetalhe(array("Foto", "<img src='arquivos/educar/aluno/small/{$registro["caminho_foto"]}' border='0'>"));
		}

		$this->addDetalhe(array("Matrícula", $this->montaTabelaMatricula()));
		//** Verificacao de permissao para cadastro
		$obj_permissao = new clsPermissoes();
		if($obj_permissao->permissao_cadastra(578, $this->pessoa_logada,7))
		{
			$this->url_novo = "educar_aluno_cad.php";
			$this->url_editar = "educar_aluno_cad.php?cod_aluno={$registro["cod_aluno"]}";

		
				$this->array_botao = array("Matr&iacute;cula", "Atualizar Hist&oacute;rico", "Ficha do Aluno");
				$this->array_botao_url_script = array("go( \"educar_matricula_lst.php?ref_cod_aluno={$registro['cod_aluno']}\" );", "go( \"educar_historico_escolar_lst.php?ref_cod_aluno={$registro['cod_aluno']}\" );", "showExpansivelImprimir(400, 200,  \"educar_relatorio_aluno_dados.php?ref_cod_aluno={$registro['cod_aluno']}\",[], \"Relatório i-Educar\" )");
		}
		//**
		$this->url_cancelar = "educar_aluno_lst.php";
		$this->largura = "100%";
	}
	
	function montaTabelaMatricula()
	{
		$sql = "SELECT cod_matricula FROM pmieducar.matricula WHERE ref_cod_aluno = {$this->cod_aluno} AND ativo = 1 ORDER BY cod_matricula DESC";
		$db = new clsBanco();
		$db->Consulta($sql);
		if ($db->Num_Linhas())
		{
			while ($db->ProximoRegistro())
			{
				list($ref_cod_matricula) = $db->Tupla();
				if (is_numeric($ref_cod_matricula))
				{
					$obj_matricula = new clsPmieducarMatricula();
					$obj_matricula->setOrderby("ano ASC");
					$lst_matricula = $obj_matricula->lista( $ref_cod_matricula );
					if($lst_matricula)
						$registro = array_shift($lst_matricula);
					$table .= "<table class='tableDetalhe'><tr class='formdktd'><td colspan=2><strong>Matrícula - Ano {$registro["ano"]}</strong></td></tr>";
					$obj_ref_cod_curso = new clsPmieducarCurso( $registro["ref_cod_curso"] );
					$det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
					$nm_curso = $det_ref_cod_curso["nm_curso"];
					$obj_serie = new clsPmieducarSerie( $registro["ref_ref_cod_serie"] );
					$det_serie = $obj_serie->detalhe();
					$nm_serie = $det_serie["nm_serie"];
					$obj_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
					$obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
					$nm_instituicao = $obj_cod_instituicao_det["nm_instituicao"];
					$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_ref_cod_escola"] );
					$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
					$nm_escola = $det_ref_cod_escola["nome"];
					$obj_mat_turma = new clsPmieducarMatriculaTurma();
					$det_mat_turma = $obj_mat_turma->lista($ref_cod_matricula,null,null,null,null,null,null,null,1);
					if($det_mat_turma){
						$det_mat_turma = array_shift($det_mat_turma);
						$obj_turma = new clsPmieducarTurma($det_mat_turma['ref_cod_turma']);
						$det_turma = $obj_turma->detalhe();
						$nm_turma = $det_turma['nm_turma'];
					} else {
						$nm_turma = "";
					}
					$transferencias = array();
					if ($registro["aprovado"] == 1)
					{
						$aprovado = "Aprovado";
					}
					elseif ($registro["aprovado"] == 2)
					{
						$aprovado = "Reprovado";
					}
					elseif ($registro["aprovado"] == 3)
					{
						$aprovado = "Em Andamento";
					}
					elseif ($registro["aprovado"] == 4)
					{
						if (is_numeric($registro["cod_matricula"]))
						{
							$aprovado = "Transferido";
							$sql = "SELECT 
										ref_cod_matricula_entrada, 
										ref_cod_matricula_saida,
										to_char(data_transferencia, 'DD/MM/YYYY') as dt_transferencia
									FROM
										pmieducar.transferencia_solicitacao
									WHERE
										(ref_cod_matricula_entrada = {$registro["cod_matricula"]}
										OR ref_cod_matricula_saida = {$registro["cod_matricula"]})
										AND ativo = 1";
							$db2 = new clsBanco();
							$db2->Consulta($sql);
							if ($db2->Num_Linhas())
							{
								while ($db2->ProximoRegistro())
								{
									list($ref_cod_matricula_entrada, $ref_cod_matricula_saida, $dt_transferencia) = $db2->Tupla();
									if ($ref_cod_matricula_saida == $registro["cod_matricula"])
									{
										$transferencias[] = array("data_trans" => $dt_transferencia, "desc" => "Data Transferência Saída");
									}
									elseif ($ref_cod_matricula_entrada == $registro["cod_matricula"])
									{
										$transferencias[] = array("data_trans" => $dt_transferencia, "desc" => "Data Transferência Admissão");
									}
								}
							}
						}
					}
					elseif ($registro["aprovado"] == 5)
					{
						$aprovado = "Reclassificado";
					}
					elseif ($registro["aprovado"] == 6)
					{
						$aprovado = "Abandono";
					}
					elseif ($registro["aprovado"] == 7)
					{
						$aprovado = "Em Exame";
					}
					$formando = $registro["formando"] == 0 ? "N&atilde;o" : "Sim";
					$table .= "<tr class='formlttd'><td>Número da Matrícula</td><td>{$registro["cod_matricula"]}</td></tr>";
					$table .= "<tr class='formmdtd'><td>Instituição</td><td>{$nm_instituicao}</td></tr>";
					$table .= "<tr class='formlttd'><td>Escola</td><td>{$nm_escola}</td></tr>";
					$table .= "<tr class='formmdtd'><td>Curso</td><td>{$nm_curso}</td></tr>";
					$table .= "<tr class='formlttd'><td>Série</td><td>{$nm_serie}</td></tr>";
					$table .= "<tr class='formmdtd'><td>Turma</td><td>{$nm_turma}</td></tr>";
					$table .= "<tr class='formlttd'><td>Situação</td><td>{$aprovado}</td></tr>";

					$class = "formmdtd";
					if (is_array($transferencias))
					{
						asort($transferencias);
						foreach ($transferencias as $trans) 
						{
							$table .= "<tr class='{$class}'><td>{$trans["desc"]}</td><td>{$trans["data_trans"]}</td></tr>";
							$class = $class == "formmdtd" ? "formlttd" : "formmdtd";
						}
					}
					if ($registro["aprovado"] < 4) 
					{
						if (is_numeric($registro["cod_matricula"]))
						{
							$sql = "SELECT to_char(data_transferencia, 'DD/MM/YYYY') FROM pmieducar.transferencia_solicitacao 
									WHERE ref_cod_matricula_entrada = {$registro["cod_matricula"]} AND ativo = 1";
							$db2 = new clsBanco();
							$data_transferencia = $db2->CampoUnico($sql);
							if ($data_transferencia) 
							{
								$table .= "<tr class='{$class}'><td>Data Transferência Admissão</td><td>{$data_transferencia}</td></tr>";
								$class = $class == "formmdtd" ? "formlttd" : "formmdtd";
							}
						}
					}
					$table .= "<tr class='{$class}'><td>Formando</td><td>{$formando}</td></tr>";
					$table .= "</table>";
				}
			}
		} 
		else 
		{
			return "<strong>O aluno não está matriculado em nenhuma escola</strong>";
		}
		return $table;
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