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
require_once 'Educacenso/Model/AlunoDataMapper.php';
require_once( "modules/Ciasc/Model/CodigoAlunoDataMapper.php" );
require_once( "modules/Ciasc/Model/CodigoAlunoDataMapper.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Aluno" );
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

	var $cod_aluno;
	var $ref_idpes_responsavel;
	//var $ref_cod_pessoa_educ;
	var $ref_cod_aluno_beneficio;
	var $ref_cod_religiao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_idpes;
	var $ativo;

	var $nome_aluno;
	var $mat_aluno;
	var $identidade;
	var $matriculado;
	var $inativado;
	var $nome_responsavel;
	var $cpf_responsavel;

	var $nome_pai;
	var $nome_mae;
	var $data_nascimento;

	function Gerar()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Aluno - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->campoNumero("cod_aluno","C&oacute;digo Aluno",$this->cod_aluno,20,255,false);
		$this->campoNumero("cod_ciasc","C&oacute;digo CIASC",$this->cod_ciasc,20,255,false);
		$this->campoNumero("cod_inep","C&oacute;digo INEP",$this->cod_inep,20,255,false);
		$this->campoTexto("nome_aluno","Nome do aluno", $this->nome_aluno,50,255,false);
//		if ($this->pessoa_logada == 184580) {
			$this->campoData("data_nascimento", "Data de Nascimento", $this->data_nascimento);
			$this->campoTexto("nome_pai", "Nome do Pai", $this->nome_pai, 50, 255);
			$this->campoTexto("nome_mae", "Nome da Mãe", $this->nome_mae, 50, 255);
			$this->campoTexto("nome_responsavel", "Nome do Responsável", $this->nome_responsavel, 50, 255);
//		}

		$obj_permissoes = new clsPermissoes();
		$cod_escola = $obj_permissoes->getEscola( $this->pessoa_logada );
		if ($cod_escola)
		{
			$this->campoCheck( "meus_alunos", "Meus Alunos", $_GET['meus_alunos'] );
			$ref_cod_escola = false;
			if ($_GET['meus_alunos'])
			{
				$ref_cod_escola = $cod_escola;
			}
		}


		$array_matriculado = array('S' => "Sim", 'N' => 'N&atilde;o');

		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

		$this->addCabecalhos( array(
			"C&oacute;digo Aluno",
                        "Matrícula CIASC",
                        "Código INEP",
			"Nome do Aluno",
			"Nome da Mãe",
			"Nome do Respons&aacute;vel",
			"CPF Respons&aacute;vel",
		) );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_aluno = new clsPmieducarAluno();
//		$obj_aluno->setOrderby( "cod_aluno DESC" );
		$obj_aluno->setLimite( $this->limite, $this->offset );

		/*$lista = $obj_aluno->lista(
					$this->cod_aluno,
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
					$this->nome_aluno,
					$this->nome_responsavel,
					idFederal2int($this->cpf_responsavel),
					null,
					$this->nome_pai,
					$this->nome_mae,
					$ref_cod_escola
				);*/
