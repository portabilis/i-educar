<?php

/*
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 */

/**
 * Histórico escolar.
 *
 * @author      Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  Aluno
 * @since       Arquivo disponível desde a versão 1.0.0
 * @version     $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/clsPDF.inc.php';


class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Hist&oacute;rico Escolar" );
		$this->processoAp = "707";
		$this->renderMenu = false;
		$this->renderMenuSuspenso = false;
	}
}

class indice extends clsCadastro
{


	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;


	var $ref_cod_instituicao;
	var $ref_cod_escola;
	var $ref_cod_aluno;

	var $nm_escola;
	var $nm_instituicao;
	var $nm_curso;
	var $nm_municipio;

	var $pdf;

	var $page_y = 195;

	var $get_link;
	var $cor_fundo;
	var $endereco;

	var $meses_do_ano = array(
							 "1" => "JANEIRO"
							,"2" => "FEVEREIRO"
							,"3" => "MARÇO"
							,"4" => "ABRIL"
							,"5" => "MAIO"
							,"6" => "JUNHO"
							,"7" => "JULHO"
							,"8" => "AGOSTO"
							,"9" => "SETEMBRO"
							,"10" => "OUTUBRO"
							,"11" => "NOVEMBRO"
							,"12" => "DEZEMBRO"
						);

	function renderHTML()
	{

		@session_start();
			$pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		if($_GET){
			foreach ($_GET as $key => $value) {
				$this->$key = $value;
			}
		}

		if($this->ref_ref_cod_serie)
			$this->ref_cod_serie = $this->ref_ref_cod_serie;

		$fonte = 'arial';
		$corTexto = '#000000';

		if(!is_numeric($this->ref_cod_aluno) || !is_numeric($this->ref_cod_escola))
		{

			echo "<center>Não existem dados a serem exibidos!</center>"	;
			echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');}</script>";
			die();
		}
//		if ($pessoa_logada==184580)
//		{
//			$this->verificaHistorico();
//		}


		$obj_historico_escolar = new clsPmieducarHistoricoEscolar();
		$obj_historico_escolar->setOrderby( "ano ASC" );
		$lst_historico_escolar = $obj_historico_escolar->lista( $this->ref_cod_aluno, null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null, 1, null,null,null);//, 0 );


		$ultima_mat = count($lst_historico_escolar) - 1;
		$observacao = $lst_historico_escolar[$ultima_mat]['observacao'];

		$obj_escola_instituicao = new clsPmieducarEscola();
		$lst_escola_instituicao = $obj_escola_instituicao->lista($this->ref_cod_escola, null, null, $this->ref_cod_instituicao, null, null, null, null, null, null,1);


		$this->pdf = new clsPDF("Histórico Escolar", "Histórico Escolar", "A4", "", false, false);
		$this->pdf->OpenPage();

		//***************INICIO CABECALHO

		$obj_aluno = new clsPmieducarAluno( $this->ref_cod_aluno );
		$det_aluno = $obj_aluno->detalhe();

		$obj_matricula = new clsPmieducarMatricula();
		$lst_matriculas = $obj_matricula->lista(null, null, $this->ref_cod_escola,null,null,null,$this->ref_cod_aluno,array(1,2),null,null,null,null,1,null,null,$this->ref_cod_instituicao,1,null,null,null,null,null,null,null,null);
		if($lst_matriculas)
			$cod_matricula = $lst_matriculas[0]['cod_matricula'];

		$obj_pessoa = new clsPessoa_( $det_aluno['ref_idpes'] );
		$det_pessoa = $obj_pessoa->detalhe();

		// NOME DO ALUNO
		$nm_aluno = str2upper($det_pessoa['nome']);

		$obj_fisica = new clsFisica( $det_aluno['ref_idpes'] );
		$det_fisica = $obj_fisica->detalhe();

		// SEXO
		$sexo = $det_fisica["sexo"];
		if ($sexo == "M")
			$sexo = "MASCULINO";
		else
			$sexo = "FEMININO";

		// DATA DE NASCIMENTO
		$dt_nasc = dataToBrasil( $det_fisica["data_nasc"] );
		$dia = substr( $dt_nasc, 0, 2 );
		$mes = substr( $dt_nasc, 3, 2 );
		$meses = array( "01" => "Janeiro", "02" => "Fevereiro", "03" => "Março", "04" => "Abril", "05" => "Maio", "06" => "Junho", "07" => "Julho", "08" => "Agosto", "09" => "Setembro", "10" => "Outubro", "11" => "Novembro", "12" => "Dezembro" );
		$ano = substr( $dt_nasc, 6, 4 );

		// NATURALIDADE
		$idmun_nascimento = $det_fisica["idmun_nascimento"]->idmun;
		$obj_mun_nasc = new clsMunicipio( $idmun_nascimento );
		$det_mun_nasc = $obj_mun_nasc->detalhe();
		$naturalidade = str2upper($det_mun_nasc['nome']);
		if( $det_mun_nasc['sigla_uf'] )
		{
			$naturalidade_uf = $det_mun_nasc['sigla_uf']->detalhe();
			$naturalidade_uf = $naturalidade_uf['nome'];
		}

		// NACIONALIDADE
		$nacionalidade = $det_fisica["nacionalidade"];
		if ($nacionalidade == 1)
		{
			$nacionalidade = "Brasileira";
		}
		else if ($nacionalidade == 2)
		{
			$nacionalidade = "Naturalizado(a) Brasileiro(a)";
		}
		else if ($nacionalidade == 3)
		{
			$nacionalidade = "Estrangeira";
		}

		$nm_pai = str2upper($det_aluno["nm_pai"]);
		$nm_mae = str2upper($det_aluno['nm_mae']);

		if( !$nm_pai || !$nm_mae )
		{
			$obj_fisica = new clsFisica( $det_aluno['ref_idpes'] );
			$det_fisica = $obj_fisica->detalhe();

			if(!$nm_pai)
				$nm_pai = str2upper($det_fisica["nome_pai"]);

			if(!$nm_mae)
				$nm_mae = str2upper($det_fisica["nome_mae"]);

			if( !$nm_pai )
			{
				$obj_pessoa = new clsPessoa_( $det_fisica["idpes_pai"] );
				$det_pessoa = $obj_pessoa->detalhe();
				// NOME DO PAI
				$nm_pai = str2upper($det_pessoa['nome']);

			}

			if(!$nm_mae )
			{

				$obj_pessoa = new clsPessoa_( $det_fisica["idpes_mae"] );
				$det_pessoa = $obj_pessoa->detalhe();
				// NOME DA MAE
				$nm_mae = str2upper($det_pessoa['nome']);
			}
		}

		$obj_escola_complemento = new clsPmieducarEscolaComplemento( $this->ref_cod_escola );
		$det_escola_complemento = $obj_escola_complemento->detalhe();
		if( $det_escola_complemento )
		{
			// NOME DA ESCOLA
			$nm_escola = str2upper($det_escola_complemento['nm_escola']);
			// ENDERECO DA ESCOLA
			$logradouro = str2upper($det_escola_complemento['logradouro']);
			$numero = $det_escola_complemento['numero'];
			$complemento = str2upper($det_escola_complemento['complemento']);

			$bairro = str2upper($det_escola_complemento['bairro']);
			$municipio = str2upper($det_escola_complemento['municipio']);
			$cep = $det_escola_complemento['cep'];
			$cep = int2CEP($cep);

			$this->endereco = "{$logradouro} {$complemento},{$numero} CEP {$cep} {$municipio}";

		}
		else
		{
			$obj_escola = new clsPmieducarEscola( $this->ref_cod_escola );
			$det_escola = $obj_escola->detalhe();

			$obj_juridica = new clsJuridica( $det_escola['ref_idpes'] );
			$det_juridica = $obj_juridica->detalhe();

			$nm_escola = $det_juridica['fantasia'];

			if( !$nm_escola )
			{
				$obj_pessoa_ = new clsPessoa_( $det_escola['ref_idpes'] );
				$det_pessoa_ = $obj_pessoa_->detalhe();

				$nm_escola = $det_pessoa_['nome'];
			}
			$this->nm_escola = $nm_escola;

			$obj_endereco = new clsPessoaEndereco($det_escola["ref_idpes"]);
			if ( class_exists( "clsPessoaEndereco" ) )
			{
				$tipo = 1;
				$endereco_lst = $obj_endereco->lista($det_escola["ref_idpes"]);
				if ( $endereco_lst )
				{
					foreach ($endereco_lst as $endereco)
					{
						$cep = $endereco["cep"]->cep;
						$idlog = $endereco["idlog"]->idlog;
						$obj = new clsLogradouro($idlog);
						$obj_det = $obj->detalhe();
						$logradouro = $obj_det["nome"];
						$idtlog = $obj_det["idtlog"]->detalhe();
						$tipo_logradouro = strtoupper($idtlog["descricao"]);
						$bairro = $idbai = $endereco["idbai"]->detalhe();
						$idbai = $idbai['nome'];
						$numero = $endereco["numero"];
						$complemento = $endereco["complemento"];
						$andar = $endereco["andar"];
					}
					$obj_log = new clsLogradouro($idlog );
					$obj_log_det = $obj_log->detalhe();
					if($obj_log_det)
					{
						$logradouro = str2upper($obj_log_det["nome"]);

						$obj_mun = new clsMunicipio( $obj_log_det["idmun"]);
						$det_mun = $obj_mun->detalhe();

						if($det_mun)
						{
							$municipio = str2upper($det_mun["nome"]);
						}
						$estado = $det_mun['sigla_uf']->sigla_uf;

					}

					$cep = int2CEP($cep);
					$this->endereco = "{$tipo_logradouro} {$logradouro} {$complemento},{$numero} CEP {$cep} {$municipio} {$estado}";

				}
				else if ( class_exists( "clsEnderecoExterno" ) )
				{
					$tipo = 2;
					$obj_endereco = new clsEnderecoExterno();
					$endereco_lst = $obj_endereco->lista( null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $det_escola["ref_idpes"]);
					if ( $endereco_lst )
					{
						foreach ($endereco_lst as $endereco)
						{

							$cep = $endereco["cep"];
							$estado =  $endereco["sigla_uf"]->sigla_uf;
							$sigla_uf = $endereco["sigla_uf"]->detalhe();
							$sigla_uf = $sigla_uf["nome"];
							$cidade = $endereco["cidade"];
							$idtlog = $endereco["idtlog"]->detalhe();
							$tipo_logradouro = $idtlog["descricao"];
							$logradouro = $endereco["logradouro"];
							$bairro = $endereco["bairro"];
							$numero = $endereco["numero"];
							$complemento = $endereco["complemento"];
							$andar = $endereco["andar"];
							$municipio = str2upper($endereco['cidade']);
							$bairro = str2upper($endereco_lst['bairro']);
						}
					}
					$cep = int2CEP($cep);
					$this->endereco = "{$tipo_logradouro} {$logradouro} {$complemento},{$numero}{$bairro} CEP {$cep} {$municipio} - {$sigla_uf}";
				}
			}



		}

		if( $dt_nasc )
		{
			$nascimento = ", nascido(a) em {$dia} de {$meses[$mes]} de {$ano}";
		}


		if( $naturalidade )
		{
			$natural = " natural de {$naturalidade},";
			if( $naturalidade_uf )
			{
				$natural_uf = " Estado de(o) {$naturalidade_uf}";
			}
		}
		if( $nacionalidade )
		{
			$nacional = " de nacionalidade {$nacionalidade}";
		}

		$naturalidade = ", {$natural}{$natural_uf}{$nacional}";

		$gruda_pai = ", filho(a) de ";

		if($nm_pai)
		{
			$pais = "{$gruda_pai}{$nm_pai}";
			$gruda_pai = " e de ";
		}

		if($nm_mae)
		{
			$pais .= "{$gruda_pai}{$nm_mae}";
		}

		if($sexo)
			$sexo = ", do sexo {$sexo}";

		if($cod_matricula)
		{
			$cod_matricula = ", matrícula {$cod_matricula}";
		}

		$serie_concluiu = ", cursou em {$lst_historico_escolar[$ultima_mat]['ano']}, o(a) {$lst_historico_escolar[$ultima_mat]['nm_serie']} do Ensino Fundamental";


		$this->addCabecalho($nm_aluno, $cod_matricula,$naturalidade,$sexo,$nascimento,$pais,$serie_concluiu);

		//*************** FIM CABECALHO

		if($lst_historico_escolar)
		{
			//*************** INICIO NOTAS

			$db = new clsBanco();
			/**
			 * busca nome das series e fonetiza
			 * para tentar remover duplicidades
			 */

			$consulta = "SELECT nm_serie
								,sequencial
								,CASE WHEN faltas_globalizadas IS NOT NULL THEN
									100::float -  (faltas_globalizadas::float / dias_letivos::float )::float * 100
								else
									carga_horaria
								END AS frequencia
								,CASE WHEN faltas_globalizadas IS NULL THEN
									0
								else
									1
								END AS faltas_globalizadas
						   FROM pmieducar.historico_escolar
						  WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'
						    AND ativo = 1
						  ORDER BY ano";

			$db->Consulta($consulta);
			if($db->Num_Linhas())
			{
				$series = array();
				while ($db->ProximoRegistro())
				{
					$registro = $db->Tupla();
					$registro['total_faltas'] = 0;
					$series[] = $registro; //['nm_serie'];

				}

				$serie_fonetizada = array();
				foreach ($series as $serie)
				{
					$fonetiza = fonetiza_palavra($serie['nm_serie']);
					$serie_fonetizada["{$fonetiza}"] = $serie;
				}

				$lst_series = array();
				foreach ($serie_fonetizada as $key => $serie)
				{
					$lst_series[$key] = $serie;
					$lst_series[$key]['nm_serie'] = $serie['nm_serie'];
					$frequencia[$key] = $serie['frequencia'] != '' ? number_format($serie['frequencia'],1,'.','')."%" : $serie['frequencia'];
				}

				$consulta = "SELECT nm_disciplina
							   FROM pmieducar.historico_disciplinas
							  WHERE ref_ref_cod_aluno = '{$this->ref_cod_aluno}'
							  AND nm_disciplina IS NOT NULL
							  AND nm_disciplina != ''
							  AND nota IS NOT NULL
							  AND nota != ''
							 ORDER BY 1";

				$db->Consulta($consulta);
				if($db->Num_Linhas())
				{
					$disciplinas = array();
					while ($db->ProximoRegistro())
					{
						$registro = $db->Tupla();
						$disciplinas[] = $registro;
					}

					$disciplina_fonetizada = array();

					foreach ($disciplinas as $disciplina)
					{
						$fonetiza = fonetiza_palavra($disciplina['nm_disciplina']);
						$disciplina_fonetizada["{$fonetiza}"] = $disciplina;
					}

					$lst_disciplinas = array();
					foreach ($disciplina_fonetizada as $disciplina)
					{
						$lst_disciplinas[] = $disciplina;
					}

				}

				/**
				 * cabecalho com todas as series
				 */
				$this->novaLinha($lst_series,'s');

				$notas = array();
				$possui_eja = false;
				foreach ($lst_disciplinas as $key => $disciplina)
				{

					foreach ($lst_series as $key2 => $serie)
					{
						$consulta = "SELECT nm_disciplina
									       ,nota
									       ,faltas
									  FROM pmieducar.historico_disciplinas
									 WHERE ref_ref_cod_aluno = {$this->ref_cod_aluno}
									   AND ref_sequencial = {$serie['sequencial']}
									   AND nm_disciplina IS NOT NULL
									   AND nm_disciplina != ''
									   AND nota IS NOT NULL
									   AND nota != ''
									ORDER BY 1";

						$db->Consulta($consulta);

						if($db->Num_Linhas())
						{
							while ($db->ProximoRegistro())
							{
								$registro = $db->Tupla();
								if(fonetiza_palavra($disciplina['nm_disciplina']) == fonetiza_palavra($registro['nm_disciplina']) )
								{
									if (is_numeric(substr($registro["nota"],0,1)) || is_numeric(substr($registro["nota"],strpos($registro["nota"],",")+1,1))) {
										$notas[fonetiza_palavra($disciplina['nm_disciplina'])][$serie['sequencial']] = number_format(str_replace(",",".",$registro['nota']),2,".",'');
									} else {
										if ($extra_curricular) {
											$possui_eja = true;
										}
										$notas[fonetiza_palavra($disciplina['nm_disciplina'])][$serie['sequencial']] = $registro["nota"];
									}
//									$notas[fonetiza_palavra($disciplina['nm_disciplina'])][$serie['sequencial']] = number_format(str_replace(",",".",$registro['nota']),2,".",'');
									//$falta += $registro['faltas'];
									if(!$serie['faltas_globalizadas'])
										$lst_series[$key2]['total_faltas'] += $registro['faltas'];
									break;
								}
								else
									$notas[fonetiza_palavra($disciplina['nm_disciplina'])][$serie['sequencial']] = "- -";

							}
						}
						else
						{
							$notas[fonetiza_palavra($disciplina['nm_disciplina'])][$serie['sequencial']] = "";
						}
					}
				}
				foreach ($notas as $key => $nota)
				{
					$nota['nm_disciplina'] = $disciplina_fonetizada[$key]['nm_disciplina'];
					$this->novaLinha($nota,'n');
				}
			}

			//*************** FIM NOTAS

			//*************** FREQUENCIA

			foreach ($lst_series as $key => $serie)
			{
				if(!$serie['faltas_globalizadas'])
				{
					$frequencia[$key] = 100 - ($serie['total_faltas'] / $serie['frequencia']) * 100;
				}
				if($frequencia[$key])
					$frequencia[$key] .="%";
			}

			$this->novaLinha($frequencia,'f');
			//*************** FREQUENCIA


			$this->page_y += 5;
			$this->cor_fundo = "";
			$this->linhaHistorico(array(),true);


			//*************** INICIO HISTORICO
			$consulta = "SELECT nm_serie
						        ,ano
						        ,escola
						        ,escola_cidade
						        ,escola_uf
						        ,CASE aprovado
						          	  WHEN 1 THEN 'APROVADO'
						              WHEN 2 THEN 'REPROVADO'
							 	 END
							 	,observacao
						   FROM pmieducar.historico_escolar
						  WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'
						    AND ativo = 1
						  ORDER BY ano";
			$observacao = '';
			$db->Consulta($consulta);
			$qtd_observacoes = 0;
			if($db->Num_Linhas())
			{
				while ($db->ProximoRegistro())
				{
					$registro =  $db->Tupla();
					if($registro[6])
					{
						$qtd_observacoes++;
						$observacao .= "{$registro[1]} - {$registro[6]}\n";
					}
					$registro = array($registro[0], $registro[1],$registro[5],$registro[2], $registro[3], $registro[4] );
					$this->linhaHistorico($registro);

				}
			}
			//*************** FIM HISTORICO
		}
		if ($qtd_observacoes < 3)
			$this->observacao($observacao);
		else
			$this->observacao($observacao, 100);

		if ($possui_eja)
		{
			$tabela_conversao = "\nConversão de Valores das Notas";
			$tabela_conversao .= "\nNRE - Necessita retomar os estudos - abaixo de 5,0";
			$tabela_conversao .= "\nEM   - Evidência Mínima  - 5,0 a 6,9";
			$tabela_conversao .= "\nEP   - Evidência Parcial - 7,0 a 8,4";
			$tabela_conversao .= "\nEC   - Evidência Completa - 8,5 a 10,0";
			$this->pdf->quadrado_relativo( 20, $this->page_y+5 , 555, 50,0.1,"#000000","#FFFFFF" );
			$this->pdf->escreve_relativo( "$tabela_conversao",23, $this->page_y + 3 , 545, 60, $fonte, 8, $corTexto, 'justify' );
			$this->page_y += 50;
		}

		$this->rodape(strtoupper("{$municipio} ({$estado})"));


		$this->pdf->CloseFile();
		$this->get_link = $this->pdf->GetLink();


		echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=".$this->get_link."'}</script>";

		echo "<html><center>Se o download não iniciar automaticamente <br /><a target='_blank' href='" . $this->get_link  . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>clique aqui!</a><br><br>
			<span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>

			Clique na Imagem para Baixar o instalador<br><br>
			<a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
			</span>
			</center>";

		/*else
		{

			echo "<center>O aluno não possui Histórico Escolar!</center>"	;
			echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');}</script>";
			die();

		}*/
	}

	function verificaHistorico () {
		if (is_numeric($this->ref_cod_aluno)) {
			@session_start();
			$pessoa_logada = $_SESSION['id_pessoa'];
			@session_write_close();
			$sql_existe_historico = "select sequencial from pmieducar.historico_escolar where ref_cod_aluno={$this->ref_cod_aluno}
										and ativo=1 and ano=2007";

			$sql = "SELECT ref_ref_cod_serie from pmieducar.matricula where cod_matricula in (
							SELECT MAX(cod_matricula) as max_matricula
										from pmieducar.matricula WHERE ref_cod_aluno = {$this->ref_cod_aluno}
										AND ano=2007 AND ativo=1 AND aprovado IN (1,2))";
			$db = new clsBanco();
			$serie = $db->CampoUnico($sql);
			if ($serie != 15) {
				$db = new clsBanco();
				$existe_historico = $db->CampoUnico($sql_existe_historico);

				$liberar_historico = false;
				$existe_historico_sequencial=false;
				if (!is_numeric($existe_historico)) {
					$liberar_historico=true;
				} else {
					$sql_existe_disciplina = "select 1 from pmieducar.historico_disciplinas where
											ref_ref_cod_aluno={$this->ref_cod_aluno} and ref_sequencial={$existe_historico}";
					$existe_disciplinas = $db->CampoUnico($sql_existe_disciplina);
					if (!is_numeric($existe_disciplinas)) {
						$liberar_historico = true;
						$existe_historico_sequencial = true;
					}
				}
				$sql_aprovado = "SELECT aprovado from pmieducar.matricula where cod_matricula in (
							SELECT MAX(cod_matricula) as max_matricula
										from pmieducar.matricula WHERE ref_cod_aluno = {$this->ref_cod_aluno}
										AND ano=2007 AND ativo=1 AND aprovado IN (1,2))";
				$aprovado_aux = $db->CampoUnico($sql_aprovado);
				if (is_numeric($existe_historico) && !$existe_historico_sequencial) {
					$sql_historico_aprovado = "SELECT aprovado from pmieducar.historico_escolar
											where ref_cod_aluno={$this->ref_cod_aluno}
											and sequencial={$existe_historico} and ano=2007 and ativo=1";
					$aprovado_historico = $db->CampoUnico($sql_historico_aprovado);
					/*if ($aprovado_aux != $aprovado_historico) {
					$liberar_historico = true;
					$sql_desativa_he = "update pmieducar.historico_escolar set ativo=0
					where ref_cod_aluno={$this->ref_cod_aluno}
					and sequencial={$existe_historico} and ano=2007";
					$db->Consulta($sql_desativa_he);
					$existe_historico_sequencial=false;
					}*/
				}

				$sql_aprovado = "SELECT aprovado from pmieducar.matricula where cod_matricula in (
							SELECT MAX(cod_matricula) as max_matricula
										from pmieducar.matricula WHERE ref_cod_aluno = {$this->ref_cod_aluno}
										AND ano=2007 AND ativo=1 AND aprovado IN (1,2))";
				$aprovado_aux = $db->CampoUnico($sql_aprovado);
				if (is_numeric($existe_historico) && !$existe_historico_sequencial) {
					$sql_historico_aprovado = "SELECT aprovado from pmieducar.historico_escolar
											where ref_cod_aluno={$this->ref_cod_aluno}
											and sequencial={$existe_historico} and ano=2007 and ativo=1";
					$aprovado_historico = $db->CampoUnico($sql_historico_aprovado);
					/*if ($aprovado_aux != $aprovado_historico) {
					$liberar_historico = true;
					$sql_desativa_he = "update pmieducar.historico_escolar set ativo=0
					where ref_cod_aluno={$this->ref_cod_aluno}
					and sequencial={$existe_historico} and ano=2007";
					$db->Consulta($sql_desativa_he);
					$existe_historico_sequencial=false;
					}*/
				}

				if ($liberar_historico) {
					$sql = "SELECT cod_matricula,aprovado,ref_ref_cod_escola,ref_ref_cod_serie,ref_cod_curso,matricula_reclassificacao from pmieducar.matricula where cod_matricula in (
							SELECT MAX(cod_matricula) as max_matricula
										from pmieducar.matricula WHERE ref_cod_aluno = {$this->ref_cod_aluno}
										AND ano=2007 AND ativo=1 AND aprovado IN (1,2))";
					$db->Consulta($sql);
					while ($db->ProximoRegistro()) {
						list($cod_matricula, $aprovado,$ref_cod_escola, $ref_cod_serie,$ref_cod_curso,$matricula_reclassificacao) = $db->Tupla();
					}
					$obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
					$lst_ano_letivo_modulo = $obj_ano_letivo_modulo->lista(2007,$ref_cod_escola);
					$qtd_modulos = count($lst_ano_letivo_modulo);
					$obj_curso = new clsPmieducarCurso($ref_cod_curso);
					$det_curso = $obj_curso->detalhe();
					$falta_ch_globalizada = $det_curso["falta_ch_globalizada"];
					$objTipoAvaliacao = new clsPmieducarTipoAvaliacao($det_curso["ref_cod_tipo_avaliacao"]);
					$detalhe_tipo_avaliacao = $objTipoAvaliacao->detalhe();
					$conceitual = $detalhe_tipo_avaliacao["conceitual"];
