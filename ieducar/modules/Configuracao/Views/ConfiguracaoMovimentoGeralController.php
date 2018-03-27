<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';

class clsIndexBase extends clsBase
{

    function Formular() {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Configuração movimento geral');
        $this->processoAp = 9998866;
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsCadastro
{
    public $_formMap    = array(
        'serie-0' => array(
            'label' => 'Educação infantil',
            'help'  => ''
        ),
        'serie-1' => array(
            'label' => '1° ano',
            'help'  => ''
        ),
        'serie-2' => array(
            'label' => '2° ano',
            'help'  => ''
        ),
        'serie-3' => array(
            'label' => '3° ano',
            'help'  => ''
        ),
        'serie-4' => array(
            'label' => '4° ano',
            'help'  => ''
        ),
        'serie-5' => array(
            'label' => '5° ano',
            'help'  => ''
        ),
        'serie-6' => array(
            'label' => '6° ano',
            'help'  => ''
        ),
        'serie-7' => array(
            'label' => '7° ano',
            'help'  => ''
        ),
        'serie-8' => array(
            'label' => '8° ano',
            'help'  => ''
        ),
        'serie-9' => array(
            'label' => '9° ano',
            'help'  => ''
        )
    );

    function Inicializar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(9998866, $_SESSION['id_pessoa'], 1,
            'educar_index.php');
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
            $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
            "educar_configuracoes_index.php"    => "Configurações",
            ""                                  => "Configuração movimento geral"
        ));
        $this->enviaLocalizacao($localizacao->montar());
        return 'Editar';
    }

    public function Gerar() {

        foreach ($this->_formMap as $key => $value){
//            $this->inputsHelper()->multipleSearchSerie($key, array('label' => $this->_getLabel($key), 'required' => false));
        }
    }

    function Editar()
    {

//        $configuracoes = new clsPmieducarConfiguracoesGerais($ref_cod_instituicao, $permiteRelacionamentoPosvendas, $this->url_novo_educacao);
//        $detalheAntigo = $configuracoes->detalhe();
//        $editou = $configuracoes->edita();
//
//        if( $editou )
//        {
//            $detalheAtual = $configuracoes->detalhe();
//            $auditoria = new clsModulesAuditoriaGeral("configuracoes_gerais", $this->pessoa_logada, $ref_cod_instituicao ? $ref_cod_instituicao : 'null');
//            $auditoria->alteracao($detalheAntigo, $detalheAtual);
//            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
//            header( "Location: index.php" );
//            die();
//            return true;
//        }
//
//        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
//        return false;
    }

}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();

?>
