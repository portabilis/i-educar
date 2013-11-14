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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Hist&oacute;rico Escolar" );
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

	var $ref_cod_aluno;
	var $sequencial;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ano;
	var $carga_horaria;
	var $dias_letivos;
	var $escola;
	var $escola_cidade;
	var $escola_uf;
	var $observacao;
	var $aprovado;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	var $ref_cod_instituicao;
	var $nm_serie;
	var $origem;
	var $extra_curricular;
	var $ref_cod_matricula;
	var $frequencia;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Hist&oacute;rico Escolar - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->sequencial=$_GET["sequencial"];
		$this->ref_cod_aluno=$_GET["ref_cod_aluno"];

		$tmp_obj = new clsPmieducarHistoricoEscolar( $this->ref_cod_aluno, $this->sequencial );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_historico_escolar_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );
			die();
		}

		if( class_exists( "clsPmieducarAluno" ) )
		{
			$obj_aluno = new clsPmieducarAluno();
			$lst_aluno = $obj_aluno->lista( $registro["ref_cod_aluno"],null,null,null,null,null,null,null,null,null,1 );
			if ( is_array($lst_aluno) )
			{
				$det_aluno = array_shift($lst_aluno);
				$nm_aluno = $det_aluno["nome_aluno"];
			}
		}
		else
		{
			$nm_aluno = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarAluno\n-->";
		}


		if( $nm_aluno )
		{
			$this->addDetalhe( array( "Aluno", "{$nm_aluno}") );
		}
