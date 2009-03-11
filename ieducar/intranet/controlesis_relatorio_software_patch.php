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
require_once( "include/pmieducar/geral.inc.php" );
require_once ("include/relatorio.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - " );
		$this->processoAp = "795";
		$this->renderMenu = false;
		$this->renderMenuSuspenso = false;
	}
}

class indice extends clsCadastro
{


	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;


	var $pdf;

	var $cod_software_patch;


	function renderHTML()
	{

		$relatorio = new relatorios("Relatório de Patch de Software", 210, false, "Patch de Software", "A4", "Prefeitura de Itajaí\nServidores\nRua Tijucas, 511 - Centro\nCEP. 88304-020 - Itajaí - SC");

		//tamanho do retangulo, tamanho das linhas.
		$relatorio->novaPagina();

		$this->cod_software_patch = $_GET['cod_software_patch'];

		$obj_patch = new clsPmicontrolesisSoftwarePatch($this->cod_software_patch);
		$det_patch = $obj_patch->detalhe();

		if(!$det_patch)
		{
			die("<center>Não foi possível encontrar o Patch solicitado</center>");
		}

		$obj_soft = new clsPmicontrolesisSoftware($det_patch['ref_cod_software']);
		$det_soft = $obj_soft->detalhe();


		$data_patch = dataFromPgToBr($det_patch['data_patch'],'Y-d-m');
		$data_patch_ = dataFromPgToBr($det_patch['data_patch'],'d/m/Y');


		$db = new clsBanco();

		$consulta = " SELECT data_patch
				        FROM pmicontrolesis.software_patch
			   	       WHERE ativo = true
				         AND ref_cod_software = 1
						ORDER BY data_patch DESC
						      OFFSET 1
						      LIMIT 1";

		$data_patch_anterior = $db->CampoUnico($consulta);

		$data_patch_anterior_ = dataFromPgToBr($data_patch_anterior,'m/d/Y');
		$data_patch_anterior = dataFromPgToBr($data_patch_anterior,'Y-d-m');


		$where_and = "";
		if($data_patch_anterior)
		{
			$where_and = "  AND ( to_char(data_cadastro,'yyyy-mm-dd') > '{$data_patch_anterior}'
					         OR to_char(data_exclusao,'yyyy-mm-dd')   > '{$data_patch_anterior}'
						    )";
		}

		$consulta = "SELECT *
					   FROM pmicontrolesis.software_alteracao
					  WHERE ativo = true
					    AND ( to_char(data_cadastro,'yyyy-mm-dd')    <= '{$data_patch}'
					          OR to_char(data_exclusao,'yyyy-mm-dd') <= '{$data_patch}'
						    )
						$where_and";

		$db->Consulta($consulta);

		$periodo = $data_patch_ ? "      ALTERAÇÕES:{$data_patch_anterior_}-{$data_patch_}" : "";

		$opcoes_motivo = array('i' => 'Inserção','a' => 'Alteração','e' => 'Exclusão');
		$opcoes_tipo = array('s' => 'Script','b' => 'Banco');
		if($db->Num_Linhas())
		{
			$i = 0;
			$total = $db->Num_Linhas();
			$relatorio->novalinha( array("SISTEMA:  {$det_soft['nm_software']}{$periodo}        DATA PATCH:  {$data_patch_}"),0,13,true,"arial",false,"#d3d3d3","#d3d3d3","#000000");
			while ($db->ProximoRegistro())
			{
				$registro = $db->Tupla();

				$registro['motivo'] = $opcoes_motivo[$registro['motivo']];
				$registro['tipo'] = $opcoes_tipo[$registro['tipo']];

				$num_linhas = 6 + ((int)strlen($registro['descricao']) / 85);
				$data = $registro['data_exclusao'] ? dataFromPgToBr($registro['data_exclusao']) : dataFromPgToBr($registro['data_cadastro']);
				$relatorio->novalinha( array("Descrição de alterações:  {$registro['descricao']}\nMotivo Alteração: {$registro['motivo']}\nTipo Alteração:{$registro['tipo']}\nScript/Banco:{$registro['script_banco']}\nData: {$data}"),0,$num_linhas*10,false,"arial",false,"#ffffff","#ffffff","#ffffff",false,false,null,null,'justify');

				$i++;
				if($i < $total)
				$relatorio->novalinha( array(""),0,5,false,false,false,false,false,false,true );
			}

			$link = $relatorio->fechaPdf();

		}
		else
		{
			$this->campoRotulo("aviso","Aviso", "Nenhuma Registro neste relat&oacute;rio.");
		}






		echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=".$link."'}</script>";

		echo "<html><center>Se o download não iniciar automaticamente <br /><a target='_blank' href='" . $link  . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>clique aqui!</a><br><br>
			<span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>

			Clique na Imagem para Baixar o instalador<br><br>
			<a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
			</span>
			</center>";
	}


	function Editar()
	{
		return false;
	}

	function Excluir()
	{
		return false;
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