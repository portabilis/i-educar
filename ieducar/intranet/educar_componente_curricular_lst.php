<?php
//error_reporting(E_ERROR);
//ini_set("display_errors", 1);

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/modules/clsModulesComponenteCurricular.inc.php" );
require_once( "modules/ComponenteCurricular/Model/TipoBase.php" );
require_once( "modules/AreaConhecimento/Model/AreaDataMapper.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Componentes curriculares" );
        $this->processoAp = "946";
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

  var $ref_cod_instituicao;
  var $nome;
  var $abreviatura;
  var $tipo_base;
    var $area_conhecimento_id;

    function Gerar()
    {
        $this->titulo = "Componentes curriculares - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $lista_busca = array(
            "Nome",
            "Abreviatura",
      "Base",
      "&Aacute;rea de conhecimento"
        );

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1)
            $lista_busca[] = "Institui&ccedil;&atilde;o";

        $this->addCabecalhos($lista_busca);

        include("include/pmieducar/educar_campo_lista.php");

        // outros Filtros
    $this->campoTexto( "nome", "Nome", $this->nome, 41, 255, false );
        $this->campoTexto( "abreviatura", "Abreviatura", $this->abreviatura, 41, 255, false );

    $tipos = ComponenteCurricular_Model_TipoBase::getInstance();
    $tipos = $tipos->getEnums();
    $tipos = Portabilis_Array_Utils::insertIn(null, 'Selecionar', $tipos);

    $options = array(
      'label'       => 'Base Curricular',
      'placeholder' => 'Base curricular',
      'value'       => $this->tipo_base,
      'resources'   => $tipos,
      'required'    => false
    );

    $this->inputsHelper()->select('tipo_base', $options);

    $objAreas = new AreaConhecimento_Model_AreaDataMapper();
    $objAreas = $objAreas->findAll(array('id', 'nome'));
    $areas = array();

    foreach ($objAreas as $area) {
      $areas[$area->id] = $area->nome;
    }

    $areas = Portabilis_Array_Utils::insertIn(null, 'Selecionar', $areas);

    $options = array(
      'label'       => 'Área de conhecimento',
      'placeholder' => 'Área de conhecimento',
      'value'       => $this->area_conhecimento_id,
      'resources'   => $areas,
      'required'    => false
    );

    $this->inputsHelper()->select('area_conhecimento_id', $options);


        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $objCC = new clsModulesComponenteCurricular();
        $objCC->setOrderby( "cc.nome ASC" );
        $objCC->setLimite( $this->limite, $this->offset );

        $lista = $objCC->lista(
        $this->ref_cod_instituicao,
        $this->nome,
        $this->abreviatura,
        $this->tipo_base,
        $this->area_conhecimento_id
      );

        $total = $objCC->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $obj_cod_instituicao = new clsPmieducarInstituicao( $registro["instituicao_id"] );
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro["instituicao_id"] = $obj_cod_instituicao_det["nm_instituicao"];

                $lista_busca = array(
          "<a href=\"/module/ComponenteCurricular/view?id={$registro["id"]}\">{$registro["nome"]}</a>",
          "<a href=\"/module/ComponenteCurricular/view?id={$registro["id"]}\">{$registro["abreviatura"]}</a>",
                    "<a href=\"/module/ComponenteCurricular/view?id={$registro["id"]}\">".$tipos[$registro["tipo_base"]]."</a>",
                    "<a href=\"/module/ComponenteCurricular/view?id={$registro["id"]}\">{$registro["area_conhecimento"]}</a>"
                );

                if ($nivel_usuario == 1)
                    $lista_busca[] = "<a href=\"module/ComponenteCurricular/view?id={$registro["id"]}\">{$registro["instituicao_id"]}</a>";
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2( "educar_componente_curricular_lst.php", $total, $_GET, $this->nome, $this->limite );

        if( $obj_permissoes->permissao_cadastra( 580, $this->pessoa_logada,3 ) )
        {
            $this->acao = "go(\"/module/ComponenteCurricular/edit\")";
            $this->nome_acao = "Novo";
        }
        $this->largura = "100%";

        $this->breadcrumb('Listagem de componentes curriculares', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
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
