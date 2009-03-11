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
	require_once( "include/clsBanco.inc.php" );
	require_once( "include/Geral.inc.php" );

	require_once( "include/pmidrh/clsPmidrhCargos.inc.php" );
	require_once( "include/pmidrh/clsPmidrhDiaria.inc.php" );
	require_once( "include/pmidrh/clsPmidrhDiariaGrupo.inc.php" );
	require_once( "include/pmidrh/clsPmidrhDiariaValores.inc.php" );
	require_once( "include/pmidrh/clsPmidrhLogVisualizacaoOlerite.inc.php" );
	require_once( "include/pmidrh/clsPmidrhPortaria.inc.php" );
	require_once( "include/pmidrh/clsPmidrhPortariaCamposEspeciaisValor.inc.php" );
	require_once( "include/pmidrh/clsPmidrhPortariaCamposTabela.inc.php" );
	require_once( "include/pmidrh/clsPmidrhPortariaFuncionario.inc.php" );
	require_once( "include/pmidrh/clsPmidrhStatus.inc.php" );
	require_once( "include/pmidrh/clsPmidrhTipoPortaria.inc.php" );
	require_once( "include/pmidrh/clsPmidrhTipoPortariaCamposEspeciais.inc.php" );
	require_once( "include/pmidrh/clsPmidrhPortariaCamposTabelaValor.inc.php" );
	require_once( "include/pmidrh/clsPmidrhPortariaResponsavel.inc.php" );
	require_once( "include/pmidrh/clsPmidrhPortariaAssinatura.inc.php" );
	require_once( "include/pmidrh/clsPmidrhUsuario.inc.php" );
	require_once( "include/pmidrh/clsPmidrhInstituicao.inc.php" );
	require_once( "include/pmidrh/clsSetor.inc.php" );

?>