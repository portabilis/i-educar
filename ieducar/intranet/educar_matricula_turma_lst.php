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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Matricula Turma" );
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
	var $ref_cod_turma;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	var $ref_cod_serie;
	var $ref_cod_escola;
	var $ref_cod_turma_origem;
	var $ref_cod_curso;
	var $ref_cod_instituicao;

	var $sequencial;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Listagem - Selecione a turma para realizar a transfer&ecirc;ncia";

		$this->ref_cod_matricula = $_GET['ref_cod_matricula'];

		if(!$this->ref_cod_matricula)
		{
			header("location: educar_matricula_lst.php");
			die;
		}

		$obj_matricula = new clsPmieducarMatricula($this->ref_cod_matricula);
		$det_matricula = $obj_matricula->detalhe();
		$this->ref_cod_curso = $det_matricula['ref_cod_curso'];

		$this->ref_cod_serie  = $det_matricula['ref_ref_cod_serie'];
		$this->ref_cod_escola = $det_matricula['ref_ref_cod_escola'];
		$this->ref_cod_turma = $_GET['ref_cod_turma'];

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array(
			"Turma"
		) );

		/**
		 * Busca dados da matricula
		 */
			if( class_exists( "clsPmieducarMatricula" ) )
			{
				$obj_ref_cod_matricula = new clsPmieducarMatricula();
				$detalhe_aluno = array_shift($obj_ref_cod_matricula->lista($this->ref_cod_matricula));
			}
			else
			{
				$registro["ref_cod_matricula"] = "Erro na geracao";
				echo "<!--\nErro\nClasse nao existente: clsPmieducarMatricula\n-->";
			}

			$obj_aluno = new clsPmieducarAluno();
			$det_aluno = array_shift($det_aluno = $obj_aluno->lista($detalhe_aluno['ref_cod_aluno'],null,null,null,null,null,null,null,null,null,1));

			$obj_escola = new clsPmieducarEscola($this->ref_cod_escola,null,null,null,null,null,null,null,null,null,1);
			$det_escola = $obj_escola->detalhe();

			if($det_escola['nome'])
				$this->campoRotulo("nm_escola","Escola",$det_escola['nome']);

			$this->campoRotulo("nm_pessoa","Nome do Aluno",$det_aluno['nome_aluno']);

		/**
		 *
		 */

		// Filtros de Foreign Keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarTurma" ) )
		{
			$objTemp = new clsPmieducarTurma();
			$lista = $objTemp->lista(null,null,null,$this->ref_cod_serie,$this->ref_cod_escola,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,$this->ref_cod_curso);
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_turma']}"] = "{$registro['nm_turma']}";
				}
				
			}
			
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTurma nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}		
		
		
		$this->campoLista( "ref_cod_turma_", "Turma", $opcoes, $this->ref_cod_turma );

		// outros Filtros
		$this->campoOculto("ref_cod_matricula",$this->ref_cod_matricula);
		$this->campoOculto("ref_cod_serie","");
		$this->campoOculto("ref_cod_turma","");
		$this->campoOculto("ref_cod_escola","");
		//$this->campoOculto("sequencial","");

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_matricula_turma = new clsPmieducarTurma();
		$obj_matricula_turma->setOrderby( "data_cadastro ASC" );
		$obj_matricula_turma->setLimite( $this->limite, $this->offset );

				
		$lista = $obj_matricula_turma->lista($this->ref_cod_turma,null,null,$this->ref_cod_serie,$this->ref_cod_escola,null,null,null,null,null,null,null,null,null,1,null,null,null,null,null,null,null,null,null,$this->ref_cod_curso,null,null,null,null,null,null,  true);//,   null, $parar);
		if (is_numeric($this->ref_cod_serie) && is_numeric($this->ref_cod_curso) && is_numeric($this->ref_cod_escola)) {
				$sql = "SELECT t.cod_turma, t.ref_usuario_exc, t.ref_usuario_cad, t.ref_ref_cod_serie, t.ref_ref_cod_escola, 
					t.ref_cod_infra_predio_comodo, t.nm_turma, t.sgl_turma, t.max_aluno, t.multiseriada, t.data_cadastro, t.data_exclusao, 
					t.ativo, t.ref_cod_turma_tipo, t.hora_inicial, t.hora_final, t.hora_inicio_intervalo, t.hora_fim_intervalo, 
					t.ref_cod_regente, t.ref_cod_instituicao_regente,t.ref_cod_instituicao, t.ref_cod_curso, t.ref_ref_cod_serie_mult, 
					t.ref_ref_cod_escola_mult FROM pmieducar.turma t 
					WHERE 
						t.ref_ref_cod_serie_mult = {$this->ref_cod_serie} AND t.ref_ref_cod_escola={$this->ref_cod_escola}
					AND t.ativo = '1' AND ( t.ref_ref_cod_escola = '{$this->ref_cod_escola}' )";
			$db = new clsBanco();
			$db->Consulta($sql);
			$lista_aux = array();
			while ($db->ProximoRegistro()) {
				$lista_aux[] = $db->Tupla();
			}
			if (is_array($lista_aux) && count($lista_aux)) {
				if (is_array($lista) && count($lista)) {
					$lista = array_merge($lista, $lista_aux);
				} else {
					$lista = $lista_aux;
				}
			}
			$total = count($lista);
		}
		else {
			$total = $obj_matricula_turma->_total;
		}
		$tmp_obj = new clsPmieducarMatriculaTurma();

		$det_obj = $tmp_obj->lista($this->ref_cod_matricula,null,null,null,null,null,null,null,1 );

		if($det_obj)
			$det_obj = array_shift($det_obj);

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				if($registro['cod_turma'] != $det_obj['ref_cod_turma'])
				{
					$script = "onclick='enturmar(\"{$this->ref_cod_escola}\",\"{$registro['ref_ref_cod_serie']}\",\"{$this->ref_cod_matricula}\",\"{$registro["cod_turma"]}\");'";
				//	$url = "educar_matricula_turma_det.php?ref_cod_matricula={$this->ref_cod_matricula}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_serie={$registro['ref_ref_cod_serie']}&ref_cod_turma={$registro["cod_turma"]}";
					$this->addLinhas( array(

						"<a href=\"#\" {$script}>{$registro["nm_turma"]}</a>"
					) );
				}
			}
		}


		$this->addPaginador2( "educar_matricula_turma_lst.php", $total, $_GET, $this->nome, $this->limite );
		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7 ) )
		{
			//$this->acao = "go(\"educar_matricula_turma_cad.php\")";
			//$this->nome_acao = "Novo";
		}
		$this->array_botao[] = 'Voltar';
		$this->array_botao_url[] = "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}";

		$this->largura = "100%";
	}

	/*function existeVaGa($ref_cod_serie,$ref_cod_escola){

		$obj_turmas = new clsPmieducarTurma();
		$lst_turmas = $obj_turmas->lista( null,null,null,$ref_cod_serie, $ref_cod_escola,null,null,null,null,null,null,null,null,null,1 );
		if ( is_array($lst_turmas) )
		{
			$total_vagas = 0;
			foreach ( $lst_turmas AS $turmas )
			{
				$total_vagas += $turmas["max_aluno"];
			}
		}
		else
		{
			$this->mensagem = "S&eacute;rie n&atilde;o possui nenhuma Turma cadastrada.<br>";
			return false;
		}

		$obj_matricula = new clsPmieducarMatricula();
		$lst_matricula = $obj_matricula->lista( null,null,$ref_cod_escola,$ref_ref_cod_serie,null,null,null,3,null,null,null,null,1,null,null,null,1 );
		if ( is_array($lst_matricula) )
		{
			$matriculados = count($lst_matricula);
		}

		$obj_reserva_vaga = new clsPmieducarReservaVaga();
		$lst_reserva_vaga = $obj_reserva_vaga->lista( null,$ref_cod_escola,$ref_cod_serie,null,null,null,null,null,null,null,1,null,null );
		if ( is_array($lst_reserva_vaga) )
		{
			$reservados = count($lst_reserva_vaga);
		}

		$vagas_restantes = $total_vagas - ($matriculados + $reservados);

		return $vagas_restantes > 0 ? true : false;
	}*/
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

function enturmar(ref_cod_escola,ref_cod_serie,ref_cod_matricula,ref_cod_turma,ref_cod_turma_origem){

	document.formcadastro.method = 'post';
	document.formcadastro.action = 'educar_matricula_turma_det.php';
	document.formcadastro.ref_cod_escola.value = ref_cod_escola;
	document.formcadastro.ref_cod_serie.value = ref_cod_serie;
	document.formcadastro.ref_cod_matricula.value = ref_cod_matricula;
	document.formcadastro.ref_cod_turma.value = ref_cod_turma;
	//document.formcadastro.sequencial.value = sequencial;

	document.formcadastro.submit();

}
</script>