//		if( $registro["sequencial"] )
//		{
//			$this->addDetalhe( array( "Sequencial", "{$registro["sequencial"]}") );
//		}

		if($registro["extra_curricular"])
		{
			if( $registro["escola"] )
			{
				$this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["escola"]}") );
			}
			if( $registro["escola_cidade"] )
			{
				$this->addDetalhe( array( "Cidade da Institui&ccedil;&atilde;o", "{$registro["escola_cidade"]}") );
			}
			if( $registro["escola_uf"] )
			{
				$this->addDetalhe( array( "Estado da Institui&ccedil;&atilde;o", "{$registro["escola_uf"]}") );
			}
			if( $registro["nm_serie"] )
			{
				$this->addDetalhe( array( "Série", "{$registro["nm_serie"]}") );
			}
		}
		else
		{
			if( $registro["escola"] )
			{
				$this->addDetalhe( array( "Escola", "{$registro["escola"]}") );
			}
			if( $registro["escola_cidade"] )
			{
				$this->addDetalhe( array( "Cidade da Escola", "{$registro["escola_cidade"]}") );
			}
			if( $registro["escola_uf"] )
			{
				$this->addDetalhe( array( "Estado da Escola", "{$registro["escola_uf"]}") );
			}
			if( $registro["nm_serie"] )
			{
				$this->addDetalhe( array( "S&eacute;rie", "{$registro["nm_serie"]}") );
			}
		}

		if( $registro["nm_curso"] )
		{
			$this->addDetalhe( array( "Curso", "{$registro["nm_curso"]}") );
		}

		if( $registro["ano"] )
		{
			$this->addDetalhe( array( "Ano", "{$registro["ano"]}") );
		}
		if( $registro["carga_horaria"] )
		{
			$registro["carga_horaria"] = str_replace(".",",",$registro["carga_horaria"]);

			$this->addDetalhe( array( "Carga Hor&aacute;ria", "{$registro["carga_horaria"]}") );
		}

		$this->addDetalhe( array( "Faltas globalizadas", is_numeric($registro["faltas_globalizadas"]) ? 'Sim' : 'Não'));

		if( $registro["dias_letivos"] )
		{
			$this->addDetalhe( array( "Dias Letivos", "{$registro["dias_letivos"]}") );
		}
		if( $registro["frequencia"] )
		{
			$this->addDetalhe( array( "Frequência", "{$registro["frequencia"]}") );
		}
		if( $registro["extra_curricular"] )
		{
			$this->addDetalhe( array( "Extra-Curricular", "Sim") );
		}
		else
		{
			$this->addDetalhe( array( "Extra-Curricular", "N&atilde;o") );
		}

    if( $registro["aceleracao"] )
		{
			$this->addDetalhe( array( "Aceleração", "Sim") );
		}
		else
		{
			$this->addDetalhe( array( "Aceleração", "N&atilde;o") );
		}
		if( $registro["origem"] )
		{
			$this->addDetalhe( array( "Origem", "Externo") );
		}
		else
		{
			$this->addDetalhe( array( "Origem", "Interno") );
		}
		if( $registro["observacao"] )
		{
			$this->addDetalhe( array( "Observa&ccedil;&atilde;o", "{$registro["observacao"]}") );
		}
		if( $registro["aprovado"] )
		{
			if ($registro["aprovado"] == 1)
			{
				$registro["aprovado"] = "Aprovado";
			}
			elseif ($registro["aprovado"] == 2)
			{
				$registro["aprovado"] = "Reprovado";
			}
			elseif ($registro["aprovado"] == 3)
			{
				$registro["aprovado"] = "Em Andamento";
			}
			elseif ($registro["aprovado"] == 4)
			{
				$registro["aprovado"] = "Transferido";
			}
			$this->addDetalhe( array( "Situa&ccedil;&atilde;o", "{$registro["aprovado"]}") );
		}

			if( $registro["registro"] )
			{
				$this->addDetalhe( array( "Registro (arquivo)", "{$registro["registro"]}") );
			}

			if( $registro["livro"] )
			{
				$this->addDetalhe( array( "Livro", "{$registro["livro"]}") );
			}

			if( $registro["folha"] )
			{
				$this->addDetalhe( array( "Folha", "{$registro["folha"]}") );
			}

		$obj = new clsPmieducarHistoricoDisciplinas();
		$obj->setOrderby("nm_disciplina ASC");
		$lst = $obj->lista( null,$this->ref_cod_aluno,$this->sequencial );
		$qtd_disciplinas = count($lst);
		if ($lst)
		{
			$tabela = "<table>
					       <tr align='center'>
					           <td bgcolor=#a1b3bd><b>Nome</b></td>
					           <td bgcolor=#a1b3bd><b>Nota</b></td>
					           <td bgcolor=#a1b3bd><b>Faltas</b></td>
					       </tr>";
			$cont = 0;
			$prim_disciplina = false;
			foreach ( $lst AS $valor )
			{
				if ( ($cont % 2) == 0 )
				{
					$color = " bgcolor='#E4E9ED' ";
				}
				else
				{
					$color = " bgcolor='#FFFFFF' ";
				}

				$valor["nm_disciplina"] = urldecode($valor["nm_disciplina"]);

				$tabela .= "<tr>
							    <td {$color} align='left'>{$valor["nm_disciplina"]}</td>
							    <td {$color} align='center'>{$valor["nota"]}</td>";

				if (is_numeric($registro["faltas_globalizadas"]) && !$prim_disciplina)
					$tabela .= "<td rowspan='{$qtd_disciplinas}' {$color} align='center'>{$registro["faltas_globalizadas"]}</td>";
				else if ( !is_numeric($registro["faltas_globalizadas"]) )
					$tabela .= "<td {$color} align='center'>{$valor["faltas"]}</td>";

				$tabela .= "</tr>";

				$registro["faltas_globalizadas"];

				$cont++;
				$prim_disciplina = true;
			}
			$tabela .= "</table>";
		}
		if( $tabela )
		{
			$this->addDetalhe( array( "Disciplina", "{$tabela}") );
		}

		$this->addBotao('Copiar Hist&oacute;rico',"educar_historico_escolar_cad.php?ref_cod_aluno={$registro["ref_cod_aluno"]}&sequencial={$registro["sequencial"]}&copia=true");

		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7 ) )
		{
			$this->url_novo = "educar_historico_escolar_cad.php?ref_cod_aluno={$registro["ref_cod_aluno"]}";
			if ($registro['origem'])
				$this->url_editar = "educar_historico_escolar_cad.php?ref_cod_aluno={$registro["ref_cod_aluno"]}&sequencial={$registro["sequencial"]}";
		}

		$this->url_cancelar = "educar_historico_escolar_lst.php?ref_cod_aluno={$registro["ref_cod_aluno"]}";
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
