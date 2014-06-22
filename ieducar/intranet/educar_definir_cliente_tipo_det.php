<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itaja�								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
	*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
	*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
	*																		 *
	*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
	*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
	*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/**
 * @author Adriano Erik Weiguert Nagasava
 */
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class indice extends clsDetalhe
{
	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	var $cod_cliente;
	var $ref_cod_cliente_tipo;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_idpes;
	var $login;
	var $senha;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Cliente - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_cliente 			= $_GET["cod_cliente"];
		$this->ref_cod_cliente_tipo = $_GET["cod_cliente_tipo"];

		if ( !( isset( $this->cod_cliente ) && isset( $this->ref_cod_cliente_tipo ) ) ) {
			header( "location: educar_definir_cliente_tipo_lst.php" );
			die();
		}

		$tmp_obj = new clsPmieducarCliente();
		$registro = $tmp_obj->listaCompleta( $this->cod_cliente,
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
											 null,
											 $this->ref_cod_cliente_tipo );

		if( ! $registro )
		{
			header( "location: educar_definir_cliente_tipo_lst.php" );
			die();
		}
		else {
			foreach ( $registro as $cliente )
			{
				if ( $cliente["nome"] ) {
					$this->addDetalhe( array( "Cliente", "{$cliente["nome"]}") );
				}
				if ( $cliente["nm_biblioteca"] ) {
					$this->addDetalhe( array( "Biblioteca", "{$cliente["nm_biblioteca"]}") );
				}
				if ( $cliente["nm_tipo"] ) {
					$this->addDetalhe( array( "Tipo do Cliente", "{$cliente["nm_tipo"]}") );
				}
				if ( class_exists( "clsBanco" ) ) {
					$obj_banco = new clsBanco();
					$sql_unico = "SELECT ref_cod_motivo_suspensao
									FROM pmieducar.cliente_suspensao
								   WHERE ref_cod_cliente = {$cliente["cod_cliente"]}
									 AND data_liberacao IS NULL
									 AND EXTRACT ( DAY FROM ( NOW() - data_suspensao ) ) < dias";
					$motivo    = $obj_banco->CampoUnico( $sql_unico );
					if ( is_numeric( $motivo ) ) {
						$this->addDetalhe( array( "Status", "Suspenso" ) );
						if ( class_exists( "clsPmieducarMotivoSuspensao" ) ) {
							$obj_motivo_suspensao = new clsPmieducarMotivoSuspensao( $motivo );
							$det_motivo_suspensao = $obj_motivo_suspensao->detalhe();
							$this->addDetalhe( array( "Motivo da Suspens�o", "{$det_motivo_suspensao["nm_motivo"]}" ) );
							$this->addDetalhe( array( "Descri��o", "{$det_motivo_suspensao["descricao"]}" ) );
						}
					}
					else
						$this->addDetalhe( array( "Status", "Regular" ) );
				}
				else {
					$registro["ref_idpes"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsBanco\n-->";
				}
			}
		}
		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 623, $this->pessoa_logada, 11 ) )
		{
		$this->url_novo = "educar_definir_cliente_tipo_cad.php";
		$this->url_editar = "educar_definir_cliente_tipo_cad.php?cod_cliente={$cliente["cod_cliente"]}&cod_cliente_tipo={$cliente["cod_cliente_tipo"]}";
		}

		$this->url_cancelar = "educar_definir_cliente_tipo_lst.php";
		$this->largura = "100%";
	}
}

// cria uma extensao da classe base
$pagina = new clsBase();

$pagina->SetTitulo( "{$pagina->_instituicao} i-Educar - Cliente" );
$pagina->processoAp = "623";
	// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>