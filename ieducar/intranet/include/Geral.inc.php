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

require_once ("include/pessoa/clsPessoa_.inc.php");
require_once ("include/pessoa/clsPessoaFj.inc.php");
require_once ("include/pessoa/clsPessoaJuridica.inc.php");
require_once ("include/pessoa/clsPessoaFisica.inc.php");
require_once ("include/pessoa/clsPessoaTelefone.inc.php");
require_once ("include/pessoa/clsEnderecoPessoa.inc.php");
require_once ("include/pessoa/clsEnderecoExterno.inc.php");
require_once ("include/pessoa/clsEndereco.inc.php");
require_once ("include/pessoa/clsFisicaCpf.inc.php");
require_once ("include/pessoa/clsFisica.inc.php");
require_once ("include/pessoa/clsJuridica.inc.php");
require_once ("include/pessoa/clsCepLogradouroBairro.inc.php");
require_once ("include/pessoa/clsCepLogradouro.inc.php");
require_once ("include/pessoa/clsLogradouro.inc.php");
require_once ("include/pessoa/clsBairro.inc.php");
require_once ("include/pessoa/clsMunicipio.inc.php");
require_once ("include/pessoa/clsUf.inc.php");
require_once ("include/pessoa/clsPais.inc.php");
require_once ("include/pessoa/clsVila.inc.php");
require_once ("include/pessoa/clsTipoLogradouro.inc.php");
require_once ("include/pessoa/clsFuncionario.inc.php");
require_once ("include/pessoa/clsEscolaridade.inc.php");
require_once ("include/pessoa/clsEstadoCivil.inc.php");
require_once ("include/pessoa/clsOcupacao.inc.php");
require_once ("include/pessoa/clsFisica.inc.php");
require_once ("include/pessoa/clsOrgaoEmissorRg.inc.php");
require_once ("include/pessoa/clsDocumento.inc.php");
require_once ("include/pessoa/clsRegiao.inc.php");
require_once ("include/pessoa/clsEscolaridade.inc.php");
require_once ("include/pessoa/clsCadastroEscolaridade.inc.php");
require_once ("include/pessoa/clsCadastroDeficiencia.inc.php");
require_once ("include/pessoa/clsCadastroFisicaDeficiencia.inc.php");
require_once( "include/pmidrh/clsSetor.inc.php" );



require_once ("include/pmidrh/geral.inc.php");
require_once ("include/pessoa/clsBairroRegiao.inc.php");
require_once ("include/funcoes.inc.php");
require_once ( "include/clsParametrosPesquisas.inc.php" );
require_once ("include/portal/geral.inc.php");
require_once ("include/public/geral.inc.php");
require_once ("include/urbano/geral.inc.php");


?>