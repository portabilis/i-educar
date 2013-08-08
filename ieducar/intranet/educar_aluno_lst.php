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
require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'Educacenso/Model/AlunoDataMapper.php';
require_once 'include/localizacaoSistema.php';

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Aluno" );
		$this->processoAp = "578";
                $this->addEstilo( "localizacaoSistema" );
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
		$this->campoNumero("cod_inep","C&oacute;digo INEP",$this->cod_inep,20,255,false);
		$this->campoTexto("nome_aluno","Nome do aluno", $this->nome_aluno,50,255,false);
		$this->campoData("data_nascimento", "Data de Nascimento", $this->data_nascimento);
		$this->campoTexto("nome_pai", "Nome do Pai", $this->nome_pai, 50, 255);
		$this->campoTexto("nome_mae", "Nome da Mãe", $this->nome_mae, 50, 255);
		$this->campoTexto("nome_responsavel", "Nome do Responsável", $this->nome_responsavel, 50, 255);

		
                
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
                        "Código INEP",
			"Nome do Aluno",
			"Nome da Mãe",
			"Nome do Respons&aacute;vel",
			"CPF Respons&aacute;vel",
		) );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$aluno = new clsPmieducarAluno();
		$aluno->setLimite( $this->limite, $this->offset );

		$alunos = $aluno->lista2(
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
                        $this->cod_inep
		);

		$total = $aluno->_total;

		foreach ( $alunos AS $registro ) {
	    $alunoInepId      = $this->tryLoadAlunoInepId($registro["cod_aluno"]);
	    $nomeAluno        = strtoupper($registro["nome_aluno"]);
	    $nomeMae          = strtoupper($this->loadNomeMae($registro));

	    // responsavel
			$aluno->cod_aluno = $registro["cod_aluno"];
	    $responsavel      = $aluno->getResponsavelAluno();
	    $nomeResponsavel  = strtoupper($responsavel["nome_responsavel"]);

			$this->addLinhas( array(
				"<a href=\"educar_aluno_det.php?cod_aluno={$registro["cod_aluno"]}\">{$registro["cod_aluno"]}</a>",
				"<a href=\"educar_aluno_det.php?cod_aluno={$registro["cod_aluno"]}\">{$alunoInepId}</a>",
				"<a href=\"educar_aluno_det.php?cod_aluno={$registro["cod_aluno"]}\">{$nomeAluno}</a>",
				"<a href=\"educar_aluno_det.php?cod_aluno={$registro["cod_aluno"]}\">{$nomeMae}</a>",
				"<a href=\"educar_aluno_det.php?cod_aluno={$registro["cod_aluno"]}\">{$nomeResponsavel}</a>",
				"<a href=\"educar_aluno_det.php?cod_aluno={$registro["cod_aluno"]}\">{$responsavel["cpf_responsavel"]}</a>"
			) );
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
	
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    "educar_index.php"                  => "Escola",
                    ""                                  => "Lista de Aluno"
                ));
                $this->enviaLocalizacao($localizacao->montar());
    
        }

	protected function loadNomeMae($aluno) {
		$nome        = $aluno['nm_mae'];

  	$pessoaAluno = new clsFisica($aluno['ref_idpes']);
  	$pessoaAluno = $pessoaAluno->detalhe();

  	if ($pessoaAluno['idpes_mae']) {
    	$pessoaMae   = new clsPessoaFj($pessoaAluno['idpes_mae']);
    	$pessoaMae   = $pessoaMae->detalhe();
    	$nome        = $pessoaMae['nome'];
         }
         
    return $nome;
	}

	protected function tryLoadAlunoInepId($alunoId) {
    $dataMapper  = new Educacenso_Model_AlunoDataMapper();

    try {
      $alunoInep = $dataMapper->find(array('cod_aluno' => $alunoId));
	    $id        = $alunoInep->alunoInep;
    }
    catch(Exception $e) {
    	$id = '';
    }
                
    return $id;
      
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
