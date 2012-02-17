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
require_once ("include/clsListagem.inc.php");
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

class indice extends clsListagem
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	/**
	 * Quantidade de registros a ser apresentada em cada pagina
	 *
	 * @var int
	 */
	var $limite;

	/**
	 * Inicio dos registros a serem exibidos (limit)
	 *
	 * @var int
	 */
	var $offset;

	var $ref_cod_aluno;
	var $sequencial;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_serie;
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
	var $ref_cod_escola;
	var $extra_curricular;
	var $frequencia;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Hist&oacute;rico Escolar - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = $val;

		$this->campoOculto( "ref_cod_aluno", $this->ref_cod_aluno );

		if ( !$this->ref_cod_aluno )
		{
			header( "location: educar_aluno_lst.php" );
			die();
		}

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$lista_busca = array(
			"Ano",
			"Extra-curricular"
		);

		$obj_permissao = new clsPermissoes();
		$nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
		{
			if (!$this->extra_curricular)
				$lista_busca[] = "Escola";

			$lista_busca[] = "Institui&ccedil;&atilde;o";
		}
		else if ($nivel_usuario == 2)
		{
			if (!$this->extra_curricular)
				$lista_busca[] = "Escola";
		}
    $lista_busca = array_merge($lista_busca, array('Curso', 'Série', 'Registro', 'Livro', 'Folha'));

		$this->addCabecalhos($lista_busca);

		$get_escola = true;

		include("include/pmieducar/educar_campo_lista.php");

		// outros Filtros
		$this->campoNumero( "ano", "Ano", $this->ano, 4, 4, false );

		$opcoes = array( "" => "Selecione", 2 => "N&atilde;o", 1 => "Sim" );
		$this->campoLista( "extra_curricular", "Extra-curricular", $opcoes, $this->extra_curricular,"",false,"","",false,false );

		if ($this->extra_curricular == 2)
			$this->extra_curricular = 0;

//		if ($this->ref_cod_escola)
//		{
//			$obj_ref_cod_escola = new clsPmieducarEscola( $this->ref_cod_escola );
//			$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
//			$this->escola = $det_ref_cod_escola["nome"];
//		}

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_historico_escolar = new clsPmieducarHistoricoEscolar();
		$obj_historico_escolar->setOrderby( "ano, sequencial ASC" );
		$obj_historico_escolar->setLimite( $this->limite, $this->offset );

		$lista = $obj_historico_escolar->lista(
			$this->ref_cod_aluno,
			null,
			null,
			null,
			null,
			$this->ano,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			1,
			null,
			$this->ref_cod_instituicao,
			null,
			$this->extra_curricular,
			null
		);

		$total = $obj_historico_escolar->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				if( class_exists( "clsPmieducarInstituicao" ) )
				{
					$obj_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
					$obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
					$registro["ref_cod_instituicao"] = $obj_cod_instituicao_det["nm_instituicao"];
				}
				else
				{
					$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
				}

				if ($registro["extra_curricular"])
					$registro["extra_curricular"] = "Sim";
				else
					$registro["extra_curricular"] = "N&atilde;o";

				$lista_busca = array(
					"<a href=\"educar_historico_escolar_det.php?ref_cod_aluno={$registro["ref_cod_aluno"]}&sequencial={$registro["sequencial"]}\">{$registro["ano"]}</a>",

					"<a href=\"educar_historico_escolar_det.php?ref_cod_aluno={$registro["ref_cod_aluno"]}&sequencial={$registro["sequencial"]}\">{$registro["extra_curricular"]}</a>"
				);

				if ($nivel_usuario == 1)
				{
					if (!$this->extra_curricular)
						$lista_busca[] = "<a href=\"educar_historico_escolar_det.php?ref_cod_aluno={$registro["ref_cod_aluno"]}&sequencial={$registro["sequencial"]}\">{$registro["escola"]}</a>";

					$lista_busca[] = "<a href=\"educar_historico_escolar_det.php?ref_cod_aluno={$registro["ref_cod_aluno"]}&sequencial={$registro["sequencial"]}\">{$registro["ref_cod_instituicao"]}</a>";
				}
				else if ($nivel_usuario == 2)
				{
					if (!$this->extra_curricular)
						$lista_busca[] = "<a href=\"educar_historico_escolar_det.php?ref_cod_aluno={$registro["ref_cod_aluno"]}&sequencial={$registro["sequencial"]}\">{$registro["escola"]}</a>";
				}

        $lista_busca[] = $registro['nm_curso'];
        $lista_busca[] = $registro['nm_serie'];
        $lista_busca[] = $registro['registro'];
        $lista_busca[] = $registro['livro'];
        $lista_busca[] = $registro['folha'];

				$this->addLinhas($lista_busca);
			}
		}
		$this->addPaginador2( "educar_historico_escolar_lst.php", $total, $_GET, $this->nome, $this->limite );
		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7 ) )
		{
			$this->acao = "go(\"educar_historico_escolar_cad.php?ref_cod_aluno={$this->ref_cod_aluno}\")";
			$this->nome_acao = "Novo";
		}
		$this->array_botao[] = 'Voltar';
		$this->array_botao_url[] = "educar_aluno_det.php?cod_aluno={$this->ref_cod_aluno}";

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
