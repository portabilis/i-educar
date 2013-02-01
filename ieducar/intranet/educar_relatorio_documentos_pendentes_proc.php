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
require_once ("include/clsPDF.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Documentos Pendentes" );
		$this->processoAp = "711";
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


	var $ref_cod_instituicao;
	var $ref_cod_escola;
	var $ref_cod_curso;
	var $ref_cod_serie;
	var $ref_cod_turma;
	var $cod_aluno;
	var $nm_aluno;
	var $nm_responsavel;
	var $cpf_responsavel;

	var $ano;

	var $nm_escola;
	var $nm_instituicao;
	var $nm_curso;
	var $nm_municipio;
	var $nm_localidade;

	var $pdf;

	var $page_y = 139;

	var $get_link;

	var $total_dias_uteis;
	var $total_semanas;

	var $primeiro_dia_semana;
	var $ultimo_dia_semana;

	function renderHTML()
	{

		if($_POST){
			foreach ($_POST as $key => $value) {
				$this->$key = $value;

			}
		}

		if($this->ref_ref_cod_serie)
			$this->ref_cod_serie = $this->ref_ref_cod_serie;

		$fonte = 'arial';
		$corTexto = '#000000';

		$obj_escola_instituicao = new clsPmieducarEscola();
		$lst_escola_instituicao = $obj_escola_instituicao->lista($this->ref_cod_escola, null, null, $this->ref_cod_instituicao, null, null, null, null, null, null,1);

		$this->pdf = new clsPDF("Documentos Pendentes", "Documentos Pendentes", "A4", "", false, false);

		$join = "";
		$where = "";

		if(is_numeric($this->ref_cod_escola))
		{
			$where .= " AND matricula.ref_cod_aluno = cod_aluno ";
			$where .= " AND matricula.ref_ref_cod_escola = {$this->ref_cod_escola} ";

			$join  .= " ,pmieducar.matricula ";
		}

		if(is_numeric($this->ref_cod_curso))
		{
			$where .= " AND matricula.ref_cod_curso = {$this->ref_cod_curso} ";
		}

		if(is_numeric($this->ref_cod_serie))
		{
			$where .= " AND matricula.ref_ref_cod_serie = {$this->ref_cod_serie} ";
		}

		if(is_numeric($this->cod_aluno))
		{
			$where .= " AND cod_aluno = '{$this->cod_aluno}' ";
		}

		if(is_string($this->nm_aluno))
		{
			$where .= " AND nome like '%{$this->nm_aluno}%' ";
		}

		$SELECT = "SELECT cod_aluno
					       ,nome
					       ,data_nasc 			as \"Data de Nascimento\"
					       ,ideciv	   			as \"Estado Civil\"
					       ,idmun_nascimento	as \"Naturalidade\"
					       ,tipo_cert_civil	   	as \"Tipo de Certidão Civil\"
					       ,num_termo	   		as \"Número Termo\"
					       ,num_livro	   		as \"Número Livro\"
					       ,num_folha	   		as \"Número Folha\"
					       ,data_emissao_cert_civil	as \"Data Emissão Civil\"
					       ,sigla_uf_cert_civil	   	as \"Sigla Uf Cert. Civil\"
					       ,cartorio_cert_civil	   	as \"Cartório cert. Civil\"
						   ,cep   		as \"CEP\"
						   ,idbai::text as \"Bairro\"
						   ,idlog::text as \"Logradouro\"
						   ,1::text 	as \"Cidade\"
						   ,1::text 	as \"Estado\"
					  FROM pmieducar.aluno
					       ,cadastro.pessoa
					       ,cadastro.fisica
					       ,cadastro.endereco_pessoa
					       ,cadastro.documento
					       $join
					 WHERE aluno.ref_idpes       = pessoa.idpes
					   AND endereco_pessoa.idpes = pessoa.idpes
					   AND documento.idpes	     = pessoa.idpes
					   AND fisica.idpes	     = pessoa.idpes
					   AND fisica.idpes	     = documento.idpes
					   AND fisica.idpes	     = aluno.ref_idpes
					   AND documento.idpes	 = aluno.ref_idpes
					   $where
					   AND (
						data_nasc IS NULL
					    OR ideciv IS NULL
						OR idmun_nascimento IS NULL
						OR tipo_cert_civil  IS NULL
						OR num_termo	    IS NULL
						OR num_livro	    IS NULL
						OR num_folha	    IS NULL
						OR data_emissao_cert_civil IS NULL
						OR sigla_uf_cert_civil     IS NULL
						OR cartorio_cert_civil     IS NULL
						OR cep   IS NULL
						OR idbai IS NULL
						OR idlog IS NULL
					       )

					UNION

					SELECT cod_aluno
					       ,nome
					       ,data_nasc
					       ,ideciv
					       ,idmun_nascimento
					       ,tipo_cert_civil
					       ,num_termo
					       ,num_livro
					       ,num_folha
					       ,data_emissao_cert_civil
					       ,sigla_uf_cert_civil
					       ,cartorio_cert_civil
					       ,cep
					       ,bairro
					       ,logradouro
					       ,cidade
					       ,sigla_uf
					  FROM pmieducar.aluno
					       ,cadastro.pessoa
					       ,cadastro.fisica
					       ,cadastro.endereco_externo
					       ,cadastro.documento
					       $join
					 WHERE aluno.ref_idpes        = pessoa.idpes
					   AND endereco_externo.idpes = pessoa.idpes
					   AND documento.idpes	      = pessoa.idpes
					   AND fisica.idpes	     = pessoa.idpes
					   AND fisica.idpes	     = aluno.ref_idpes
					   AND fisica.idpes	     = documento.idpes
					   $where
					   AND (
						data_nasc IS NULL
					    OR ideciv IS NULL
						OR idmun_nascimento IS NULL
						OR tipo_cert_civil  IS NULL
						OR num_termo	    IS NULL
						OR num_livro	    IS NULL
						OR num_folha	    IS NULL
						OR data_emissao_cert_civil IS NULL
						OR sigla_uf_cert_civil     IS NULL
						OR cartorio_cert_civil     IS NULL
						OR logradouro IS NULL
						OR bairro     IS NULL
						OR cidade     IS NULL
						OR sigla_uf   IS NULL
						OR cep        IS NULL
					       )
					ORDER BY nome
					";


		if($this->ref_cod_escola){

			$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
			$det_escola = $obj_escola->detalhe();
			$this->nm_escola = $det_escola['nome'];

			$obj_instituicao = new clsPmieducarInstituicao($this->ref_cod_instituicao);
			$det_instituicao = $obj_instituicao->detalhe();
			$this->nm_instituicao = $det_instituicao['nm_instituicao'];

			if($det_escola['ref_idpes'])
			{
				$obj_endereco_escola = new clsEndereco($det_escola['ref_idpes']);
				$det_enderedo_escola = $obj_endereco_escola->detalhe();

				$this->nm_localidade = $this->nm_municipio = $det_enderedo_escola['cidade'];

				if(!$det_enderedo_escola)
				{
					$obj_endereco_externo_escola = new clsEnderecoExterno($det_escola['ref_idpes']);
					$det_enderedo_externo_escola = $obj_endereco_externo_escola->detalhe();

					$this->nm_localidade = $this->nm_municipio = $det_enderedo_externo_escola['cidade'];
				}

			}
			else
			{
				$obj_escola_complemento = new clsPmieducarEscolaComplemento($this->ref_cod_escola);
				$det_escola_complemento = $obj_escola_complemento->detalhe();
				$this->nm_localidade	= $this->nm_municipio = $det_escola_complemento['municipio'];
			}

		}

		$db = new clsBanco();
		$db->Consulta($SELECT);
		if($db->numLinhas())
		{
			$x_quadrado = 30;
			$this->page_y = 120;
			$altura_caixa = 20;

			$this->pdf->OpenPage();
			$this->addCabecalho();
			$total_alunos = 0;
			while ($db->ProximoRegistro()) {

				$tupla = $db->Tupla();

				$dados_pendentes = 0;
				for($id = 2; $id < (count($tupla) / 2); $id++)
				{
					if(!$tupla[$id])
						$dados_pendentes++;
				}

				if($this->page_y + $altura_caixa >= 780)
				{

					$this->page_y = 120;

					$this->pdf->ClosePage();
					$this->pdf->OpenPage();

					$page_open = true;

					$this->addCabecalho();

				}

				if($dados_pendentes)
				{

					$total_alunos++;
					
					if($this->page_y + $altura_caixa * $dados_pendentes >= 780)
					{

						$this->page_y = 120;

						$this->pdf->ClosePage();
						$this->pdf->OpenPage();

						$page_open = true;

						$this->addCabecalho();

					}

					$this->pdf->quadrado_relativo( $x_quadrado, $this->page_y, 535, $altura_caixa,0.1,"#ffffff","#D0D0D0" );
					
					$sql = "SELECT 
								nm_turma, nm_serie 
							FROM 
								pmieducar.matricula, 
								pmieducar.matricula_turma, 
								pmieducar.turma t, 
								pmieducar.serie
							WHERE 
								ref_cod_aluno = {$tupla['cod_aluno']} 
								AND cod_matricula = ref_cod_matricula 
								AND ref_cod_turma = cod_turma 
								AND t.ref_ref_cod_serie = cod_serie";
					$db2 = new clsBanco();
					$db2->Consulta($sql);
					$db2->ProximoRegistro();
					list($nm_turma, $nm_serie) = $db2->Tupla();

					$this->pdf->escreve_relativo( "Aluno:      {$tupla['cod_aluno']} - {$tupla['nome']}         Série:    {$nm_serie}        Turma:      {$nm_turma}", 35, $this->page_y + 5, 400, $altura_caixa, $fonte, 8, $corTexto, 'left' );
					$this->page_y += $altura_caixa;


					$this->pdf->quadrado_relativo( $x_quadrado, $this->page_y, 535, $altura_caixa * $dados_pendentes);
					$this->pdf->linha_relativa( $x_quadrado, $this->page_y, 535, 0, '1');

					foreach ($tupla as $key => $valor)
					{

						if(!$valor && !is_numeric($key))
						{

							$this->pdf->escreve_relativo( $key, 35, $this->page_y + 5, 300, $altura_caixa, $fonte, 8, $corTexto, 'left' );
							$this->page_y += $altura_caixa;
						}
					}
				}
			}
			if ($total_alunos != 0)
			{
				$this->pdf->quadrado_relativo( $x_quadrado, $this->page_y, 535, $altura_caixa * $dados_pendentes);
				$this->pdf->linha_relativa( $x_quadrado, $this->page_y, 535, 0, '1');
				$this->pdf->escreve_relativo( "TOTAL: {$total_alunos} alunos", 35, $this->page_y + 5, 400, $altura_caixa, $fonte, 8, $corTexto, 'left' );
				
			}
		}
		else
		{
			echo '<script>alert("Não existem alunos com documentos pendentes para os filtros informados!");window.parent.fechaExpansivel("div_dinamico_" + (window.parent.DOM_divs.length-1)); </script>';
		}





		$this->pdf->CloseFile();
		$this->get_link = $this->pdf->GetLink();


		echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir');window.location='download.php?filename=".$this->get_link."'}</script>";

		echo "<html><center>Se o download não iniciar automaticamente <br /><a target='_blank' href='" . $this->get_link  . "' style='font-size: 16px; color: #000000; text-decoration: underline;'>clique aqui!</a><br><br>
			<span style='font-size: 10px;'>Para visualizar os arquivos PDF, é necessário instalar o Adobe Acrobat Reader.<br>

			Clique na Imagem para Baixar o instalador<br><br>
			<a href=\"http://www.adobe.com.br/products/acrobat/readstep2.html\" target=\"new\"><br><img src=\"imagens/acrobat.gif\" width=\"88\" height=\"31\" border=\"0\"></a>
			</span>
			</center>";
	}

	function addCabecalho()
	{
		// variavel que controla a altura atual das caixas
		$altura = 30;
		$fonte = 'arial';
		$corTexto = '#000000';

		// cabecalho
		$this->pdf->quadrado_relativo( 30, $altura, 535, 85 );
		$this->pdf->InsertJpng( "gif", "imagens/brasao.gif", 50, 95, 0.30 );

		//titulo principal
		$this->pdf->escreve_relativo( "PREFEITURA COBRA TECNOLOGIA", 30, 30, 535, 80, $fonte, 18, $corTexto, 'center' );
		$this->pdf->escreve_relativo( date("d/m/Y"), 500, 30, 100, 80, $fonte, 12, $corTexto, 'left' );

		//dados escola
		$this->pdf->escreve_relativo( "Instituição:$this->nm_instituicao", 120, 58, 300, 80, $fonte, 10, $corTexto, 'left' );
		$this->pdf->escreve_relativo( "Escola:{$this->nm_escola}",136, 70, 300, 80, $fonte, 10, $corTexto, 'left' );

		//titulo
		$this->pdf->escreve_relativo( "Documentos Pendentes", 30, 85, 535, 80, $fonte, 14, $corTexto, 'center' );


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
