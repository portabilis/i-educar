<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

return new class extends clsListagem {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset;

    public $cod_calendario_anotacao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_anotacao;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public $dia;
    public $mes;
    public $ano;

    public $ref_cod_calendario_ano_letivo;

    public function Gerar()
    {
        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        if ($this->ref_cod_calendario_ano_letivo && $this->ano && $this->mes && $this->dia) {
            $obj_calendario = new clsPmieducarCalendarioAnoLetivo($this->ref_cod_calendario_ano_letivo);
            if (!$obj_calendario->existe()) {
                throw new HttpResponseException(
                    new RedirectResponse('educar_calendario_ano_letivo_lst.php')
                );
            }
            $this->titulo = "Anota&ccedil;&otilde;oes Calend&aacute;rio <b>{$this->dia}/{$this->mes}/{$this->ano}</b> - Listagem";

            Session::put([
                'calendario.anotacao.dia' => $this->dia,
                'calendario.anotacao.mes' => $this->mes,
                'calendario.anotacao.ano' => $this->ano,
                'calendario.anotacao.ref_cod_calendario_ano_letivo' => $this->ref_cod_calendario_ano_letivo,
            ]);
        } else {
            $this->simpleRedirect('educar_calendario_ano_letivo_lst.php');
        }

        $this->addCabecalhos([
            'Anotac&atilde;o',
            'Descri&ccedil;&atilde;o'
        ]);

        // Filtros de Foreign Keys

        //// outros Filtros
        //  $this->campoTexto( "nm_anotacao", "Nome Anotac&atilde;o", $this->nm_anotacao, 30, 255, false );

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        /*$obj_calendario_anotacao = new clsPmieducarCalendarioAnotacao();
        $obj_calendario_anotacao->setOrderby( "nm_anotacao ASC" );
        $obj_calendario_anotacao->setLimite( $this->limite, $this->offset );

        $lista = $obj_calendario_anotacao->lista(
            $this->cod_calendario_anotacao,
            null,
            null,
            $this->nm_anotacao,
            $this->descricao,
            null,
            null,
            1
        );*/

        $obj_calendario_anotacao_dia = new clsPmieducarCalendarioDiaAnotacao();
        $obj_calendario_anotacao_dia->setLimite($this->limite, $this->offset);

        $lista = $obj_calendario_anotacao_dia->lista($this->dia, $this->mes, $this->ref_cod_calendario_ano_letivo, null, 1);

        $total = $obj_calendario_anotacao_dia->_total;

        // monta a lista
        $get = "&dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}";
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_calendario_anotacao = new clsPmieducarCalendarioAnotacao($registro['ref_cod_calendario_anotacao'], null, null, null, null, null, null, 1);
                $det = $obj_calendario_anotacao->detalhe();
                /*
                    "<a href=\"educar_calendario_anotacao_det.php?cod_calendario_anotacao={$det["cod_calendario_anotacao"]}\">{$registro["ref_dia"]}</a>",
                    "<a href=\"educar_calendario_anotacao_det.php?cod_calendario_anotacao={$det["cod_calendario_anotacao"]}\">{$registro["ref_mes"]}</a>",
                */
                $this->addLinhas([
                    "<a href=\"educar_calendario_anotacao_cad.php?cod_calendario_anotacao={$det['cod_calendario_anotacao']}{$get}\">{$det['nm_anotacao']}</a>",
                    "<a href=\"educar_calendario_anotacao_cad.php?cod_calendario_anotacao={$det['cod_calendario_anotacao']}{$get}\">{$det['descricao']}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_calendario_anotacao_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(620, $this->pessoa_logada, 7)) {
            $this->acao = "go(\"educar_calendario_anotacao_cad.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}\")";
            $this->nome_acao = 'Nova Anota&ccedil;&atilde;o';
            $this->array_botao = ['Dia Extra/N&atilde;o Letivo','Calend&aacute;rio'];
            $this->array_botao_url = ["educar_calendario_dia_cad.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}","educar_calendario_ano_letivo_lst.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}"];
        }

        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Calendario Anotacao';
        $this->processoAp = '620';
    }
};
