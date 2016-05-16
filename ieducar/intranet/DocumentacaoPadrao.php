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
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Documentação padrão" );
		$this->processoAp = "578";
		$this->addEstilo('localizacaoSistema');
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

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->largura = "100%";

	    $localizacao = new LocalizacaoSistema();
	    $localizacao->entradaCaminhos( array(
	         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
	         "educar_escola_index.php"                  => "i-Educar - Escola",
	         ""                                  => "Documentação padrão"
	    ));
	    $this->enviaLocalizacao($localizacao->montar());

	    $this->inputsHelper()->dynamic(array('instituicao'));

	    $opcoes_relatorio = array();
		$opcoes_relatorio[""] = "Selecione";
	    $this->campoLista("relatorio", "Relatório", $opcoes_relatorio);
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

document.getElementById('btn_enviar').style.display = 'none';

document.getElementById('ref_cod_instituicao').onchange = function()
{
  var selectRelatorio = document.getElementById('relatorio');
  if (this.selectedIndex!==0) {
	 selectRelatorio.length = 1;
	 selectRelatorio.disabled = true;
	 selectRelatorio.options[0].text = 'Carregando Relatorios';
	 var instituicaoId = document.getElementById('ref_cod_instituicao').value;
	 console.log(instituicaoId);
	 getDocumento(instituicaoId);
  }else{
  	selectRelatorio.length = 1;
	selectRelatorio.options[0].text = 'Selecione';
  }
}

document.getElementById('relatorio').onchange = function()
{
 if (this.selectedIndex!==0) {
    window.open(this.value,'_blank');
 }
}

function getDocumento(instituicaoId) {
  var searchPath = '../module/Api/InstituicaoDocumentacao?oper=get&resource=getDocuments';
  var params = {instituicao_id : instituicaoId}
  var id     = '';
  var titulo = '';
  var url    = '';

  $j.get(searchPath, params, function(data){

    var documentos = data.documentos;

    for (var i = documentos.length - 1; i >= 0; i--) {
      	console.log(documentos[i].id, documentos[i].titulo_documento, documentos[i].url_documento);
      	var selectRelatorio = document.getElementById("relatorio");
      	var option = document.createElement("option");
		selectRelatorio.options[0].text = 'Selecione um relatório';
		selectRelatorio.disabled = false;
      	option.text = documentos[i].titulo_documento;
		option.value = documentos[i].url_documento;
		selectRelatorio.add(option);
    }
  });
}
</script>