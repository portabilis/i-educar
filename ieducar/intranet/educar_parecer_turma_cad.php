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
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Parecer da turma" );
		$this->processoAp = "586";
		$this->addEstilo("localizacaoSistema");
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

	var $cod_turma;
  var $parecer_1_etapa;
  var $parecer_2_etapa;
  var $parecer_3_etapa;
	var $parecer_4_etapa;

	function Inicializar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_turma=$_GET["cod_turma"];

    $obj      = new clsPmieducarTurma($this->cod_turma);
    $registro = $obj->detalhe();

    foreach ($registro as $campo => $val)
      $this->$campo = $val;

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 586, $this->pessoa_logada, 7,  "educar_turma_lst.php" );


		$this->url_cancelar = "educar_turma_det.php?cod_turma={$this->cod_turma}";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "i-Educar - Escola",
         ""        => "Lançar pareceres da turma"
    ));
    $this->enviaLocalizacao($localizacao->montar());

		$this->nome_url_cancelar = "Cancelar";

		return 'Editar';
	}

	function Gerar()
	{
				// primary keys
		$this->campoOculto( "cod_turma", $this->cod_turma );

		$obj_aluno = new clsPmieducarAluno();
		// text
    $this->campoMemo( "parecer_1_etapa", "Parecer 1ª etapa", $this->parecer_1_etapa, 60, 5, false );
    $this->campoMemo( "parecer_2_etapa", "Parecer 2ª etapa", $this->parecer_2_etapa, 60, 5, false );
    $this->campoMemo( "parecer_3_etapa", "Parecer 3ª etapa", $this->parecer_3_etapa, 60, 5, false );
		$this->campoMemo( "parecer_4_etapa", "Parecer 4ª etapa", $this->parecer_4_etapa, 60, 5, false );
	}

	function Editar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 586, $this->pessoa_logada, 7,  "educar_turma_lst.php" );

    $obj = new clsPmieducarTurma($this->cod_turma);
    $obj->ref_usuario_exc = $this->pessoa_logada;
    $obj->parecer_1_etapa = $this->parecer_1_etapa;
    $obj->parecer_2_etapa = $this->parecer_2_etapa;
    $obj->parecer_3_etapa = $this->parecer_3_etapa;
    $obj->parecer_4_etapa = $this->parecer_4_etapa;

    if ($obj->edita()){
      header( "Location: educar_turma_det.php?cod_turma={$this->cod_turma}" );
      return true;
    }
    $this->mensagem = "Erro ao salvar lançamentos.<br>";
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
