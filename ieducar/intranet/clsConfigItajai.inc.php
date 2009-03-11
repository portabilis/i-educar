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
include_once("include/constants.inc.php");
/**
* classe config
* todas devem ser derivadas desta para poderem compartilhar das
* variáveis de caminho.
**/
class clsConfig
{
	/**
	* variavel config, terá todos os caminhos e configura&ccedil;ões
	* necessária para qualquer página.
	**/
	/*protected */var $arrayConfig = array();

	/**
	* variavel de depura&ccedil;&atilde;o: com vários níveis:
	* desligado: 0;
	* em comentário html: 1;
	* em bro(nw)ser: 2;
	**/
	/*public */var $depurar = 0;

	/**
	* fun&ccedil;&atilde;o de constru&ccedil;&atilde;o:
	* em php5 tem o nome de __construct, no php4 é o mesmo nome
	* da classe.
	**/
	function /*__construct */clsConfig ()
	{
		/* HttpType pode ser: http, https, shtp */
		$this->arrayConfig['strHttpType'] = "http://";

		/* Host */
		$this->arrayConfig['strHost'] = "http://ieducar.dccobra.com.br";

		/* diretório de trabalho, se for na raiz deve-se mante-lo vazio. */
		$this->arrayConfig['strDirPublic'] = "/";

		/* depende muito no php.ini pois se lá a configura&ccedil;&atilde;o do diretorio de
		* include já estiver setada, este dir pode ser mandito em '' */
		$this->arrayConfig['strDirPrivate'] = "/";

		/* uniao de todos os dir's formando o diretorio caminho da aplica&ccedil;&atilde;o. */
		$this->arrayConfig['strDirComplete'] = $this->arrayConfig['strHttpType'].$this->arrayConfig['strHost'].$this->arrayConfig['strDirPublic'];

		/* diretorio de onde estar&atilde;o os css que será uma das camadas a serem programadas */
		$this->arrayConfig['strDirStyles'] = $this->arrayConfig['strDirComplete']."styles/";

		/* diretorio de onde dever&atilde;o estar todas as imagens da aplica&ccedil;&atilde;o.
		* ps.: n&atilde;o confundir com diretorio de fotos. */
		$this->arrayConfig['strDirImages'] = $this->arrayConfig['strDirComplete']."imagens/";

		/* diretorio de scripts em javascript, neste diretorio n&atilde;o deve ser permitido o list*/
		$this->arrayConfig['strDirScript'] = $this->arrayConfig['strDirComplete']."scripts/";

		/* diretorio dos templates que s&atilde;o os responsáveis pelo design da aplica&ccedil;&atilde;o. */
		$this->arrayConfig['strDirTemplates'] = "templates/";

		/*
			configuracoes de tempo, para depuracao e controle de qualidade
		*/

		// quantidade de segundos maxima permitida para o processamento total de uma pagina passando este limite a pagina envia um alerta
		$this->arrayConfig['intSegundosProcessaPagina'] = 5;

		// quantidade de segundos maxima permitida para a execucao de uma consulta ao banco passando este limite a pagina envia um alerta
		$this->arrayConfig['intSegundosQuerySQL'] = 3;

		// emails para os quais reports administrativos (debugs e afins) devem ser encaminhados
		$this->arrayConfig['ArrStrEmailsAdministradores'] = array( "edmilson.silva@cobra.com.br" );

		$this->Depurar( $this->arrayConfig );
		
		$this->_instituicao = "Cobra - ";
	}

	/** func&atilde;o de depura&ccedil;&atilde;o.
	* controlado pela variável de depura&ccedil;&atilde;o retorna ao programador
	* a situa&ccedil;&atilde;o atual do script
	**/
	/*protected */function Depurar ( $msg )
	{
		if ($this->depurar)
		{
			if ($this->depurar == 1)
				echo "\n\n<!--";

			echo "<pre>";

			if (is_array( $msg ) )
				var_dump ($msg);
			else
				echo $msg;

			echo "</pre>";

			if ($this->depurar == 1)
				echo "-->\n\n";
		}
	}
}

?>