//		if ($this->pessoa_logada == 184580) {
			$lista = $obj_aluno->lista2(
						$this->cod_aluno,
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
						$this->nome_aluno,
						null,
						idFederal2int($this->cpf_responsavel),
						null,
						null,
						null,
						$ref_cod_escola,
						null,
						$this->data_nascimento,
						$this->nome_pai,
						$this->nome_mae,
						$this->nome_responsavel,
                                                $this->cod_ciasc,
                                                $this->cod_inep
					);
		/*} else {
			$lista = $obj_aluno->lista(
					$this->cod_aluno,
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
					$this->nome_aluno,
					null,
					idFederal2int($this->cpf_responsavel),
					null,
					null,
					null,
					$ref_cod_escola
				);
		}*/

		$total = $obj_aluno->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{

				$registro["nome_responsavel"] = null;
				$det_fisica_aluno = null;

				if($registro['tipo_responsavel'] == 'p'  || (!$registro["nome_responsavel"] && $registro['tipo_responsavel'] == null))
				{
					$obj_fisica= new clsFisica($registro["ref_idpes"]);
					$det_fisica_aluno = $obj_fisica->detalhe();
					if($det_fisica_aluno["idpes_pai"] )
					{
						$obj_ref_idpes = new clsPessoa_( $det_fisica_aluno["idpes_pai"] );
						$det_ref_idpes = $obj_ref_idpes->detalhe();
						$obj_fisica= new clsFisica($det_fisica_aluno["idpes_pai"]);
						$det_fisica = $obj_fisica->detalhe();
						$registro["nome_responsavel"] = $det_ref_idpes['nome'];

						if( $det_fisica["cpf"] )
							$registro["cpf_responsavel"] = int2CPF($det_fisica["cpf"]);
					}

				}

				if($registro['tipo_responsavel'] == 'm' || ($registro["nome_responsavel"] == null && $registro['tipo_responsavel'] == null))
				{
					if(!$det_fisica_aluno)
					{
						$obj_fisica= new clsFisica($registro["ref_idpes"]);
						$det_fisica_aluno = $obj_fisica->detalhe();
					}

					if($det_fisica_aluno["idpes_mae"] )
					{
						$obj_ref_idpes = new clsPessoa_( $det_fisica_aluno["idpes_mae"] );
						$det_ref_idpes = $obj_ref_idpes->detalhe();
						$obj_fisica= new clsFisica($det_fisica_aluno["idpes_mae"]);
						$det_fisica = $obj_fisica->detalhe();
						$registro["nome_responsavel"] = $det_ref_idpes["nome"];

						if($det_fisica["cpf"])
							$registro["cpf_responsavel"] = int2CPF($det_fisica["cpf"]);
					}
				}

				if($registro['tipo_responsavel'] == 'r' || ($registro["nome_responsavel"] == null && $registro['tipo_responsavel'] == null))
				{
					if(!$det_fisica_aluno)
					{
						$obj_fisica= new clsFisica($registro["ref_idpes"]);
						$det_fisica_aluno = $obj_fisica->detalhe();
					}

					if( $det_fisica_aluno["idpes_responsavel"] )
					{
						$obj_ref_idpes = new clsPessoa_( $det_fisica_aluno["idpes_responsavel"] );
						$obj_fisica = new clsFisica( $det_fisica_aluno["idpes_responsavel"] );
						$det_ref_idpes = $obj_ref_idpes->detalhe();
						$det_fisica = $obj_fisica->detalhe();
						$registro["nome_responsavel"] = $det_ref_idpes["nome"];
						if($det_fisica["cpf"])
							$registro["cpf_responsavel"] = int2CPF($det_fisica["cpf"]);
					}
				}

				if(!$registro["nome_responsavel"])
				{
					if($registro['tipo_responsavel'] != null)
					{
						if($registro['tipo_responsavel'] == 'p')
							$registro["nome_responsavel"] = $registro["nm_pai"];
						else
							$registro["nome_responsavel"] = $registro["nm_mae"];
					}
					else
					{
						if($registro["nm_pai"])
							$registro["nome_responsavel"] = $registro["nm_pai"];
						else
							$registro["nome_responsavel"] = $registro["nm_mae"];
					}
				}
                                $ciascMapper = new Ciasc_Model_CodigoAlunoDataMapper();
                                $alunoCiasc = NULL;
                                  try {
                                    $alunoCiasc = $ciascMapper->find(array('cod_aluno' => $registro["cod_aluno"]));
                                  }
                                  catch(Exception $e) {
                                  }
                                  if (empty($alunoCiasc->cod_aluno)){
                                      $registro['cod_ciasc'] = '-';
                                  } else {
                                      $registro['cod_ciasc'] = $alunoCiasc->cod_ciasc;
                                  }

                                  $inepMapper = new Educacenso_Model_AlunoDataMapper();
                                  $alunoInep = NULL;

                                  try {
                                    $alunoInep = $inepMapper->find(array('cod_aluno' => $registro["cod_aluno"]));
                                  }
                                  catch(Exception $e) {
                                  }

                                  if (empty($alunoInep->alunoInep)){
                                      $registro['cod_inep'] = '-';
                                  } else {
                                      $registro['cod_inep'] = $alunoInep->alunoInep;
                                  }

    $registro["nome_aluno"] = strtoupper($registro["nome_aluno"]);
    $registro["nm_mae"] = strtoupper($registro["nm_mae"]);
    $registro["nome_responsavel"] = strtoupper($registro["nome_responsavel"]);


				$this->addLinhas( array(
					"<a href=\"educar_aluno_det.php?cod_aluno={$registro["cod_aluno"]}\">{$registro["cod_aluno"]}</a>",
					"<a href=\"educar_aluno_det.php?cod_aluno={$registro["cod_aluno"]}\">{$registro["cod_ciasc"]}</a>",
					"<a href=\"educar_aluno_det.php?cod_aluno={$registro["cod_aluno"]}\">{$registro["cod_inep"]}</a>",
					"<a href=\"educar_aluno_det.php?cod_aluno={$registro["cod_aluno"]}\">{$registro["nome_aluno"]}</a>",
					"<a href=\"educar_aluno_det.php?cod_aluno={$registro["cod_aluno"]}\">{$registro["nm_mae"]}</a>",
					"<a href=\"educar_aluno_det.php?cod_aluno={$registro["cod_aluno"]}\">{$registro["nome_responsavel"]}</a>",
					"<a href=\"educar_aluno_det.php?cod_aluno={$registro["cod_aluno"]}\">{$registro["cpf_responsavel"]}</a>"
				) );
			}
		}

		$this->addPaginador2( "educar_aluno_lst.php", $total, $_GET, $this->nome, $this->limite );


		//** Verificacao de permissao para cadastro
		if($obj_permissoes->permissao_cadastra(578, $this->pessoa_logada,7))
		{
			$this->acao = "go(\"/module/Cadastro/aluno\")";
			$this->nome_acao = "Novo";

			/*$this->array_botao = array("Ficha do Aluno (em branco)");
			$this->array_botao_script = array( "showExpansivelImprimir(400, 200,  \"educar_relatorio_aluno_dados.php\",\"\", \"Relatório i-Educar\" )" );*/
		}
		//**
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
