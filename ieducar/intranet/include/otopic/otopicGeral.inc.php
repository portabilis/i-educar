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
	// arquivo que faz o require de todas as classes de otopic
	require_once( "include/otopic/clsTopico.inc.php" );	
	require_once( "include/otopic/clsTopicoReuniao.inc.php" );	
	require_once( "include/otopic/clsNotas.inc.php" );	
	require_once( "include/otopic/clsGrupos.inc.php" );	
	require_once( "include/otopic/clsGrupoPessoa.inc.php" );	
	require_once( "include/otopic/clsGrupoModerador.inc.php" );	
	require_once( "include/otopic/clsReuniao.inc.php" );	
	require_once( "include/otopic/clsParticipante.inc.php" );	
	require_once( "include/otopic/clsFuncionarioSu.inc.php" );	
	require_once( "include/otopic/clsAtendimento.inc.php" );	
	require_once( "include/otopic/clsAtendimentoPessoa.inc.php" );	
	require_once( "include/otopic/clsPessoaAuxiliar.inc.php" );	
	require_once( "include/otopic/clsPessoaObservacao.inc.php" );	
	require_once( "include/otopic/clsPessoaAuxiliarTelefone.inc.php" );	
	require_once( "include/otopic/clsSituacao.inc.php" );
	
	// includes de fora do otopic
	require_once( "include/clsEmail.inc.php" );	
	require_once( "include/clsMenuFuncionario.inc.php" );	
	require_once( "include/clsLogAcesso.inc.php" );	
	require_once( "include/Geral.inc.php" );
?>