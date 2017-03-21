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
		$this->SetTitulo( "{$this->_instituicao} Grafico de Notícias por Tipo" );
		$this->processoAp = "109";
	}
}

class indice extends clsCadastro
{
	var $data_inicial,
		$link,
		$data_final;

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
		$this->campoData("data_inicial","Data Inicial","");
		$this->campoData("data_final","Data Final","");
	}
	
	function Novo()
	{
		$totais = array();
		$legenda = array();
		$ObjPecasSaida = new clsPecasSaida();
		if(!$this->data_inicial)
		{
			$this->data_inicial =false;
		}else 
		{
			$data = explode("/", $this->data_inicial);
			$this->data_inicial = "{$data[2]}-{$data[1]}-{$data[0]}";
		}
		if(!$this->data_final)
		{
			$this->data_final = false;
		}else 
		{
			$data = explode("/", $this->data_final);
			$this->data_final = "{$data[2]}-{$data[1]}-{$data[0]}";
		}
		$where = "";
		if( $this->data_inicial )
		{
			$where .= "data_noticia >= '{$this->data_inicial}' AND ";
		}
		if( $this->data_final )
		{
			$where .= "data_noticia <= '{$this->data_final}' AND ";
		}
		// gera a lista de pecas utilizadas no intervalo de tempo definido
		$db = new clsBanco();
		$db->Consulta( "SELECT nm_tipo, COUNT(0) AS total FROM not_portal, not_portal_tipo, not_tipo WHERE $where ref_cod_not_portal = cod_not_portal AND cod_not_tipo = ref_cod_not_tipo GROUP BY nm_tipo,  ref_cod_not_tipo" );
		$arr = array();
		while ( $db->ProximoRegistro() )
		{
			list( $nome, $qtd ) = $db->Tupla();
			$arr[$nome] = $qtd;
		}
		if( count( $arr ) )
		{
			$titulo = "Gráfico de Notícias por tipo";
			if( $this->data_inicial )
			{
				if( ! $this->data_final )
				{
					$titulo .= " - A partir de {$this->data_inicial}";
				}
				else 
				{
					$titulo .= " - De {$this->data_inicial} até {$this->data_final}";
				}
			}
			else 
			{
				if( $this->data_final )
				{
					$titulo .= " - Até {$this->data_final}";
				}
			}
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