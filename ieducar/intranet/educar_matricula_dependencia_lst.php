<?php
//error_reporting(E_ERROR);
//ini_set("display_errors", 1);
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
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Matr&iacute;cula depend&ecirc;ncia" );
		$this->processoAp = "578";
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

	var $ref_cod_matricula;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();
    $obj_permissoes = new clsPermissoes();

		$this->titulo = "Matr&iacute;cula - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		if ( !$this->ref_cod_matricula )
		{
			header( "location: educar_aluno_lst.php" );
			die();
		}
    $this->campoOculto("ref_cod_matricula",$this->ref_cod_matricula);

    $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();

    $componentes = $componenteMapper->findAll();
    $resourcesComponente = array(null => 'Selecione');

    foreach ($componentes as $key => $value) {
      $resourcesComponente[$value->id] = $value->nome;
    }

    $this->inputsHelper()->select('componente_curricular_id', array('label' => 'Disciplina', 'resources' => $resourcesComponente));



		$lista_busca = array(
      "Ano",
      "Escola",
      "Curso",
			"S&eacute;rie",
      "Disciplina",
			"Situa&ccedil;&atilde;o"
		);

		$this->addCabecalhos($lista_busca);

		// Paginador
		$this->limite = 10;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_matricula = new clsPmieducarMatriculaDependencia();
		$obj_matricula->setLimite( $this->limite, $this->offset );

		$lista = $obj_matricula->lista(
			$this->ref_cod_matricula, $this->componente_curricular_id);

		$total = $obj_matricula->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				if( class_exists( "clsPmieducarCurso" ) )
				{
					$obj_ref_cod_curso = new clsPmieducarCurso( $registro["ref_cod_curso"] );
					$det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
					$registro["ref_cod_curso"] = $det_ref_cod_curso["nm_curso"];
				}
				else
				{
					$registro["ref_cod_curso"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarCurso\n-->";
				}
				if( class_exists( "clsPmieducarSerie" ) )
				{
					$obj_serie = new clsPmieducarSerie( $registro["ref_cod_serie"] );
					$det_serie = $obj_serie->detalhe();
					$registro["ref_cod_serie"] = $det_serie["nm_serie"];
				}
				else
				{
					$registro["ref_cod_serie"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarSerie\n-->";
				}

				if( class_exists( "clsPmieducarEscola" ) )
				{
					$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
					$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
					$registro["ref_cod_escola"] = $det_ref_cod_escola["nome"];
				}

        $situacao = $registro['aprovado'];
        if ($situacao == 1)
          $situacao = 'Aprovado';
        elseif ($situacao == 2)
          $situacao = 'Reprovado';
        elseif ($situacao == 3)
          $situacao = 'Em Andamento';
        elseif ($situacao == 4)
          $situacao = 'Transferido';
        elseif ($situacao == 5)
          $situacao = 'Reclassificado';
        elseif ($situacao == 6)
          $situacao = 'Abandono';

        $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();

        $componente = $componenteMapper->find($registro['componente_curricular_id']);
        $registro['componente_curricular_id'] = $componente->nome;

				$lista_busca = array();

   			$lista_busca[] = "<a href=\"educar_matricula_dependencia_det.php?cod_matricula_dependencia={$registro["cod_matricula_dependencia"]}\">{$registro["ano"]}</a>";

        $lista_busca[] = "<a href=\"educar_matricula_dependencia_det.php?cod_matricula_dependencia={$registro["cod_matricula_dependencia"]}\">{$registro["ref_cod_escola"]}</a>";
        $lista_busca[] = "<a href=\"educar_matricula_dependencia_det.php?cod_matricula_dependencia={$registro["cod_matricula_dependencia"]}\">{$registro["ref_cod_curso"]}</a>";
        $lista_busca[] = "<a href=\"educar_matricula_dependencia_det.php?cod_matricula_dependencia={$registro["cod_matricula_dependencia"]}\">{$registro["ref_cod_serie"]}</a>";

				$lista_busca[] = "<a href=\"educar_matricula_dependencia_det.php?cod_matricula_dependencia={$registro["cod_matricula_dependencia"]}\">{$registro["componente_curricular_id"]}</a>";

        $lista_busca[] = "<a href=\"educar_matricula_dependencia_det.php?cod_matricula_dependencia={$registro["cod_matricula_dependencia"]}\">$situacao</a>";


				$this->addLinhas($lista_busca);
			}
		}

		$this->addPaginador2( "educar_matricula_dependencia_lst.php", $total, $_GET, $this->nome, $this->limite );
		if( $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7 ) )
		{
			$this->acao = "go(\"educar_matricula_dependencia_cad.php?ref_cod_matricula={$this->ref_cod_matricula}\")";
			$this->nome_acao = "Nova matr&iacute;cula de depend&ecirc;ncia";
		}
		$this->array_botao[] = 'Voltar';
		$this->array_botao_url[] = "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}";

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