//					if () {
					if ($ref_cod_curso != 49 && $ref_cod_curso != 20 && $ref_cod_curso != 19 && $qtd_modulos == 4 && !$conceitual && is_array($det_curso) && is_numeric($ref_cod_serie) && is_numeric($cod_matricula) && is_numeric($aprovado) && is_numeric($ref_cod_escola) && $det_curso["padrao_ano_escolar"] == 1) {
						if ($ref_cod_serie != 15) {
							if ($det_curso["padrao_ano_escolar"] == 1) {
								if ($existe_historico_sequencial) {
									//							$obj_historico = new clsPmieducarHistoricoEscolar($this->ref_cod_aluno, $existe_historico_sequencial, $pessoa_logada);
									//							if (!$obj_historico->excluir()) {
									//								die("não exclui");
									//							}
								}
								$obj_escola2 = new clsPmieducarEscola($ref_cod_escola);
								$det_escola2 = $obj_escola2->detalhe();
								$ref_cod_instituicao = $det_escola2["ref_cod_instituicao"];

								$notas_faltas_disciplina = array();
								$obj_escola_serie_disciplina = new clsPmieducarEscolaSerieDisciplina();
								$lst_escola_serie_disciplina = $obj_escola_serie_disciplina->lista($ref_cod_serie,$ref_cod_escola,null,1);
								foreach ($lst_escola_serie_disciplina as $escola_serie_disciplina) {
									$obj_dispensa = new clsPmieducarDispensaDisciplina($cod_matricula,$ref_cod_serie,$ref_cod_escola,$escola_serie_disciplina["ref_cod_disciplina"], null, null, null, null, null, 1);
									$det_disciplina = $obj_dispensa->detalhe();
									if (!is_array($det_disciplina)) {
										$obj_nota = new clsPmieducarNotaAluno();
										$obj_nota->setOrderby("modulo");
										$lst_nota = $obj_nota->lista(null,null,null,$ref_cod_serie,$ref_cod_escola,$escola_serie_disciplina["ref_cod_disciplina"], $cod_matricula, null, null, null, null,null,null,1);
										if (is_array($lst_nota))
										{
											$soma_notas = 0;
											$possui_exame = false;
											foreach ($lst_nota as $key => $nota) {
												$obj_tipo_av_val = new clsPmieducarTipoAvaliacaoValores($nota["ref_ref_cod_tipo_avaliacao"], $nota["ref_sequencial"], null, null, null, null);
												$det_tipo_av_val = $obj_tipo_av_val->detalhe();
												if ($ref_cod_serie == 5) {
													$soma_notas = $det_tipo_av_val["valor"];
												} else {
													if ($key < $qtd_modulos) {
														$soma_notas += $det_tipo_av_val["valor"];
													} else {
														$possui_exame = true;
														$soma_notas += $nota["nota"] * 2;
													}
												}
											}
											if ($ref_cod_serie == 5) {
												$media = $soma_notas;
											} else {
												if ($possui_exame) {
													$media = $soma_notas / 6;
												} else {
													$media = $soma_notas / 4;
												}

											}
											$obj_media = new clsPmieducarTipoAvaliacaoValores();
											$det_media = $obj_media->lista($det_curso["ref_cod_tipo_avaliacao"], $det_curso["ref_sequencial"], null, null, $media, $media);
											if (is_array($det_media)) {
												$det_media = array_shift($det_media);
												$media = $det_media["valor"];
												$media = sprintf("%01.1f", $media);
												$media = str_replace(".", ",", $media);
											}
											$obj_disciplina = new clsPmieducarDisciplina($escola_serie_disciplina["ref_cod_disciplina"]);
											$det_disciplina = $obj_disciplina->detalhe();
											if ($falta_ch_globalizada) {
												$notas_faltas_disciplina[$escola_serie_disciplina["ref_cod_disciplina"]] = array("media" => $media, "falta" => null, "nm_disciplina" => $det_disciplina["nm_disciplina"]);
											} else {
												//pegar as faltas
												$sql = "select sum(faltas) from pmieducar.falta_aluno where
													ref_cod_matricula={$cod_matricula} and ref_cod_disciplina={$escola_serie_disciplina["ref_cod_disciplina"]}
													and ativo=1";
												$total_faltas = $db->CampoUnico($sql);
												$notas_faltas_disciplina[$escola_serie_disciplina["ref_cod_disciplina"]] = array("media" => $media, "falta" => $total_faltas, "nm_disciplina" => $det_disciplina["nm_disciplina"]);
											}
										}
									}
								}
								if (is_array($notas_faltas_disciplina)) {
									$extra_curricular = 0;
									if ($falta_ch_globalizada) {
										$sql = "SELECT SUM(falta) FROM pmieducar.faltas WHERE ref_cod_matricula = {$cod_matricula}";
										$db5 = new clsBanco();
										$total_faltas = $db5->CampoUnico($sql);
									} else {
										$total_faltas = null;
									}
									$obj_serie = new clsPmieducarSerie($ref_cod_serie);
									$det_serie = $obj_serie->detalhe();
									$carga_horaria_serie = $det_serie["carga_horaria"];

									$obj_escola = new clsPmieducarEscola( $ref_cod_escola );
									$det_escola = $obj_escola->detalhe();
									$ref_idpes = $det_escola["ref_idpes"];
									// busca informacoes da escola
									if ($ref_idpes)
									{
										$obj_escola = new clsPessoaJuridica($ref_idpes);
										$det_escola = $obj_escola->detalhe();
										$nm_escola = $det_escola["fantasia"];
										if($det_escola)
										{
											$cidade = $det_escola["cidade"];
											$uf = $det_escola["sigla_uf"];
										}
									}
									else
									{
										if ( class_exists( "clsPmieducarEscolaComplemento" ) )
										{
											$obj_escola = new clsPmieducarEscolaComplemento( $ref_cod_escola );
											$det_escola = $obj_escola->detalhe();
											$nm_escola = $det_escola["nm_escola"];
											$cidade = $det_escola["municipio"];
										}
									}
									//falta_ch_globalizada
									if ($det_curso["falta_ch_globalizada"] == 1) {
										$dias_letivos = $this->buscaDiasLetivos($ref_cod_escola);
									} else {
										$dias_letivos = null;
									}
									if ($matricula_reclassificacao == 1) {
										$descricao_reclassificacao = $this->verificaReclassificacao();
									} else {
										$descricao_reclassificacao = null;
									}
									$obj_historico = new clsPmieducarHistoricoEscolar($this->ref_cod_aluno,null,null,$pessoa_logada,$det_serie["nm_serie"], 2007,$carga_horaria_serie,$dias_letivos,$nm_escola,$cidade,$uf,$descricao_reclassificacao,$aprovado,null,null,1,$total_faltas,$ref_cod_instituicao,0,$extra_curricular,$cod_matricula);
									$cadastrou2 = $obj_historico->cadastra();
									if ($cadastrou2) {
										$obj_historico = new clsPmieducarHistoricoEscolar();
										$sequencial = $obj_historico->getMaxSequencial($this->ref_cod_aluno);
										foreach ($notas_faltas_disciplina as $nota_falta) {
											$obj_historico_disciplina = new clsPmieducarHistoricoDisciplinas(null,$this->ref_cod_aluno,$sequencial,$nota_falta["nm_disciplina"],$nota_falta["media"],$nota_falta["falta"]);
											$cadastrou3 =  $obj_historico_disciplina->cadastra();
											if (!$cadastrou3) {
												die("<br><br><br><br>nao cadastrou disciplina");
											}
										}
									} else {
										die("<br><br><br><br>nao cadastrou historico");
									}
								}
							}
						}
					} elseif ($ref_cod_curso != 49 && $ref_cod_curso != 20 && $ref_cod_curso != 19) {
						//fazer historico ejaaaa
						//pegar matriculas do eja do aluno
						$obj_matriculas = new clsPmieducarMatricula();
						$obj_matriculas->setOrderby("cod_matricula");
						$lst_matriculas = $obj_matriculas->lista(null,null,null,null,null,null,$this->ref_cod_aluno,array(1,2),null,null,null,null,1,2007);
						if (is_array($lst_matriculas) && count($lst_matriculas)) {
							foreach ($lst_matriculas as $matricula) {
								$liberar_historico=false;
								$db = new clsBanco();
								//fazer algo para ver se o he nao esta atualizado
								$sql = "SELECT sequencial FROM pmieducar.historico_escolar WHERE ref_cod_aluno = {$matricula["ref_cod_aluno"]}
									AND ref_cod_matricula = {$matricula["cod_matricula"]} AND ano = 2007 AND ativo = 1
									AND (to_char(data_cadastro,'DD/MM/YYYY') < '20/02/2008'
										OR to_char(data_cadastro,'YYYY')::int = 2007) AND data_exclusao is null";
								$existe_he_antigo = $db->CampoUnico($sql);
								if (is_numeric($existe_he_antigo)) {
									$liberar_historico = true;
								} else {
									//verificar se possui historico
									$sql = "SELECT 1 FROM pmieducar.historico_escolar WHERE ref_cod_aluno = {$matricula["ref_cod_aluno"]}
										AND ref_cod_matricula = {$matricula["cod_matricula"]} AND ano = 2007 AND ativo = 1";
									$existe_he = $db->CampoUnico($sql);
									if (!is_numeric($existe_he)) {
										$liberar_historico = true;
									}
								}
								if ($liberar_historico) {
									if (is_numeric($existe_he_antigo)) {
										$obj_historico = new clsPmieducarHistoricoEscolar($this->ref_cod_aluno,$existe_he_antigo,$pessoa_logada);
										//									if (!$obj_historico->excluir()) {
										//										die("nao exclui");
										//									}
									}
									//fazer novo he
									//destruir he antigo
									$obj_curso = new clsPmieducarCurso($matricula["ref_cod_curso"]);
									$det_curso = $obj_curso->detalhe();
									$falta_ch_globalizada = $det_curso["falta_ch_globalizada"];
									$objTipoAvaliacao = new clsPmieducarTipoAvaliacao($det_curso["ref_cod_tipo_avaliacao"]);
									$detalhe_tipo_avaliacao = $objTipoAvaliacao->detalhe();
									$conceitual = $detalhe_tipo_avaliacao["conceitual"];

									$notas_faltas_disciplina = array();
									$obj_escola_serie_disciplina = new clsPmieducarEscolaSerieDisciplina();
									$lst_escola_serie_disciplina = $obj_escola_serie_disciplina->lista($matricula["ref_ref_cod_serie"],$matricula["ref_ref_cod_escola"],null,1);
									foreach ($lst_escola_serie_disciplina as $escola_serie_disciplina) {
										$obj_dispensa = new clsPmieducarDispensaDisciplina($matricula["cod_matricula"],$matricula["ref_ref_cod_serie"],$matricula["ref_cod_escola"],$escola_serie_disciplina["ref_cod_disciplina"], null, null, null, null, null, 1);
										$det_disciplina = $obj_dispensa->detalhe();
										if (!is_array($det_disciplina)) {
											$obj_nota = new clsPmieducarNotaAluno();
											$obj_nota->setOrderby("modulo");
											$lst_nota = $obj_nota->lista(null,null,null,$matricula["ref_ref_cod_serie"],$matricula["ref_cod_escola"],$escola_serie_disciplina["ref_cod_disciplina"], $matricula["cod_matricula"], null, null, null, null,null,null,1);
											if (is_array($lst_nota) && count($lst_nota))
											{
												$nota_matricula = array_shift($lst_nota);
												$obj_tipo_av_val = new clsPmieducarTipoAvaliacaoValores($nota_matricula["ref_ref_cod_tipo_avaliacao"], $nota_matricula["ref_sequencial"]);
												$det_tipo_av_val = $obj_tipo_av_val->detalhe();
												if ($falta_ch_globalizada) {
													$faltas = null;
												} else {
													$sql = "select sum(faltas) from pmieducar.falta_aluno where
															ref_cod_matricula={$matricula["cod_matricula"]} and ref_cod_disciplina={$escola_serie_disciplina["ref_cod_disciplina"]}
															and ativo=1";
													$faltas = $db->CampoUnico($sql);
												}
												$obj_disciplina = new clsPmieducarDisciplina($escola_serie_disciplina["ref_cod_disciplina"]);
												$det_disciplina = $obj_disciplina->detalhe();
												$notas_faltas_disciplina[$escola_serie_disciplina["ref_cod_disciplina"]] = array("media" => $det_tipo_av_val["nome"], "falta" => $faltas, "nm_disciplina" => $det_disciplina["nm_disciplina"]);
											}
										}
									}
									if (is_array($notas_faltas_disciplina)) {
										$obj_escola2 = new clsPmieducarEscola($matricula["ref_ref_cod_escola"]);
										$det_escola2 = $obj_escola2->detalhe();
										$ref_cod_instituicao = $det_escola2["ref_cod_instituicao"];
										$extra_curricular = 1;
										if ($falta_ch_globalizada) {
											$sql = "SELECT SUM(falta) FROM pmieducar.faltas WHERE ref_cod_matricula = {$matricula["cod_matricula"]}";
											$db5 = new clsBanco();
											$total_faltas = $db5->CampoUnico($sql);
										} else {
											$total_faltas = null;
										}
										$obj_serie = new clsPmieducarSerie($matricula["ref_ref_cod_serie"]);
										$det_serie = $obj_serie->detalhe();
										$carga_horaria_serie = $det_serie["carga_horaria"];

										$obj_escola = new clsPmieducarEscola( $matricula["ref_ref_cod_escola"] );
										$det_escola = $obj_escola->detalhe();
										$ref_idpes = $det_escola["ref_idpes"];
										// busca informacoes da escola
										if ($ref_idpes)
										{
											$obj_escola = new clsPessoaJuridica($ref_idpes);
											$det_escola = $obj_escola->detalhe();
											$nm_escola = $det_escola["fantasia"];
											if($det_escola)
											{
												$cidade = $det_escola["cidade"];
												$uf = $det_escola["sigla_uf"];
											}
										}
										else
										{
											if ( class_exists( "clsPmieducarEscolaComplemento" ) )
											{
												$obj_escola = new clsPmieducarEscolaComplemento( $matricula["ref_ref_cod_escola"] );
												$det_escola = $obj_escola->detalhe();
												$nm_escola = $det_escola["nm_escola"];
												$cidade = $det_escola["municipio"];
											}
										}
										//falta_ch_globalizada
										if ($det_curso["falta_ch_globalizada"] == 1) {
											//										$dias_letivos = $this->buscaDiasLetivos($matricula["ref_ref_cod_escola"]);
											$dias_letivos = null;
										} else {
											$dias_letivos = null;
										}
										if ($matricula["matricula_reclassificacao"] == 1) {
											$descricao_reclassificacao = $this->verificaReclassificacao($matricula["cod_matricula"]);
										} else {
											$descricao_reclassificacao = null;
										}
										$obj_historico = new clsPmieducarHistoricoEscolar($this->ref_cod_aluno,null,null,$pessoa_logada,$det_serie["nm_serie"], 2007,$carga_horaria_serie,$dias_letivos,$nm_escola,$cidade,$uf,$descricao_reclassificacao,$matricula["aprovado"],null,null,1,$total_faltas,$ref_cod_instituicao,0,$extra_curricular,$matricula["cod_matricula"]);
										$cadastrou2 = $obj_historico->cadastra();
										if ($cadastrou2) {
											$obj_historico = new clsPmieducarHistoricoEscolar();
											$sequencial = $obj_historico->getMaxSequencial($this->ref_cod_aluno);
											foreach ($notas_faltas_disciplina as $nota_falta) {
												$obj_historico_disciplina = new clsPmieducarHistoricoDisciplinas(null,$this->ref_cod_aluno,$sequencial,$nota_falta["nm_disciplina"],$nota_falta["media"],$nota_falta["falta"]);
												$cadastrou3 =  $obj_historico_disciplina->cadastra();
												if (!$cadastrou3) {
													die("<br><br><br><br>nao cadastrou disciplina");
												}
											}
										} else {
											die("<br><br><br><br>nao cadastrou historico");
										}
									}
								}
							}
						}
					}
				} elseif ($ref_cod_curso != 49 && $ref_cod_curso != 20 && $ref_cod_curso != 19) {
					$sql = "SELECT ref_ref_cod_escola,ref_cod_curso from pmieducar.matricula where cod_matricula in (
							SELECT MAX(cod_matricula) as max_matricula
										from pmieducar.matricula WHERE ref_cod_aluno = {$this->ref_cod_aluno}
										AND ano=2007 AND ativo=1 AND aprovado IN (1,2))";
					$db = new clsBanco();
					$db->Consulta($sql);
					while ($db->ProximoRegistro()) {
						list($ref_cod_escola,$ref_cod_curso)=$db->Tupla();
					}
					$obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
					$lst_ano_letivo_modulo = $obj_ano_letivo_modulo->lista(2007,$ref_cod_escola);
					$qtd_modulos = count($lst_ano_letivo_modulo);
					$obj_curso = new clsPmieducarCurso($ref_cod_curso);
					$det_curso = $obj_curso->detalhe();
					$falta_ch_globalizada = $det_curso["falta_ch_globalizada"];
					$objTipoAvaliacao = new clsPmieducarTipoAvaliacao($det_curso["ref_cod_tipo_avaliacao"]);
					$detalhe_tipo_avaliacao = $objTipoAvaliacao->detalhe();
					$conceitual = $detalhe_tipo_avaliacao["conceitual"];
					if ($qtd_modulos == 4 && !$conceitual) {
						$sql_eh_falta_globalizada = "SELECT 1 FROM pmieducar.historico_escolar WHERE ref_cod_aluno = {$this->ref_cod_aluno} AND sequencial = {$existe_historico}
											 AND faltas_globalizadas IS NOT NULL AND dias_letivos IS NULL";
						$db = new clsBanco();
						$busca_dias_letivos = $db->CampoUnico($sql_eh_falta_globalizada);
						if (is_numeric($busca_dias_letivos)) {
							$dias_letivos = $this->buscaDiasLetivos($ref_cod_escola);
							if ($dias_letivos) {
								$obj_he = new clsPmieducarHistoricoEscolar($this->ref_cod_aluno,$existe_historico,$pessoa_logada,null,null,null,null,$dias_letivos);
								if (!$obj_he->edita()) {
									die("nao editou dias letivos");
								}
							}
						}
						$sql = "SELECT MAX(cod_matricula),matricula_reclassificacao
											from pmieducar.matricula WHERE ref_cod_aluno = {$this->ref_cod_aluno}
											AND ano=2007 AND ativo=1 AND aprovado IN (1,2)
											group by matricula_reclassificacao";
						$db = new clsBanco();
						$db->Consulta($sql);
						while ($db->ProximoRegistro()) {
							list($cod_matricula,$matricula_reclassificacao) = $db->Tupla();
						}
						if ($matricula_reclassificacao == 1) {
							$descricao_reclassificacao = $this->verificaReclassificacao();
							if ($descricao_reclassificacao) {
								$obj_he = new clsPmieducarHistoricoEscolar($this->ref_cod_aluno,$existe_historico,$pessoa_logada,null,null,null,null,null,null,null,null,$descricao_reclassificacao);
								if (!$obj_he->edita()) {
									die("nao editou reclassificacao");
								}
							}
						}
					}
				}
			}
		}
	}

	function verificaReclassificacao($cod_matricula = false) {
		if (is_numeric($cod_matricula)) {
			$sql_reclassificacao = " AND {$cod_matricula} > cod_matricula ";
		}
		$sql_reclassificacao = "SELECT MAX(cod_matricula), descricao_reclassificacao FROM pmieducar.matricula
								WHERE aprovado = 5 AND ativo = 1 AND ref_cod_aluno = {$this->ref_cod_aluno} AND ano = 2007
								{$sql_reclassificacao}
								group by descricao_reclassificacao";
		$db = new clsBanco();
		$db->Consulta($sql_reclassificacao);
		while ($db->ProximoRegistro()) {
			list($cod_matricula, $descricao_reclassificacao) = $db->Tupla();
			return $descricao_reclassificacao;
		}
		return null;
	}

	function buscaDiasLetivos($ref_cod_escola)
	{
		if (is_numeric($ref_cod_escola)) {
			$obj_calendario = new clsPmieducarEscolaAnoLetivo();
			$lista_calendario = $obj_calendario->lista($ref_cod_escola,2007,null,null,null,null,null,null,null,1,null);

			$totalDiasUteis = 0;
			$total_semanas = 0;


			$obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
			$obj_ano_letivo_modulo->setOrderby("data_inicio asc");

			$lst_ano_letivo_modulo = $obj_ano_letivo_modulo->lista(2007, $ref_cod_escola, null, null);

			if($lst_ano_letivo_modulo)
			{
				$inicio = $lst_ano_letivo_modulo['0'];
				$fim	= $lst_ano_letivo_modulo[count($lst_ano_letivo_modulo) - 1];

				$mes_inicial = explode("-",$inicio['data_inicio']);
				$mes_inicial = $mes_inicial[1];

				$dia_inicial = $mes_inicial[2];

				$mes_final	 = explode("-",$fim['data_fim']);
				$mes_final	 = $mes_final[1];

				$dia_final   = $mes_final[2];
			}



			for ($mes = $mes_inicial;$mes <= $mes_final;$mes++)
			{
				$obj_calendario_dia = new clsPmieducarCalendarioDia();
				$lista_dias = $obj_calendario_dia->lista($calendario['cod_calendario_ano_letivo'],$mes,null,null,null,null,null,null,null,1);

				$dias_mes = array();

				if($lista_dias)
				{
					foreach ($lista_dias as $dia) {
						$obj_motivo = new clsPmieducarCalendarioDiaMotivo($dia['ref_cod_calendario_dia_motivo']);
						$det_motivo = $obj_motivo->detalhe();
						$dias_mes[$dia['dia']] = strtolower($det_motivo['tipo']);
					}
				}
				//Dias previstos do mes

				 // Qual o primeiro dia do mes
				 $primeiroDiaDoMes = mktime(0,0,0,$mes,1,2007);

				 // Quantos dias tem o mes
				 $NumeroDiasMes = date('t',$primeiroDiaDoMes);

				 //informacoes primeiro dia do mes
				 $dateComponents = getdate($primeiroDiaDoMes);

				 // What is the name of the month in question?
				 $NomeMes = $mesesDoAno[$dateComponents['mon']];

				 // What is the index value (0-6) of the first day of the
				 // month in question.
				 $DiaSemana = $dateComponents['wday'];

				 //total de dias uteis + dias extra-letivos - dia nao letivo - fim de semana
				$DiaSemana = 0;

				 if($mes == $mes_inicial)
				 {
				 	$dia_ini = $dia_inicial;
				 }
				 elseif($mes == $mes_final)
				 {
				 	$dia_ini = $dia_final;
				 }
				 else
				 {
				 	$dia_ini = 1;
				 }

				 for($dia = $dia_ini; $dia <= $NumeroDiasMes; $dia++)
				 {
				 	if($DiaSemana >= 7)
				 	{
				 		$DiaSemana = 0;
				 		$total_semanas++;
				 	}

				 	if($DiaSemana != 0 && $DiaSemana != 6){
				 		if(!(key_exists($dia,$dias_mes) && $dias_mes[$dia] == strtolower('n')))
				 			$totalDiasUteis++;
				 	}elseif(key_exists($dia,$dias_mes) && $dias_mes[$dia] == strtolower('e'))
						$totalDiasUteis++;

				 	$DiaSemana++;

				 }


			}
			return $totalDiasUteis;
		}
		return null;
	}

  function addCabecalho($nm_aluno, $cod_matricula, $naturalidade, $sexo,
    $nascimento, $pais, $serie_concluido)
  {
    /**
     * Variável global com objetos do CoreExt.
     * @see includes/bootstrap.php
     */
    global $coreExt;

    // Namespace de configuração do template PDF
    $config = $coreExt['Config']->app->template->pdf;

    // Variável que controla a altura atual das caixas
    $y        = 25;
    $fonte    = 'arial';
    $corTexto = '#000000';

    // Cabeçalho
    $logo = $config->get($config->logo, 'imagens/brasao.gif');

    $this->pdf->quadrado_relativo(20, $y, 555, 85);
    $this->pdf->insertImageScaled('gif', $logo, 40, 85, 41);

    // Título principal
    $titulo = $config->get($config->titulo, 'i-Educar');
    $this->pdf->escreve_relativo($titulo, 25, 29, 400, 80, $fonte, 18,
      $corTexto, 'center');

    // Dados escola
    $this->pdf->escreve_relativo("SECRETARIA DA EDUCAÇÃO", 100, 55, 300, 80,
      $fonte, 9, $corTexto, 'left');
    $this->pdf->escreve_relativo("ESCOLA:{$this->nm_escola}",100, 70, 300,
      80, $fonte, 9, $corTexto, 'left');
    $this->pdf->escreve_relativo("ENDEREÇO:{$this->endereco}",100, 85, 252, 80,
      $fonte, 9, $corTexto, 'left');

    // Carimbo
    $this->pdf->linha_relativa(380,$y,0,85);

    $this->pdf->escreve_relativo("CERTIFICADO DE CONCLUSÃO DE SÉRIE E/OU CURSO DO ENSINO FUNDAMENTAL",
      20, 115, 540, 80, $fonte, 9, $corTexto, 'center' );

    $this->pdf->quadrado_relativo(20, 130, 555, 43);

    $this->pdf->escreve_relativo("Certificamos que {$nm_aluno}{$naturalidade}{$sexo}{$cod_matricula}{$nascimento}{$pais}{$serie_concluido}, conforme Histórico Escolar.",
      30, 135, 525, 100, $fonte, 9, $corTexto, 'justify');

    $this->pdf->escreve_relativo("HISTÓRICO ESCOLAR DO ENSINO FUNDAMENTAL", 20,
      175, 555, 80, $fonte, 9, $corTexto, 'center' );
  }


	function novaLinha($array_valores, $tipo_linha = false)
	{

		$fonte = 'arial';
		$corTexto = '#000000';

		/**
		 * s - serie
		 * n - nota
		 * r - resultado
		 */

		if($tipo_linha == 'n')
		{
			$cor_fundo = $this->cor_fundo = ($this->cor_fundo == "#FFFFFF") ? "#E5E5E5" : "#FFFFFF";
			$altura_linha = 12;
		}
		elseif ($tipo_linha == 'r')
		{
			$cor_fundo = "#FFFFFF";
			$altura_linha = 12;

		}
		elseif ($tipo_linha == 'f')
		{
			$cor_fundo = "#777777";
			$altura_linha = 12;

		}
		else
		{
			$cor_fundo = "#FFFFFF";
			$altura_linha = 20;
		}

		$this->pdf->quadrado_relativo( 20, $this->page_y - 5, 555, $altura_linha,0.1,"#000000","$cor_fundo" );

		if($tipo_linha == 's')
		{
			$this->pdf->escreve_relativo( "DISCIPLINAS",25, $this->page_y, 300, $altura_linha, $fonte, 8, $corTexto, 'left' );
		}
		elseif($tipo_linha == 'f')
		{
			$ajuste = -4;
			$this->pdf->escreve_relativo( "FREQUÊNCIA",25, $this->page_y + $ajuste, 300, $altura_linha, $fonte, 8, $corTexto, 'left' );
		}
		elseif($tipo_linha == 'r')
		{
			$ajuste = -4;
			$this->pdf->escreve_relativo( "RESULTADO FINAL",25, $this->page_y + $ajuste, 300, $altura_linha, $fonte, 8, $corTexto, 'left' );
		}
		else
		{
			$ajuste = -4;
			$nm_disciplina = $array_valores['nm_disciplina'];
			unset($array_valores['nm_disciplina']);
			$this->pdf->escreve_relativo( "{$nm_disciplina}",25, $this->page_y + $ajuste, 300, $altura_linha, $fonte, 8, $corTexto, 'left' );
		}

		$inicio_x = 170;

		$largura_serie = 405 / count($array_valores);

		if($array_valores)
		{
			foreach ($array_valores as $key => $valor)
			{

				if($tipo_linha == 's')
				{
					$ajuste = 0;
					if($largura_serie >= (strlen($valor['nm_serie']) * 4))
					{
						$nm_serie = $valor['nm_serie'];
					}
					else if($posicao_espaco * 4 <= $largura_serie)
					{
						$ajuste = -4;
						$posicao_espaco = strpos($valor['nm_serie'],' ',1);
						$nm_serie = explode(" ",$valor['nm_serie']);
						$nm_serie = "{$nm_serie[0]} {$nm_serie[1]}\n{$nm_serie[2]}";
					}
					else
					{
						$ajuste = -4;
						$posicao_espaco = strpos($valor['nm_serie'],'º ',0);
						if(($posicao_espaco * 4) < $largura_serie)
						{
							$nm_serie = explode("º ",$valor['nm_serie']);
							$nm_serie = implode("º\n",$nm_serie);
						}
					}

					$this->pdf->linha_relativa($inicio_x,$this->page_y - 5,0,20);

					$valor = $nm_serie;

				}
				else
				{

				}


				$this->pdf->escreve_relativo( "$valor",$inicio_x, $this->page_y + $ajuste, $largura_serie, 30, $fonte, 8, $corTexto, 'center' );

				$inicio_x += $largura_serie;
			}
			if($tipo_linha == 'r')
				$this->pdf->linha_relativa(20,$this->page_y - 5,555,0,1.5);
			$this->page_y += $altura_linha;
		}
	}

	function linhaHistorico($array_valores, $is_cabecalho = false)
	{

		$fonte = 'arial';
		$corTexto = '#000000';

		if($is_cabecalho)
		{
			$altura_linha = 20;
			$cor_fundo = "#FFFFFF";
		}
		else
		{
			$ajuste = -4;
			$cor_fundo = $this->cor_fundo = ($this->cor_fundo == "#FFFFFF") ? "#E5E5E5" : "#FFFFFF";
			$altura_linha = 12;
		}

		if($is_cabecalho)
		{
			$array_valores = array('SÉRIE','ANO','RESULT.FINAL','ESTABELECIMENTO DE ENSINO','MUNICÍPIO','UF');
		}

		if(strlen($array_valores[0]) > 19)
		{
			$altura_linha = 19;
		}

		$this->pdf->quadrado_relativo( 20, $this->page_y - 5, 555, $altura_linha,0.1,"#000000","$cor_fundo" );

		$inicio_x = 22;
		$linha_x  = 100;
		$this->pdf->linha_relativa($linha_x,$this->page_y - 5,0,$altura_linha);
		$this->pdf->escreve_relativo( "{$array_valores[0]}",$inicio_x, $this->page_y + $ajuste, 90, 30, $fonte, 8, $corTexto, 'left' );

		$inicio_x += 78;
		$linha_x  += 30;
		$this->pdf->linha_relativa($linha_x,$this->page_y - 5,0,$altura_linha);
		$this->pdf->escreve_relativo( "{$array_valores[1]}",$inicio_x, $this->page_y + $ajuste, 30, 30, $fonte, 8, $corTexto, 'center' );

		$inicio_x += 35;
		$linha_x  += 70;
		$this->pdf->linha_relativa($linha_x,$this->page_y - 5,0,$altura_linha);
		$this->pdf->escreve_relativo( "{$array_valores[2]}",$inicio_x, $this->page_y + $ajuste, 60, 30, $fonte, 8, $corTexto, 'center' );

		$inicio_x += 70;
		$linha_x  += 240;
		$this->pdf->linha_relativa($linha_x,$this->page_y - 5,0,$altura_linha);
		$this->pdf->escreve_relativo( "{$array_valores[3]}",$inicio_x, $this->page_y + $ajuste, 255, 30, $fonte, 8, $corTexto, 'left' );

		$inicio_x += 240;
		$linha_x  += 100;
		$this->pdf->linha_relativa($linha_x,$this->page_y - 5,0,$altura_linha);
		$this->pdf->escreve_relativo( "{$array_valores[4]}",$inicio_x, $this->page_y + $ajuste, 250, 30, $fonte, 8, $corTexto, 'left' );

		$inicio_x += 95;
		$this->pdf->escreve_relativo( "{$array_valores[5]}",$inicio_x, $this->page_y + $ajuste, 30, 30, $fonte, 8, $corTexto, 'center' );

		$this->page_y += $altura_linha;

	}

	function observacao($observacao, $tam_obs = false)
	{
		$fonte = 'arial';
		$corTexto = '#000000';

		if ($tam_obs)
			$altura_obs = $tam_obs;
		else
			$altura_obs = 60;

		$this->pdf->quadrado_relativo( 20, $this->page_y , 555, $altura_obs,0.1,"#000000","#FFFFFF" );
		$this->pdf->escreve_relativo( "OBS: $observacao",23, $this->page_y + 3 , 545, 150, $fonte, 8, $corTexto, 'justify' );
		$this->page_y += $altura_obs;
	}

	function rodape($cidade)
	{
		$fonte = 'arial';
		$corTexto = '#000000';
		$this->page_y += 10;
		$this->pdf->quadrado_relativo( 20, $this->page_y , 555, 30,0.1,"#000000","#FFFFFF" );
		$this->pdf->escreve_relativo( "Município e Data",23, $this->page_y , 80, 555, $fonte, 8, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "{$cidade}",25, $this->page_y + 17, 250, 555, $fonte, 8, $corTexto, 'left' );
		$this->pdf->escreve_relativo( date("d/m/Y"),150, $this->page_y + 17, 250, 555, $fonte, 8, $corTexto, 'left' );
		$this->pdf->linha_relativa(200,$this->page_y,0,30);
		$this->pdf->escreve_relativo( "Secretário(a)",205, $this->page_y , 80, 555, $fonte, 8, $corTexto, 'left' );
		$this->pdf->linha_relativa(390,$this->page_y,0,30);
		$this->pdf->escreve_relativo( "Diretor(a)",395, $this->page_y , 80, 555, $fonte, 8, $corTexto, 'left' );
	}

	function Editar()
	{
		return false;
	}

	function Excluir()
	{
		return false;
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
