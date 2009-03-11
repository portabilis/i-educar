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
/**
 * @author Adriano Erik Weiguert Nagasava
 */
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Falta Atraso" );
		$this->processoAp = "635";
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

	var $cod_falta_atraso;
	var $ref_cod_escola;
	var $ref_ref_cod_instituicao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_servidor;
	var $tipo;
	var $data_falta_atraso;
	var $qtd_horas;
	var $qtd_min;
	var $justificada;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Falta Atraso - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->ref_cod_servidor 	   = $_GET["ref_cod_servidor"];
		$this->ref_cod_escola		   = $_GET["ref_cod_escola"];
		$this->ref_ref_cod_instituicao = $_GET["ref_cod_instituicao"];

		$tmp_obj = new clsPmieducarFaltaAtraso();
		$tmp_obj->setOrderby( "data_falta_atraso DESC" );
		//$registro = $tmp_obj->lista( null, $this->ref_cod_escola, $this->ref_ref_cod_instituicao, null, null, $this->ref_cod_servidor, null, null, null, null, null, 1, null, null, null, null, 1 );
		//$registro = $tmp_obj->lista( null, $this->ref_cod_escola, $this->ref_ref_cod_instituicao, null, null, $this->ref_cod_servidor, null, null, null, null, null, null, null, null, null, null, 1 );
		$this->cod_falta_atraso = $_GET['cod_falta_atraso'];
		$registro = $tmp_obj->lista($this->cod_falta_atraso);
		
		if( !$registro )
		{
			header( "location: educar_falta_atraso_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}" );
			die();
		}
		else {
			$tabela = "<TABLE>
					       <TR align=center>
					           <TD bgcolor=#A1B3BD><B>Dia</B></TD>
					           <TD bgcolor=#A1B3BD><B>Tipo</B></TD>
					           <TD bgcolor=#A1B3BD><B>Qtd. Horas</B></TD>
					           <TD bgcolor=#A1B3BD><B>Qtd. Minutos</B></TD>
					           <TD bgcolor=#A1B3BD><B>Escola</B></TD>
					           <TD bgcolor=#A1B3BD><B>Institui&ccedil;&atilde;o</B></TD>
					       </TR>";
			$cont  = 0;
			$total = 0;
			foreach ( $registro as $falta ) {
				//$total += $divida["valor_multa"];
				if ( ($cont % 2) == 0 )
					$color = " bgcolor=#E4E9ED ";
				else
					$color = " bgcolor=#FFFFFF ";
				$obj_esc = new clsPmieducarEscolaComplemento( $falta["ref_cod_escola"] );
				$det_esc = $obj_esc->detalhe();
				$obj_ins = new clsPmieducarInstituicao( $falta["ref_ref_cod_instituicao"] );
				$det_ins = $obj_ins->detalhe();
				$corpo .= "<TR>
							    <TD {$color} align=left>".dataFromPgToBr( $falta["data_falta_atraso"] )."</TD>
							    <TD {$color} align=left>".( ( $falta["tipo"] == 1 ) ? "Atraso" : "Falta" )."</TD>
							    <TD {$color} align=right>".$falta["qtd_horas"]."</TD>
							    <TD {$color} align=right>".$falta["qtd_min"]."</TD>
							    <TD {$color} align=left>".$det_esc["nm_escola"]."</TD>
							    <TD {$color} align=left>".$det_ins["nm_instituicao"]."</TD>
							</TR>";
				$cont++;
			}
			$tabela .= $corpo;
			$tabela .= "</TABLE>";
			if ( $tabela )
					$this->addDetalhe( array( "Faltas/Atrasos", "{$tabela}") );
		}

		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 7 ) )
		{
			$this->caption_novo    = "Compensar";
			$this->url_editar	   = false;
			$this->url_novo    	   = "educar_falta_atraso_compensado_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
		}
		$this->url_cancelar = "educar_falta_atraso_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
		$this->largura 		= "100%";
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