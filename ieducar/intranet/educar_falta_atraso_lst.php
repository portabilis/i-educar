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
require_once ("include/clsListagem.inc.php");
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

		$this->ref_cod_servidor 	   = $_GET["ref_cod_servidor"];
		$this->ref_ref_cod_instituicao = $_GET["ref_cod_instituicao"];

		$this->titulo = "Falta Atraso - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array(
			"Escola",
			"Instituic&atilde;o",
			"Quantidade de Horas",
			"Quantidade de Minutos"
		) );

		// Filtros de Foreign Keys
		$obrigatorio 	 = false;
		$get_instituicao = true;
		$get_escola		 = true;
		include("include/pmieducar/educar_campo_lista.php");

		// outros Filtros
		/*$opcoes = array( "" => "Selecione", "1" => "Atraso", "2" => "Falta" );
		$this->campoLista( "tipo", "Tipo", $opcoes, $this->tipo );
		$this->campoData( "data_falta_atraso", "Dia", $this->data_falta_atraso, false );*/


		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_falta_atraso = new clsPmieducarFaltaAtraso(null, $this->ref_cod_escola, $this->ref_ref_cod_instituicao, null, null, $this->ref_cod_servidor);
		$obj_falta_atraso->setOrderby( "tipo ASC" );
		$obj_falta_atraso->setLimite( $this->limite, $this->offset );

		//$lista = $obj_falta_atraso->listaHorasEscola( $this->ref_cod_servidor, $this->ref_ref_cod_instituicao, $this->ref_cod_escola );

		$lista = $obj_falta_atraso->lista();
		
		$total = $obj_falta_atraso->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				// pega detalhes de foreign_keys
				if( class_exists( "clsPmieducarEscola" ) )
				{
					$obj_ref_cod_escola = new clsPmieducarEscolaComplemento( $registro["ref_cod_escola"] );
					$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
					$registro["nm_escola"] = $det_ref_cod_escola["nm_escola"];
				}
				else
				{
					$registro["ref_cod_escola"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarEscola\n-->";
				}
				if ( class_exists( "clsPmieducarInstituicao" ) ) {
					$obj_ins = new clsPmieducarInstituicao( $registro["ref_ref_cod_instituicao"] );
					$det_ins = $obj_ins->detalhe();
				}
				else {
					echo "<!--\nErro\nClasse nao existente: clsPmieducarEscola\n-->";
				}
				if( class_exists( "clsPmieducarFaltaAtrasoCompensado" ) )
				{
					$obj_comp = new clsPmieducarFaltaAtrasoCompensado();
					$horas	  = $obj_comp->ServidorHorasCompensadas( $this->ref_cod_servidor, $registro["ref_cod_escola"], $registro["ref_ref_cod_instituicao"] );
					if ( $horas )
					{
						$horas_aux   = $horas["hora"];
						$minutos_aux = $horas["min"];
					}
				}
				else {
					echo "<!--\nErro\nClasse nao existente: classPmieducarFaltaAtrasoCompensado\n-->";
				}
//				$horas_aux   = floor( $horas );
//				$minutos_aux = ( $horas - $horas_aux ) * 60;
				$horas_aux   = $horas_aux - $registro["horas"];
				$minutos_aux = $minutos_aux - $registro["minutos"];

				if ( $horas_aux > 0 && $minutos_aux < 0 )
				{
					$horas_aux--;
					$minutos_aux += 60;
				}

				if ( $horas_aux < 0 && $minutos_aux > 0 )
				{
					$horas_aux--;
					$minutos_aux -= 60;
				}

				if ( $horas_aux < 0 )
					$horas_aux = "(".$horas_aux.")";

				if ( $minutos_aux < 0 )
					$minutos_aux = "(".$minutos_aux.")";

				/*$this->addLinhas( array(
					"<a href=\"educar_falta_atraso_det.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_instituicao={$registro["ref_ref_cod_instituicao"]}\">{$registro["nm_escola"]}</a>",
					"<a href=\"educar_falta_atraso_det.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_instituicao={$registro["ref_ref_cod_instituicao"]}\">{$det_ins["nm_instituicao"]}</a>",
					"<a href=\"educar_falta_atraso_det.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_instituicao={$registro["ref_ref_cod_instituicao"]}\">{$horas_aux}</a>",
					"<a href=\"educar_falta_atraso_det.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_instituicao={$registro["ref_ref_cod_instituicao"]}\">{$minutos_aux}</a>"
				) );*/
				$this->addLinhas( array(
					"<a href=\"educar_falta_atraso_det.php?cod_falta_atraso={$registro['cod_falta_atraso']}\">{$registro["nm_escola"]}</a>",
					"<a href=\"educar_falta_atraso_det.php?cod_falta_atraso={$registro['cod_falta_atraso']}\">{$det_ins["nm_instituicao"]}</a>",
					"<a href=\"educar_falta_atraso_det.php?cod_falta_atraso={$registro['cod_falta_atraso']}\">{$horas_aux}</a>",
					"<a href=\"educar_falta_atraso_det.php?cod_falta_atraso={$registro['cod_falta_atraso']}\">{$minutos_aux}</a>"
				) );
			}
		}
		$this->addPaginador2( "educar_falta_atraso_lst.php", $total, $_GET, $this->nome, $this->limite );
		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 7 ) )
		{
			$this->array_botao[] 	 = "Novo";
			$this->array_botao_url[] = "educar_falta_atraso_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
		}
		$this->array_botao[] 	 = "Voltar";
		$this->array_botao_url[] = "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}";
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