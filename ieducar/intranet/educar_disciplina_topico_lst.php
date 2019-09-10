<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Disciplina T&oacute;pico" );
        $this->processoAp = "565";
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
    
    var $cod_disciplina_topico;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_topico;
    var $desc_topico;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    
    function Gerar()
    {
        $this->titulo = "Disciplina T&oacute;pico - Listagem";
        
        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;
        
        
    
        $this->addCabecalhos( array( 
            "Nome T&oacute;pico"
        ) );
        
        // Filtros de Foreign Keys


        // outros Filtros
        $this->campoTexto( "nm_topico", "Nome T&oacute;pico", $this->nm_topico, 30, 255, false );
        
        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;
        
        $obj_disciplina_topico = new clsPmieducarDisciplinaTopico();
        $obj_disciplina_topico->setOrderby( "nm_topico ASC" );
        $obj_disciplina_topico->setLimite( $this->limite, $this->offset );
        
        $lista = $obj_disciplina_topico->lista(
            null,
            null,
            null,
            $this->nm_topico,
            null,
            null,
            null,
            null,
            null,
            1
        );
        
        $total = $obj_disciplina_topico->_total;
        
        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $this->addLinhas( array( 
                    "<a href=\"educar_disciplina_topico_det.php?cod_disciplina_topico={$registro["cod_disciplina_topico"]}\">{$registro["nm_topico"]}</a>",
                ) );
            }
        }
        $this->addPaginador2( "educar_disciplina_topico_lst.php", $total, $_GET, $this->nome, $this->limite );
        
        $objPermissao = new clsPermissoes();
        if( $objPermissao->permissao_cadastra( 565, $this->pessoa_logada,7 ) ) {
            $this->acao = "go(\"educar_disciplina_topico_cad.php\")";
            $this->nome_acao = "Novo";
        }
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
