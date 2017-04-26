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
require_once "include/clsBase.inc.php";
require_once "include/clsListagem.inc.php";
require_once "include/clsBanco.inc.php";
require_once "include/pmieducar/geral.inc.php";
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Auditoria geral" );
		$this->processoAp = "9998851";
		$this->addEstilo('localizacaoSistema');
	}
}

class indice extends clsListagem
{
	function Gerar() {

		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Auditoria geral";

		foreach( $_GET AS $var => $val )
			$this->$var = ( $val === "" ) ? null: $val;

    $this->inputsHelper()->dynamic(array('dataInicial','dataFinal'));
    $this->campoTexto( "usuario", "Matrícula usuário", $this->usuario, 35, 50);

		$obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
		$detalhe = $obj_usuario->detalhe();

		// Paginador
		$limite = 10;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;

		$this->addCabecalhos( array( "Usuário","Operação", "Rotina" ,"Valor antigo", "Valor novo", "Data") );

		$auditoria = new clsModulesAuditoriaGeral();
		$auditoria = $auditoria->lista();

		foreach ($auditoria as $a) {

			$valorAntigo = $this->transformaJsonEmTabela($a["valor_antigo"]);
			$valorNovo = $this->transformaJsonEmTabela($a["valor_novo"]);

			$this->addLinhas(array(
				$a["usuario_id"],
				$a["operacao"],
				$a["rotina"],
				$valorAntigo,
				$valorNovo,
				$a["data_hora"]
			));
		}

		$this->addPaginador2( "educar_auditoria_geral_lst.php", $total, $_GET, $this->nome, $limite );

		$obj_permissao = new clsPermissoes();
		if($obj_permissao->permissao_cadastra(9998851, $this->pessoa_logada,7,null,true)){
			$this->acao = "go(\"educar_usuario_cad.php\")";
			$this->nome_acao = "Novo";
		}

		$this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos(array(
			$_SERVER['SERVER_NAME']."/intranet" => "Início",
			"educar_configuracoes_index.php" => "Configurações",
			"" => "Auditoria geral"
    ));
    $this->enviaLocalizacao($localizacao->montar());
	}

	function transformaJsonEmTabela($json) {
		$dataJson = json_decode($json);
		$tabela = "<table class='tablelistagem' width='100%' border='0' cellpadding='4' cellspacing='1'>
								<tr>
							    <td class='formdktd' valign='top' align='left' style='font-weight:bold;'>Campo</td>
							    <td class='formdktd' valign='top' align='left' style='font-weight:bold;'>Valor</td>
							  </tr>";

		foreach ($dataJson as $key => $value) {
      $tabela .= "<tr>";
      $tabela .= "<td class='formlttd'>$key</td>";
      $tabela .= "<td class='formlttd'>$value</td>";
      $tabela .= "</tr>";
		}

		$tabela .= "</table>";


		return $tabela;
	}
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>
