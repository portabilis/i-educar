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
require_once ("include/clsGrafico.inc.php");

class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Grafico de Mailling por quantidade diaria" );
		$this->processoAp = "0";
	}
}

class indice extends clsCadastro
{
	var $ano,
		$link,
		$mes;

	function Inicializar()
	{
		@session_start();
		$this->cod_pessoa_fj = $_SESSION['id_pessoa'];
		session_write_close();
		$retorno = "Novo";
		return $retorno;
	}
	
	function Gerar()
	{
		$dia = date( "d", time() );
		$mes = date( "m", time() );
		$ano = date( "Y", time() );
		$opcoes = array();
		for( $i = 2005; $i <= $ano; $i++ )
		{
			$opcoes[$i] = "$i";
		}
		$this->campoLista( "ano","Ano", $opcoes, $ano );
		$opcoes = array();
		$opcoes[1] = "Janeiro";
		$opcoes[2] = "Fevereiro";
		$opcoes[3] = "Março";
		$opcoes[4] = "Abril";
		$opcoes[5] = "Maio";
		$opcoes[6] = "Junho";
		$opcoes[7] = "Julho";
		$opcoes[8] = "Agosto";
		$opcoes[9] = "Setembro";
		$opcoes[10] = "Outubro";
		$opcoes[11] = "Novembro";
		$opcoes[12] = "Dezembro";
		$this->campoLista("mes","Mes",$opcoes, $mes );
	}
	
	function Novo()
	{
		$totais = array();
		$legenda = array();
		
		$dataInicio = "{$this->ano}/{$this->mes}/01 00:00:00";
		if( $this->mes < 12 )
		{
			$dataFim = "{$this->ano}/" . ( $this->mes + 1 ) . "/01 00:00:00";
		}
		else 
		{
			$dataFim = ( $this->ano + 1 ) . "/01/01 00:00:00";
		}
		
		$arr = array();
		$meses = array( '', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro' );
		
		// gera a lista de pecas utilizadas no intervalo de tempo definido
		$db = new clsBanco();
		$db->Consulta( "SELECT DAY( data_hora ) AS dia, COUNT( ref_cod_mailling_email ) AS total  FROM mailling_historico, mailling_grupo_email WHERE data_hora >= '{$dataInicio}' AND data_hora <= '{$dataFim}' AND mailling_grupo_email.ref_cod_mailling_grupo = mailling_historico.ref_cod_mailling_grupo GROUP BY dia ORDER BY dia ASC" );
		while ( $db->ProximoRegistro() )
		{
			list( $dia, $qtd ) = $db->Tupla();
			$arr[$dia] = $qtd;
		}
		if( count( $arr ) )
		{
			$titulo = "Gráfico de Mailling Diário - {$meses[$this->mes]} de {$this->ano}";
			$grafico = new clsGrafico( $arr, $titulo, 500 );
			$grafico->setAlign( "left" );
			die( $grafico->graficoBarraHor() );
		}
		else 
		{
			$this->campoRotulo( "alerta","Alerta", "Nenhum resultado foi encontrado com este filtro");
		}
		$this->largura = "100%";
		return true;
	}

	function Editar()
	{
	}

	function Excluir()
	{
	}
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>