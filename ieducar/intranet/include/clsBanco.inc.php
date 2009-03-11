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
if( ! class_exists( "clsBancoSql_" ) )
{
	require_once ( "include/clsBancoPgSql.inc.php" );
}

class clsBanco extends clsBancoSQL_
{

	/*protected*/var $strHost			=	"localhost";			// Nome ou ip do servidor de dados
	/*protected*/var $strBanco			=	"ieducardb";				// Nome do Banco de Dados
	/*protected*/var $strUsuario			=	"ieducaruser";			// Usu&aacute;rio devidamente autorizado a acessar o Banco
	/*protected*/var $strSenha			=	"ieducar";		// Senha do Usu&aacute;rio do Banco

	/*protected*/var $bLink_ID			=	0;				// Identificador de Conex&atilde;o
	/*protected*/var $bConsulta_ID		=	0;				// Identificador de Resultado de Consulta
	/*protected*/var $arrayStrRegistro		=	array();				// Tupla resultante de uma consulta
	/*protected*/var $iLinha			=	0;				// Ponteiro interno para a Tupla atual da consulta

	/*protected*/var $bErro_no			=	0;				// Se ocorreu erro na consulta, retorna Falso
	/*protected*/var $strErro			=	"";				// Frase de descri&ccedil;&atilde;o do Erro Retornado
	/*protected*/var $bDepurar			=	false;				// Ativa ou desativa fun&ccedil;oes de depura&ccedil;&atilde;o

	/*protected*/var $bAuto_Limpa		=	false;				//" 1" para limpar o resultado assim que chegar ao &uacute;ltimo registro

	/*private*/var $strStringSQL			=	"";

	/*protected*/var $strType = "";
	/*protected*/var $arrayStrFields = array();
	/*protected*/var $arrayStrFrom = array();
	/*protected*/var $arrayStrWhere = array();
	/*protected*/var $arrayStrOrderBy = array();
	/*protected*/var $arrayStrGroupBy = array();
	/*protected*/var $iLimitInicio;
	/*protected*/var $iLimitQtd;
	/*protected*/var $arrayStrArquivo = "";

	function clsBanco( $strDataBase = false )
	{
		if( $strDataBase )
		{
			//$this->strBanco = $strDataBase;
		}
	}
}
?>
