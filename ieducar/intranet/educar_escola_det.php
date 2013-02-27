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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Escola" );
		$this->processoAp = "561";
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

	var $cod_escola;
	var $ref_usuario_cad;
	var $ref_usuario_exc;
	var $ref_cod_instituicao;
	var $ref_cod_escola_localizacao;
	var $ref_cod_escola_rede_ensino;
	var $ref_idpes;
	var $sigla;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $nm_escola;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Escola - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_escola = $_GET["cod_escola"];

		$tmp_obj = new clsPmieducarEscola( $this->cod_escola );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_escola_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarInstituicao" ) )
		{
			$obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
			$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
			$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
		}
		else
		{
			$registro["ref_cod_instituicao"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarInstituicao\n-->";
		}
		if ($registro["ref_idpes"])
		{
			$obj_escola = new clsPessoa_($registro["ref_idpes"]);
			$obj_escola_det = $obj_escola->detalhe();
			$url = $obj_escola_det["url"];
			$email = $obj_escola_det["email"];
			$obj_escola1 = new clsPessoaJuridica($registro["ref_idpes"]);
			$obj_escola_det1 = $obj_escola1->detalhe();
			$nm_escola = $obj_escola_det1["fantasia"];

			$obj_endereco = new clsPessoaEndereco($registro["ref_idpes"]);
			if ( class_exists( "clsPessoaEndereco" ) )
			{
				$tipo = 1;
				$endereco_lst = $obj_endereco->lista($registro["ref_idpes"]);
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
						$tipo_logradouro = $idtlog["descricao"];
						$idbai = $endereco["idbai"]->detalhe();
						$idbai = $idbai['nome'];
						$numero = $endereco["numero"];
						$complemento = $endereco["complemento"];
						$andar = $endereco["andar"];
					}
				}
				else if ( class_exists( "clsEnderecoExterno" ) )
				{
					$tipo = 2;
					$obj_endereco = new clsEnderecoExterno();
					$endereco_lst = $obj_endereco->lista( null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $registro["ref_idpes"] );
					if ( $endereco_lst )
					{
						foreach ($endereco_lst as $endereco)
						{
							$cep = $endereco["cep"];
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
						}
					}
				}
			}
			if ( class_exists( "clsPessoaTelefone" ) )
			{
				$obj_telefone = new clsPessoaTelefone();
				$telefone_lst = $obj_telefone->lista($registro["ref_idpes"], "tipo");
				if ($telefone_lst)
				{
					foreach ($telefone_lst as $telefone)
					{
						if ($telefone["tipo"] == 1 )
						{
							$ddd_telefone_1 = $telefone["ddd"];
							$telefone_1 = $telefone["fone"];
						}
						else if ($telefone["tipo"] == 2 )
						{
							$ddd_telefone_2 = $telefone["ddd"];
							$telefone_2 = $telefone["fone"];
						}
						else if ($telefone["tipo"] == 4 )
						{
							$ddd_telefone_fax = $telefone["ddd"];
							$telefone_fax = $telefone["fone"];
						}
					}
				}
			}
		}
		else
		{
			if ( class_exists( "clsPmieducarEscolaComplemento" ) )
			{
				$tipo= 3;
				$obj_escola = new clsPmieducarEscolaComplemento($this->cod_escola);
				$obj_escola_det = $obj_escola->detalhe();
				$nm_escola = $obj_escola_det["nm_escola"];
				$cep = $obj_escola_det["cep"];
				$numero = $obj_escola_det["numero"];
				$complemento = $obj_escola_det["complemento"];
				$email = $obj_escola_det["email"];
				$cidade = $obj_escola_det["municipio"];
				$bairro = $obj_escola_det["bairro"];
				$logradouro = $obj_escola_det["logradouro"];
				$ddd_telefone = $obj_escola_det["ddd_telefone"];
				$telefone = $obj_escola_det["telefone"];
				$ddd_telefone_fax = $obj_escola_det["ddd_fax"];
				$telefone_fax = $obj_escola_det["fax"];
			}
		}

		if( class_exists( "clsPmieducarEscolaLocalizacao" ) )
		{
			$obj_ref_cod_escola_localizacao = new clsPmieducarEscolaLocalizacao( $registro["ref_cod_escola_localizacao"] );
			$det_ref_cod_escola_localizacao = $obj_ref_cod_escola_localizacao->detalhe();
			$registro["ref_cod_escola_localizacao"] = $det_ref_cod_escola_localizacao["nm_localizacao"];
		}
		else
		{
			$registro["ref_cod_escola_localizacao"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarEscolaLocalizacao\n-->";
		}

		if( class_exists( "clsPmieducarEscolaRedeEnsino" ) )
		{
			$obj_ref_cod_escola_rede_ensino = new clsPmieducarEscolaRedeEnsino( $registro["ref_cod_escola_rede_ensino"] );
			$det_ref_cod_escola_rede_ensino = $obj_ref_cod_escola_rede_ensino->detalhe();
			$registro["ref_cod_escola_rede_ensino"] = $det_ref_cod_escola_rede_ensino["nm_rede"];
		}
		else
		{
			$registro["ref_cod_escola_rede_ensino"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarEscolaRedeEnsino\n-->";
		}

		if( class_exists( "clsPessoaJuridica" ) )
		{
			$obj_ref_idpes = new clsPessoaJuridica( $registro["ref_idpes"] );
			$det_ref_idpes = $obj_ref_idpes->detalhe();
			$registro["ref_idpes"] = $det_ref_idpes["nome"];
		}
		else
		{
			$registro["ref_idpes"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsCadastroJuridica\n-->";
		}

		if( $registro["ref_cod_instituicao"] )
		{
			$this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
		}
		if( $nm_escola )
		{
			$this->addDetalhe( array( "Escola", "{$nm_escola}") );
		}
		if( $registro["sigla"] )
		{
			$this->addDetalhe( array( "Sigla", "{$registro["sigla"]}") );
		}
		if( $registro["ref_cod_escola_localizacao"] )
		{
			$this->addDetalhe( array( "Localiza&ccedil;&atilde;o", "{$registro["ref_cod_escola_localizacao"]}") );
		}
		if( $registro["ref_cod_escola_rede_ensino"] )
		{
			$this->addDetalhe( array( "Rede Ensino", "{$registro["ref_cod_escola_rede_ensino"]}") );
		}
		if( $registro["ref_idpes"] )
		{
			$this->addDetalhe( array( "Raz&atilde;o Social", "{$registro["ref_idpes"]}") );
		}
		if ($tipo == 1)
		{
			if( $cep )
			{
				$cep = int2CEP($cep);
				$this->addDetalhe( array( "CEP", "{$cep}") );
			}
			if( $idbai )
			{
				$this->addDetalhe( array( "Bairro", "{$idbai}") );
			}
			if( $tipo_logradouro )
			{
				$this->addDetalhe( array( "Tipo Logradouro", "{$tipo_logradouro}") );
			}
			if( $logradouro )
			{
				$this->addDetalhe( array( "Logradouro", "{$logradouro}") );
			}
			if( $complemento )
			{
				$this->addDetalhe( array( "Complemento", "{$complemento}") );
			}
			if( $numero )
			{
				$this->addDetalhe( array( "N&uacute;mero", "{$numero}") );
			}
			if( $andar )
			{
				$this->addDetalhe( array( "Andar", "{$andar}") );
			}
			if( $url )
			{
				$this->addDetalhe( array( "Site", "{$url}") );
			}
			if( $email )
			{
				$this->addDetalhe( array( "E-mail", "{$email}") );
			}
			if( $ddd_telefone_1 )
			{
				$this->addDetalhe( array( "DDD Telefone 1", "{$ddd_telefone_1}") );
			}
			if( $telefone_1 )
			{
				$this->addDetalhe( array( "Telefone 1", "{$telefone_1}") );
			}
			if( $ddd_telefone_2 )
			{
				$this->addDetalhe( array( "DDD Telefone 2", "{$ddd_telefone_2}") );
			}
			if( $telefone_2 )
			{
				$this->addDetalhe( array( "Telefone 2", "{$telefone_2}") );
			}
			if( $ddd_telefone_fax )
			{
				$this->addDetalhe( array( "DDD Fax", "{$ddd_telefone_fax}") );
			}
			if( $telefone_fax )
			{
				$this->addDetalhe( array( "Fax", "{$telefone_fax}") );
			}
		}
		else if ($tipo == 2)
		{
			if( $cep )
			{
				$cep = int2CEP($cep);
				$this->addDetalhe( array( "CEP", "{$cep}") );
			}
			if( $sigla_uf )
			{
				$this->addDetalhe( array( "Estado", "{$sigla_uf}") );
			}
			if( $cidade )
			{
				$this->addDetalhe( array( "Cidade", "{$cidade}") );
			}
			if( $bairro )
			{
				$this->addDetalhe( array( "Bairro", "{$bairro}") );
			}
			if( $tipo_logradouro )
			{
				$this->addDetalhe( array( "Tipo Logradouro", "{$tipo_logradouro}") );
			}
			if( $logradouro )
			{
				$this->addDetalhe( array( "Logradouro", "{$logradouro}") );
			}
			if( $complemento )
			{
				$this->addDetalhe( array( "Complemento", "{$complemento}") );
			}
			if( $numero )
			{
				$this->addDetalhe( array( "N&uacute;mero", "{$numero}") );
			}
			if( $andar )
			{
				$this->addDetalhe( array( "Andar", "{$andar}") );
			}
			if( $url )
			{
				$this->addDetalhe( array( "Site", "{$url}") );
			}
			if( $email )
			{
				$this->addDetalhe( array( "E-mail", "{$email}") );
			}
			if( $ddd_telefone_1 )
			{
				$this->addDetalhe( array( "DDD Telefone 1", "{$ddd_telefone_1}") );
			}
			if( $telefone_1 )
			{
				$this->addDetalhe( array( "Telefone 1", "{$telefone_1}") );
			}
			if( $ddd_telefone_2 )
			{
				$this->addDetalhe( array( "DDD Telefone 2", "{$ddd_telefone_2}") );
			}
			if( $telefone_2 )
			{
				$this->addDetalhe( array( "Telefone 2", "{$telefone_2}") );
			}
			if( $ddd_telefone_fax )
			{
				$this->addDetalhe( array( "DDD Fax", "{$ddd_telefone_fax}") );
			}
			if( $telefone_fax )
			{
				$this->addDetalhe( array( "Fax", "{$telefone_fax}") );
			}

		}
		else if ($tipo == 3)
		{
			if( $cep )
			{
				$cep = int2CEP($cep);
				$this->addDetalhe( array( "CEP", "{$cep}") );
			}
			if( $cidade )
			{
				$this->addDetalhe( array( "Cidade", "{$cidade}") );
			}
			if( $bairro )
			{
				$this->addDetalhe( array( "Bairro", "{$bairro}") );
			}
			if( $logradouro )
			{
				$this->addDetalhe( array( "Logradouro", "{$logradouro}") );
			}
			if( $complemento )
			{
				$this->addDetalhe( array( "Complemento", "{$complemento}") );
			}
			if( $numero )
			{
				$this->addDetalhe( array( "N&uacute;mero", "{$numero}") );
			}
			if( $email )
			{
				$this->addDetalhe( array( "E-mail", "{$email}") );
			}
			if( $ddd_telefone )
			{
				$this->addDetalhe( array( "DDD Telefone", "{$ddd_telefone}") );
			}
			if( $telefone )
			{
				$this->addDetalhe( array( "Telefone", "{$telefone}") );
			}
			if( $ddd_telefone_fax )
			{
				$this->addDetalhe( array( "DDD Fax", "{$ddd_telefone_fax}") );
			}
			if( $telefone_fax )
			{
				$this->addDetalhe( array( "Fax", "{$telefone_fax}") );
			}
		}

		$obj = new clspmieducarescolacurso();
		$lst = $obj->lista( $this->cod_escola );
		if ($lst) {

			$tabela = "<table>
					       <tr align='center'>
					           <td bgcolor='#a1b3bd'><b>nome</b></td>
					       </tr>";
			$cont = 0;

			foreach ( $lst as $valor ) {
				if ( ($cont % 2) == 0 ) {
					$color = " bgcolor='#e4e9ed' ";
				}
				else {
					$color = " bgcolor='#ffffff' ";
				}
				$obj_curso = new clspmieducarcurso( $valor["ref_cod_curso"] );
				$obj_curso->setorderby("nm_curso asc");
				$obj_curso_det = $obj_curso->detalhe();
				$nm_curso = $obj_curso_det["nm_curso"];

				$tabela .= "<tr>
							    <td {$color} align=left>{$nm_curso}</td>
							</tr>";
				$cont++;
			}
			$tabela .= "</table>";
		}
		if( $nm_curso )
		{
			$this->addDetalhe( array( "Curso", "{$tabela}") );
		}

		if( $tabela = $this->listaAnos() ) {
			$this->addDetalhe( array( "-", "{$tabela}") );
		}

		$obj_permissoes = new clsPermissoes();

		$canCreate = $obj_permissoes->permissao_cadastra( 561, $this->pessoa_logada, 3 );
		$canEdit   = $obj_permissoes->permissao_cadastra( 561, $this->pessoa_logada, 7 );

  	if($canCreate)
			$this->url_novo = "educar_escola_cad.php";

		if($canEdit) {
			$this->url_editar      = "educar_escola_cad.php?cod_escola={$registro["cod_escola"]}";
			$this->array_botao     = array ("Definir Ano Letivo");
			$this->array_botao_url = array ("educar_escola_ano_letivo_cad.php?cod_escola={$registro["cod_escola"]}");
		}

		$this->url_cancelar = "educar_escola_lst.php";
		$this->largura      = "100%";
	}

	//***
	// Inicio listagem anos letivos
	//***
	function listaAnos()
	{

		if(!$this->cod_escola)
			return false;

		$existe  = false;

		$obj_ano_letivo = new clsPmieducarEscolaAnoLetivo();
		$obj_ano_letivo->setOrderby("ano");
		$lista_ano_letivo = $obj_ano_letivo->lista($this->cod_escola,null,null,null,null,null,null,null,null,1);

		$tabela = "<table border=0 cellpadding=2 width='100%'>";

		if($lista_ano_letivo)
		{
			//echo'<pre>';
			//print_r($lista_ano_letivo);

			$existe  = true;
			$tabela .= "<tr bgcolor=$cor><td colspan='2'><b>Anos letivos</b></td></tr><tr><td>";
			$tabela .= "<table cellpadding=\"2\" cellspacing=\"2\" border=\"0\" align=\"left\" width='60%'>";
			$tabela .= "<tr bgcolor='#A1B3BD'><th width='90'>Ano<a name='ano_letivo'/></th><th width='70'>Iniciar</th><th width='70'>Finalizar</th><th width='150'>Editar</th></tr>";
			$cor = $cor == "#FFFFFF" ? "#E4E9ED" : "#FFFFFF";

			$obj_ano_letivo = new clsPmieducarEscolaAnoLetivo();
			$existe_ano_andamento = $obj_ano_letivo->lista($this->cod_escola,null,null,null,1,null,null,null,null,1);

			foreach ($lista_ano_letivo as $ano)
			{

				$incluir = $excluir = "";
				//se nao existe ano em andamento permite inicializar um ano letivo
				if(!$existe_ano_andamento && $ano['andamento'] != 2)
					$incluir = "<td><a href='#' onclick=\"preencheForm('{$ano['ano']}','{$ano['ref_cod_escola']}','iniciar');\"><img src=\"imagens/i-educar/nvp_bot_iniciar_ano.gif\" border=0 style='padding-left:10px;'></a></td>";
				else
					$incluir = "<td width='130'>&nbsp;</td>";

				//verifica se o ano nao possui matricula em andamento para permitir finalizar o ano
				$obj_matricula_ano = new clsPmieducarMatricula();
				$matricula_em_andamento = $obj_matricula_ano->lista(null,null,$this->cod_escola,null,null,null,null,3,null,null,null,null,1,$ano['ano'],null,null,1,null,1,null,null,null,null,null,null,false);
				if(!$matricula_em_andamento && $existe_ano_andamento && $ano['andamento'] == 1)
					$excluir = "<td><a href='#' onclick=\"preencheForm('{$ano['ano']}','{$ano['ref_cod_escola']}','finalizar');\" ><img src=\"imagens/i-educar/nvp_bot_finalizar_ano.gif\" border=0 style='padding-left:10px;'></a></td>";
				else
				{

					$excluir = "<td width='130'>&nbsp;</td>";
				}

				$editar = "";//"<td align='center'> - </td>";

				if($ano['andamento'] == 2)
					$incluir = "<td colspan='3' align='center'><span class='formlttd'><b>--- Ano Finalizado ---</b></span></td>";
				else
					$editar = "<td><a href='#' onclick=\"preencheForm('{$ano['ano']}','{$ano['ref_cod_escola']}','editar');\" ><img src=\"imagens/i-educar/nvp_bot_editar_ano.gif\" alt=\"Editar Ano Letivo\" border=0 style='padding-left:10px;'></a></td>";

				$tabela .= "<tr bgcolor='$cor'><td style='padding-left:20px'><img src=\"imagens/noticia.jpg\" border='0'> {$ano['ano']}</td>{$incluir}{$excluir}{$editar}</tr>";
			}

			$tabela .= "</table></td></tr>";
			$tabela .= "<tr>
							<td>
								<span class='formlttd'><b>*Somente &eacute; poss&iacute;vel finalizar um ano letivo ap&oacute;s n&atilde;o existir mais nenhuma matr&iacute;cula em andamento.</b></span>
							</td>
						</tr>";
			$tabela .= "<tr>
							<td>
								<form name='acao_ano_letivo' action='educar_iniciar_ano_letivo.php' method='post'>
									<input type='hidden' name='ano' id='ano'>
									<input type='hidden' name='ref_cod_escola' id='ref_cod_escola'>
									<input type='hidden' name='tipo_acao' id='tipo_acao'>
								</form>
							</td>
						</tr>";
		}

		$tabela .="</table>";

		return $existe == true ?  $tabela :  false;
	}
	//***
	// Fim listagem anos letivos
	//***
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
<script>
function preencheForm(ano, escola,acao){


		if(!confirm('Deseja realmente \'' + acao.substr(0, 1).toUpperCase() + acao.substr(1) + '\' o ano letivo?'))
			return false;
	document.acao_ano_letivo.ano.value 			  = ano;
	document.acao_ano_letivo.ref_cod_escola.value = escola;
	document.acao_ano_letivo.tipo_acao.value      = acao;
	document.acao_ano_letivo.submit();

}

</script>