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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Dispensa Disciplina" );
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

	var $ref_cod_matricula;
	var $ref_cod_turma;
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

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Dispensa Disciplina - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->ref_cod_disciplina=$_GET["ref_cod_disciplina"];
		$this->ref_cod_matricula=$_GET["ref_cod_matricula"];
//		$this->ref_cod_turma=$_GET["ref_cod_turma"];
		$this->ref_cod_serie=$_GET["ref_cod_serie"];
		$this->ref_cod_disciplina=$_GET["ref_cod_disciplina"];
		$this->ref_cod_escola=$_GET["ref_cod_escola"];
//		$this->ref_sequencial=$_GET["ref_sequencial"];


		$tmp_obj = new clsPmieducarDispensaDisciplina( $this->ref_cod_matricula, $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_disciplina );
		$registro = $tmp_obj->detalhe();

		if( !$registro )
		{
			header( "location: educar_dispensa_disciplina_lst.php?ref_cod_matricula={$this->ref_cod_matricula}" );
			die();
		}
		//**

		if( class_exists( "clsPmieducarSerie" ) )
		{
			$obj_serie = new clsPmieducarSerie( $this->ref_cod_serie );
			$det_serie = $obj_serie->detalhe();
			$registro["ref_ref_cod_serie"] = $det_serie["nm_serie"];
		}
		else
		{
			$registro["ref_ref_cod_serie"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarSerie\n-->";
		}
		//**
//		if( class_exists( "clsPmieducarMatriculaTurma" ) )
//		{
//			$obj_ref_ref_cod_turma = new clsPmieducarMatriculaTurma( $registro["ref_cod_matricula"], $registro["ref_cod_turma"],null,null,null,null,null,null,$this->ref_sequencial );
//			$det_ref_ref_cod_turma = $obj_ref_ref_cod_turma->detalhe();
//			$registro["ref_cod_turma"] = $det_ref_ref_cod_turma["ref_cod_turma"];
//
//			$obj_turma = new clsPmieducarTurma($registro["ref_cod_turma"],null,null,null,null,null,null,null,null,null,null,null,1);
//			$det_turma = $obj_turma->detalhe();
//			$nm_turma = $det_turma['nm_turma'];

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


			$nm_aluno = $det_aluno['nome_aluno'];

		/**
		 *
		 */

//		}
//		else
//		{
//			$registro["ref_ref_cod_turma"] = "Erro na geracao";
//			echo "<!--\nErro\nClasse nao existente: clsPmieducarMatriculaTurma\n-->";
//		}

		if( class_exists( "clsPmieducarCurso" ) )
		{
			$obj_ref_cod_curso = new clsPmieducarCurso( $detalhe_aluno["ref_cod_curso"] );
			$det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
			$registro["ref_cod_curso"] = $det_ref_cod_curso["nm_curso"];
		}
		else
		{
			$registro["ref_cod_curso"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarCurso\n-->";
		}

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


		if( $registro["ref_cod_matricula"] )
		{
			$this->addDetalhe( array( "Matricula", "{$registro["ref_cod_matricula"]}") );
		}
		if( $nm_aluno )
		{
			$this->addDetalhe( array( "Aluno", "{$nm_aluno}") );
		}
		if( $registro["ref_cod_curso"] )
		{
			$this->addDetalhe( array( "Curso", "{$registro["ref_cod_curso"] }") );
		}
		if( $registro["ref_ref_cod_serie"] )
		{
			$this->addDetalhe( array( "S&eacute;rie", "{$registro["ref_ref_cod_serie"]}") );
		}
//		if( $nm_turma )
//		{
//			$this->addDetalhe( array( "Turma", "{$nm_turma}") );
//		}

		if( $registro["ref_cod_disciplina"] )
		{
			$obj_disciplina = new clsPmieducarDisciplina($registro['ref_cod_disciplina'],null,null,null,null,null,null,null,null,null,1);
			$det_disciplina = $obj_disciplina->detalhe();
			$this->addDetalhe( array( "Disciplina", "{$det_disciplina["nm_disciplina"]}") );
		}
		if( $registro["ref_cod_tipo_dispensa"] )
		{
			$this->addDetalhe( array( "Tipo Dispensa", "{$registro["ref_cod_tipo_dispensa"]}") );
		}
		if( $registro["observacao"] )
		{
			$this->addDetalhe( array( "Observa&ccedil;&atilde;o", "{$registro["observacao"]}") );
		}

		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7 ) )
		{
		$this->url_novo = "educar_dispensa_disciplina_cad.php?ref_cod_matricula={$this->ref_cod_matricula}";
		$this->url_editar = "educar_dispensa_disciplina_cad.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_disciplina={$registro["ref_cod_disciplina"]}";
		}

		$this->url_cancelar = "educar_dispensa_disciplina_lst.php?ref_cod_matricula={$this->ref_cod_matricula}";
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