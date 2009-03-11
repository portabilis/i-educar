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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Servidor" );
		$this->processoAp = "635";
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

	var $cod_servidor;
	var $ref_cod_deficiencia;
	var $ref_idesco;
	var $ref_cod_funcao;
	var $carga_horaria;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	var $ref_cod_instituicao;

	var $alocacao_array = array();

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Servidor - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_servidor=$_GET["cod_servidor"];
		$this->ref_cod_instituicao=$_GET["ref_cod_instituicao"];

		$tmp_obj = new clsPmieducarServidor( $this->cod_servidor,null,null,null,null,null,null,$this->ref_cod_instituicao );
		$registro = $tmp_obj->detalhe();

		if( !$registro )
		{
			header( "location: educar_servidor_lst.php" );
			die();
		}

		if( class_exists( "clsCadastroDeficiencia" ) )
		{
			$obj_ref_cod_deficiencia = new clsCadastroDeficiencia( $registro["ref_cod_deficiencia"] );
			$det_ref_cod_deficiencia = $obj_ref_cod_deficiencia->detalhe();
			$registro["ref_cod_deficiencia"] = $det_ref_cod_deficiencia["nm_deficiencia"];
		}
		else
		{
			$registro["ref_cod_deficiencia"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsCadastroDeficiencia\n-->";
		}

		if( class_exists( "clsCadastroEscolaridade" ) )
		{
			$obj_ref_idesco = new clsCadastroEscolaridade( $registro["ref_idesco"] );
			$det_ref_idesco = $obj_ref_idesco->detalhe();
			$registro["ref_idesco"] = $det_ref_idesco["descricao"];
		}
		else
		{
			$registro["ref_idesco"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsCadastroEscolaridade\n-->";
		}

		if( class_exists( "clsPmieducarFuncao" ) )
		{
			$obj_ref_cod_funcao = new clsPmieducarFuncao( $registro["ref_cod_funcao"],null,null,null,null,null,null,null,null,$this->ref_cod_instituicao );
			$det_ref_cod_funcao = $obj_ref_cod_funcao->detalhe();
			$registro["ref_cod_funcao"] = $det_ref_cod_funcao["nm_funcao"];
		}
		else
		{
			$registro["ref_cod_funcao"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarFuncao\n-->";
		}

		if( class_exists( "clsFuncionario" ) )
		{
			$obj_cod_servidor = new clsFuncionario( $registro["cod_servidor"] );
			$det_cod_servidor = $obj_cod_servidor->detalhe();
			$registro["matricula"] = $det_cod_servidor["matricula"];
			$det_cod_servidor = $det_cod_servidor["idpes"]->detalhe();
			$registro["nome"] = $det_cod_servidor["nome"];

		}
		else
		{
			$registro["cod_servidor"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsFuncionario\n-->";
		}

		if( class_exists( "clsPmieducarInstituicao" ) )
		{
			$obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
			$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
			$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
		}
		else
		{
			$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
		}

		$obj = new clsPmieducarServidorAlocacao( );
		$obj->setOrderby("periodo,carga_horaria");
		$lista  = $obj->lista(null,$this->ref_cod_instituicao,null,null,null,$this->cod_servidor,null,null,null,null,null,null,null,null,null,1);
		if( $lista )
		{
			foreach( $lista AS $campo => $val ){	// passa todos os valores obtidos no registro para atributos do objeto
				$temp = array();
				$temp['carga_horaria'] = $val['carga_horaria'];
				$temp['periodo'] = $val['periodo'];

				$obj_escola = new clsPmieducarEscola($val['ref_cod_escola']);
				$det_escola = $obj_escola->detalhe();
				$det_escola = $det_escola["nome"];
				$temp['ref_cod_escola'] = $det_escola;

				$this->alocacao_array[] = $temp;

			}


		}

		if( $registro["cod_servidor"] )
		{
			$this->addDetalhe( array( "Servidor", "{$registro["cod_servidor"]}") );
		}
		if( $registro["matricula"] )
		{
			$this->addDetalhe( array( "Matr&iacute;cula", "{$registro["matricula"]}") );
		}
		if( $registro["nome"] )
		{
			$this->addDetalhe( array( "Nome", "{$registro["nome"]}") );
		}
		if( $registro["ref_cod_instituicao"] )
		{
			$this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
		}
		if( $registro["ref_cod_deficiencia"] )
		{
			$this->addDetalhe( array( "Defici&eacute;ncia", "{$registro["ref_cod_deficiencia"]}") );
		}
		if( $registro["ref_idesco"] )
		{
			$this->addDetalhe( array( "Escolaridade", "{$registro["ref_idesco"]}") );
		}
		if( $registro["ref_cod_subnivel"] )
		{
			$obj_nivel = new clsPmieducarSubnivel($registro["ref_cod_subnivel"]);
			$det_nivel = $obj_nivel->detalhe();

			$this->addDetalhe( array( "Nível", "{$det_nivel["nm_subnivel"]}") );
		}

		if( $registro["ref_cod_funcao"] )
		{
			$this->addDetalhe( array( "Fun&ccedil;&atilde;o", "{$registro["ref_cod_funcao"]}") );
		}

		$obj_funcao = new clsPmieducarServidorFuncao();
		$lst_funcao = $obj_funcao->lista($this->ref_cod_instituicao,$this->cod_servidor);


		if( $lst_funcao )
		{
			$tabela .= "<table cellspacing='0' cellpadding='0' border='0'>
							<tr bgcolor='#A1B3BD' align='center'>
								<td width='150'>Fun&ccedil;&atilde;o</td>
							</tr>";

			$class = "formlttd";

			$tab_disc = null;

			$obj_disciplina_servidor = new clsPmieducarServidorDisciplina();
			$lst_disciplina_servidor = $obj_disciplina_servidor->lista(null,$this->ref_cod_instituicao,$this->cod_servidor);

			if($lst_disciplina_servidor)
			{
				$tab_disc .= "<table cellspacing='0' cellpadding='0' width='200' border='0' style='border:1px dotted #000000'>";

				$class2 = $class2 == "formlttd" ? "formmdtd" : "formlttd" ;
				$tab_disc .= " <tr>
									<td bgcolor='#A1B3BD' align='center'>Disciplinas</td>
								</tr>";
				foreach ($lst_disciplina_servidor as $disciplina)
				{
					$obj_disciplina = new clsPmieducarDisciplina($disciplina['ref_cod_disciplina']);
					$det_disciplina = $obj_disciplina->detalhe();

					$tab_disc .= " <tr class='$class2' align='center'>
									<td align='left'>{$det_disciplina['nm_disciplina']}</td>
								</tr>";

					$class2 = $class2 == "formlttd" ? "formmdtd" : "formlttd" ;

				}
				$tab_disc .=	"</table>";
			}

			$obj_servidor_curso = new clsPmieducarServidorCursoMinistra();
			$lst_servidor_curso = $obj_servidor_curso->lista(null,$this->ref_cod_instituicao,$this->cod_servidor);

			if($lst_servidor_curso)
			{
				$tab_curso .= "<table cellspacing='0' cellpadding='0' width='200' border='0' style='border:1px dotted #000000'>";

				$class2 = $class2 == "formlttd" ? "formmdtd" : "formlttd" ;
				$tab_curso .= " <tr>
									<td bgcolor='#A1B3BD' align='center'>Cursos Ministrados</td>
								</tr>";
				foreach ($lst_servidor_curso as $curso)
				{
					$obj_curso = new clsPmieducarCurso($curso['ref_cod_curso']);
					$det_curso = $obj_curso->detalhe();

					$tab_curso .= " <tr class='$class2' align='center'>
									<td align='left'>{$det_curso['nm_curso']}</td>
								</tr>";

					$class2 = $class2 == "formlttd" ? "formmdtd" : "formlttd" ;

				}
				$tab_curso .=	"</table>";
			}

			foreach ($lst_funcao as $funcao)
			{

				$obj_funcao = new clsPmieducarFuncao($funcao['ref_cod_funcao']);
				$det_funcao = $obj_funcao->detalhe();

				$tabela .= " <tr class='$class' align='left'>
								<td><b>{$det_funcao['nm_funcao']}</b></td>
							</tr>";

				$class = $class == "formlttd" ? "formmdtd" : "formlttd" ;
			}

			if($tab_curso)
			{

				$tabela .= " <tr class='$class' align='center'>
								<td style='padding:5px'>$tab_curso</td>
							</tr>";
			}

			if($tab_disc)
			{

				$tabela .= " <tr class='$class' align='center'>
								<td style='padding:5px'>$tab_disc</td>
							</tr>";
			}

			$tabela .=	"</table>";

			$this->addDetalhe(array("Fun&ccedil;&atilde;o", "<a href='javascript:trocaDisplay(\"det_f\");' >Mostrar detalhe</a><div id='det_f' name='det_f' style='display:none;'>".$tabela."</div>"));
		}

		$tabela = null;

		if( $registro["carga_horaria"] )
		{

			$this->addDetalhe( array( "Carga Hor&aacute;ria", "{$registro["carga_horaria"]}"));
		}

		$dias_da_semana = array( '' => 'Selecione', 1 => 'Domingo', 2 => 'Segunda', 3 => 'Ter&ccedil;a', 4 => 'Quarta', 5 => 'Quinta', 6 => 'Sexta', 7 => 'S&aacute;bado' );

		if( $this->alocacao_array )
		{
			$tabela .= "<table cellspacing='0' cellpadding='0' border='0'>
							<tr bgcolor='#A1B3BD' align='center'>
								<td width='150'>Carga Horaria</td>
								<td width='80'>Periodo</td>
								<td width='150'>Escola</td>
							</tr>";

			$class = "formlttd";
			foreach ($this->alocacao_array as $alocacao)
			{
				switch ( $alocacao['periodo'] )
				{
					case 1:
						$nm_periodo = "Matutino";
						break;
					case 2:
						$nm_periodo = "Vespertino";
						break;
					case 3:
						$nm_periodo = "Noturno";
						break;
				}
			$tabela .= " <tr class='$class' align='center'>
							<td>{$alocacao['carga_horaria']}</td>
							<td>{$nm_periodo}</td>
							<td>{$alocacao['ref_cod_escola']}</td>
						</tr>";
			$class = $class == "formlttd" ? "formmdtd" : "formlttd" ;
			}
			$tabela .=	"</table>";

			$this->addDetalhe(array("Hor&aacute;rios de trabalho", "<a href='javascript:trocaDisplay(\"det_pree\");' >Mostrar detalhe</a><div id='det_pree' name='det_pree' style='display:none;'>".$tabela."</div>"));
		}

		$obj_permissoes = new clsPermissoes();

		if( $obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 3 ) )
		{
			$this->url_novo = "educar_servidor_cad.php";
			$this->url_editar = "educar_servidor_cad.php?cod_servidor={$registro["cod_servidor"]}&ref_cod_instituicao={$this->ref_cod_instituicao}";

			//	VERIFICAR SE ESTA AFASTADO FICA RETORNAR
			//  VERIFICAR SE ESTA Alocar Servidor FICA SUBSTITUIR

			/**
			 * @todo   O botão [Alocar Servidor] só deverá aparecer para usuários
			 * poli-institucionais e institucionais apenas quando o servidor
			 * nâo estiver com todas as horas semanais alocadas.
			 */
			$get_padrao ="ref_cod_servidor={$registro["cod_servidor"]}&ref_cod_instituicao={$this->ref_cod_instituicao}";

			$this->array_botao = array();
			$this->array_botao_url_script = array();

			$this->array_botao[] = 'Avalia&ccedil;&atilde;o de Desempenho';
			$this->array_botao_url_script[] = "go(\"educar_avaliacao_desempenho_lst.php?{$get_padrao}\");";

			$this->array_botao[] = 'Forma&ccedil;&atilde;o';
			$this->array_botao_url_script[] = "go(\"educar_servidor_formacao_lst.php?{$get_padrao}\");";

			$this->array_botao[] = 'Faltas/Atrasos';
			$this->array_botao_url_script[] = "go(\"educar_falta_atraso_lst.php?{$get_padrao}\");";

			$this->array_botao[] =  'Alocar Servidor';
			$this->array_botao_url_script[] = "go(\"educar_servidor_alocacao_cad.php?{$get_padrao}\");";

			$this->array_botao[] =  'Alterar Nível';
			$this->array_botao_url_script[] = "popless();";

			$obj_servidor_alocacao = new clsPmieducarServidorAlocacao();
			$lista_alocacao = $obj_servidor_alocacao->lista(null,$this->ref_cod_instituicao,null,null,null,$this->cod_servidor,null,null,null,null,null,null,null,null,null,1);

			if($lista)
			{
				$this->array_botao[] =  'Substituir Hor&aacute;rio Servidor';
				$this->array_botao_url_script[] = "go(\"educar_servidor_substituicao_cad.php?{$get_padrao}\");";
			}

			if ( class_exists( "clsPmieducarServidorAfastamento" ) ) {
				$obj_afastamento = new clsPmieducarServidorAfastamento();
				$afastamento	 = $obj_afastamento->afastado( $this->cod_servidor, $this->ref_cod_instituicao );

				if ( is_numeric( $afastamento ) && $afastamento == 0 ) {
					$this->array_botao[] =  'Afastar Servidor';
					$this->array_botao_url_script[] = "go(\"educar_servidor_afastamento_cad.php?{$get_padrao}\");";
				}
				elseif ( is_numeric( $afastamento ) ) {
					$this->array_botao[] =  'Retornar Servidor';
					$this->array_botao_url_script[] = "go(\"educar_servidor_afastamento_cad.php?{$get_padrao}&sequencial={$afastamento}\");";
				}
			}

			//	$this->array_botao = array('Avalia&ccedil;&atilde;o de Desempenho', 'Forma&ccedil;&atilde;o', 'Faltas/Atrasos', 'Alocar Servidor' , 'Substituir', 'Afastar', 'Retornar','substituto');



			//$this->array_botao_url = array("educar_avaliacao_desempenho_lst.php?{$get_padrao}", "educar_servidor_formacao_lst.php?{$get_padrao}", "educar_falta_atraso_lst.php?{$get_padrao}", "educar_servidor_alocacao_cad.php?{$get_padrao}","educar_servidor_substituicao_cad.php?{$get_padrao}", 'Afastar', 'Retornar');

		}

		$this->url_cancelar = "educar_servidor_lst.php";
		$this->largura = "100%";
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
<script>

function trocaDisplay(id)
{
	var element = document.getElementById(id);
  	element.style.display = (element.style.display == "none") ? "inline" : "none";
}

function popless()
{
	var campoServidor = <?=$_GET["cod_servidor"];?>;
	var campoInstituicao = <?=$_GET["ref_cod_instituicao"];?>;
	pesquisa_valores_popless('educar_servidor_nivel_cad.php?ref_cod_servidor='+campoServidor+'&ref_cod_instituicao='+campoInstituicao, '');
}


</script>