<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/pmieducar/geral.inc.php");

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} i-Educar - Etapa");
        $this->processoAp = "584";
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

    var $cod_modulo;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $descricao;
    var $num_etapas;
    var $num_meses;
    var $num_semanas;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Gerar()
    {
        $this->titulo = "Etapa - Listagem";

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET AS $var => $val) {
            $this->$var = ($val === "") ? null : $val;
        }

        $lista_busca = array(
            "Etapa",
            "Número de meses"
        );

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = "Instituição";
        }

        $this->addCabecalhos($lista_busca);

        // Filtros de Foreign Keys
        include ("include/pmieducar/educar_campo_lista.php");

        // outros Filtros
        $this->campoTexto("nm_tipo", "Etapa", $this->nm_tipo, 30, 255, false);
        $this->campoNumero("num_meses", "Número de meses", $this->num_meses, 2, 2, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_modulo = new clsPmieducarModulo();
        $obj_modulo->setOrderby("nm_tipo ASC");
        $obj_modulo->setLimite($this->limite, $this->offset);

        $lista = $obj_modulo->lista(
            null,
            null,
            null,
            $this->nm_tipo,
            null,
            $this->num_meses,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_instituicao
        );

        $total = $obj_modulo->_total;

        // monta a lista
        if(is_array($lista) && count($lista))
        {
            foreach ($lista AS $registro)
            {
                $obj_cod_instituicao = new clsPmieducarInstituicao($registro["ref_cod_instituicao"]);
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro["ref_cod_instituicao"] = $obj_cod_instituicao_det["nm_instituicao"];

                $lista_busca = array(
                    "<a href=\"educar_modulo_det.php?cod_modulo={$registro["cod_modulo"]}\">{$registro["nm_tipo"]}</a>",
                    "<a href=\"educar_modulo_det.php?cod_modulo={$registro["cod_modulo"]}\">{$registro["num_meses"]}</a>"
                );

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_modulo_det.php?cod_modulo={$registro["cod_modulo"]}\">{$registro["ref_cod_instituicao"]}</a>";
                }
                $this->addLinhas($lista_busca);
            }
        }

        $this->addPaginador2("educar_modulo_lst.php", $total, $_GET, $this->nome, $this->limite);

        if($obj_permissoes->permissao_cadastra(584, $this->pessoa_logada, 3))
        {
            $this->acao = "go(\"educar_modulo_cad.php\")";
            $this->nome_acao = "Novo";
        }

        $this->largura = "100%";

        $this->breadcrumb('Listagem de etapas', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm($miolo);
// gera o html
$pagina->MakeAll();
?>
