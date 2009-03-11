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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Dispensa Disciplina" );
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

	var $ref_cod_matricula;
	var $ref_cod_serie;
	var $ref_cod_escola;
	var $ref_cod_disciplina;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_tipo_dispensa;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $observacao;
	var $ref_sequencial;

	var $ref_cod_instituicao;
	var $ref_cod_turma;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Dispensa Disciplina - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;


		if(!$_GET['ref_cod_matricula'])
		{
			header("location: educar_matricula_lst.php");
			die;
		}

		$this->ref_cod_matricula = $_GET['ref_cod_matricula'];

		$obj_matricula = new clsPmieducarMatricula();
		$lst_matricula = $obj_matricula->lista( $this->ref_cod_matricula );
		if (is_array($lst_matricula))
		{
			$det_matricula = array_shift($lst_matricula);
			$this->ref_cod_instituicao = $det_matricula["ref_cod_instituicao"];
			$this->ref_cod_escola = $det_matricula["ref_ref_cod_escola"];
			$this->ref_cod_serie = $det_matricula["ref_ref_cod_serie"];

			$obj_matricula_turma = new clsPmieducarMatriculaTurma();
			$lst_matricula_turma = $obj_matricula_turma->lista( $this->ref_cod_matricula,null,null,null,null,null,null,null,1,$this->ref_cod_serie,null,$this->ref_cod_escola );
			if (is_array($lst_matricula_turma))
			{
				$det = array_shift($lst_matricula_turma);
				$this->ref_cod_turma = $det["ref_cod_turma"];
				$this->ref_sequencial = $det["sequencial"];

			}
		}
		$this->campoOculto( "ref_cod_turma", $this->ref_cod_turma );

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array(
			"Disciplina",
			"Tipo Dispensa",
			"Data Dispensa"
		) );

		// Filtros de Foreign Keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarTipoDispensa" ) )
		{
			$objTemp = new clsPmieducarTipoDispensa();
			if ($this->ref_cod_instituicao)
				$lista = $objTemp->lista( null,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao );
			else
				$lista = $objTemp->lista( null,null,null,null,null,null,null,null,null,1 );

			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_tipo_dispensa']}"] = "{$registro['nm_tipo']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTipoDispensa nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_tipo_dispensa", "Motivo", $opcoes, $this->ref_cod_tipo_dispensa,"",false,"","",false,false);

		$this->campoOculto("ref_cod_matricula",$this->ref_cod_matricula);

		// outros Filtros
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarEscolaSerieDisciplina" ) )
		{
			$objTemp = new clsPmieducarEscolaSerieDisciplina();
			$lista = $objTemp->lista( $this->ref_cod_serie,$this->ref_cod_escola,null,1 );
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$obj_disciplina = new clsPmieducarDisciplina($registro['ref_cod_disciplina'],null,null,null,null,null,null,null,null,null,1);
					$det_disciplina = $obj_disciplina->detalhe();
					$opcoes["{$registro['ref_cod_disciplina']}"] = "{$det_disciplina['nm_disciplina']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarEscolaSerieDisciplina nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}

		$this->campoLista( "ref_cod_disciplina", "Disciplina", $opcoes, $this->ref_cod_disciplina,"",false,"","",false,false );


		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_dispensa_disciplina = new clsPmieducarDispensaDisciplina();
		$obj_dispensa_disciplina->setOrderby( "data_cadastro ASC" );
		$obj_dispensa_disciplina->setLimite( $this->limite, $this->offset );

		$lista = $obj_dispensa_disciplina->lista(
			$this->ref_cod_matricula,
			null,
			null,
			$this->ref_cod_disciplina,
			null,
			null,
			$this->ref_cod_tipo_dispensa,
			null,
			null,
			null,
			null,
			1
		);

		$total = $obj_dispensa_disciplina->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				// muda os campos data
				$registro["data_cadastro_time"] = strtotime( substr( $registro["data_cadastro"], 0, 16 ) );
				$registro["data_cadastro_br"] = date( "d/m/Y", $registro["data_cadastro_time"] );

				if( class_exists( "clsPmieducarTipoDispensa" ) )
				{
					$obj_ref_cod_tipo_dispensa = new clsPmieducarTipoDispensa( $registro["ref_cod_tipo_dispensa"] );
					$det_ref_cod_tipo_dispensa = $obj_ref_cod_tipo_dispensa->detalhe();
					$registro["ref_cod_tipo_dispensa"] = $det_ref_cod_tipo_dispensa["nm_tipo"];
				}
				else
				{
					$registro["ref_cod_tipo_dispensa"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarTipoDispensa\n-->";
				}

				$obj_disciplina = new clsPmieducarDisciplina($registro['ref_cod_disciplina'],null,null,null,null,null,null,null,null,null,1);
				$det_disciplina = $obj_disciplina->detalhe();

				$this->addLinhas( array(
					"<a href=\"educar_dispensa_disciplina_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_serie={$registro["ref_cod_serie"]}&ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_disciplina={$registro["ref_cod_disciplina"]}\">{$det_disciplina["nm_disciplina"]}</a>",
					"<a href=\"educar_dispensa_disciplina_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_serie={$registro["ref_cod_serie"]}&ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_disciplina={$registro["ref_cod_disciplina"]}\">{$registro["ref_cod_tipo_dispensa"]}</a>",
					"<a href=\"educar_dispensa_disciplina_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_serie={$registro["ref_cod_serie"]}&ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_disciplina={$registro["ref_cod_disciplina"]}\">{$registro["data_cadastro_br"]}</a>"
				) );
			}
		}
		$this->addPaginador2( "educar_dispensa_disciplina_lst.php", $total, $_GET, $this->nome, $this->limite );
		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7 ) )
		{
			$this->array_botao_url[] = "educar_dispensa_disciplina_cad.php?ref_cod_matricula={$this->ref_cod_matricula}";
			$this->array_botao[] = "Novo";
		}

		$this->array_botao_url[] = "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}";
		$this->array_botao[] = "Voltar";

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
var campoTurma = document.getElementById('ref_cod_turma');
var campoDisciplina = document.getElementById('ref_cod_disciplina');
campoTurma.onchange = function(){
						campoDisciplina.options.length = 1;
						for(var ct=0;ct<disciplina.length;ct++){
							if((campoTurma.options[campoTurma.selectedIndex].value.split("-"))[0] == disciplina[ct][0])
								campoDisciplina.options[campoDisciplina.length] = new Option(disciplina[ct][2],disciplina[ct][1],false,false);
						}
					  };
</script>