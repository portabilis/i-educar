<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

return new class extends clsListagem {
    public $pessoa_logada;
    public $titulo;
    public $limite;
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
            $obj_calendario = new clsPmieducarCalendarioAnoLetivo(cod_calendario_ano_letivo: $this->ref_cod_calendario_ano_letivo);
            if (!$obj_calendario->existe()) {
                throw new HttpResponseException(
                    response: new RedirectResponse(url: 'educar_calendario_ano_letivo_lst.php')
                );
            }
            $this->titulo = "Anotaçõoes Calendário <b>{$this->dia}/{$this->mes}/{$this->ano}</b> - Listagem";

            Session::put([
                'calendario.anotacao.dia' => $this->dia,
                'calendario.anotacao.mes' => $this->mes,
                'calendario.anotacao.ano' => $this->ano,
                'calendario.anotacao.ref_cod_calendario_ano_letivo' => $this->ref_cod_calendario_ano_letivo,
            ]);
        } else {
            $this->simpleRedirect(url: 'educar_calendario_ano_letivo_lst.php');
        }

        $this->addCabecalhos(coluna: [
            'Anotacão',
            'Descrição'
        ]);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_calendario_anotacao_dia = new clsPmieducarCalendarioDiaAnotacao();
        $obj_calendario_anotacao_dia->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $obj_calendario_anotacao_dia->lista(int_ref_dia: $this->dia, int_ref_mes: $this->mes, int_ref_ref_cod_calendario_ano_letivo: $this->ref_cod_calendario_ano_letivo, is_ativo: 1);

        $total = $obj_calendario_anotacao_dia->_total;

        // monta a lista
        $get = "&dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}";
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $obj_calendario_anotacao = new clsPmieducarCalendarioAnotacao(cod_calendario_anotacao: $registro['ref_cod_calendario_anotacao'], ativo: 1);
                $det = $obj_calendario_anotacao->detalhe();
                /*
                    "<a href=\"educar_calendario_anotacao_det.php?cod_calendario_anotacao={$det["cod_calendario_anotacao"]}\">{$registro["ref_dia"]}</a>",
                    "<a href=\"educar_calendario_anotacao_det.php?cod_calendario_anotacao={$det["cod_calendario_anotacao"]}\">{$registro["ref_mes"]}</a>",
                */
                $this->addLinhas(linha: [
                    "<a href=\"educar_calendario_anotacao_cad.php?cod_calendario_anotacao={$det['cod_calendario_anotacao']}{$get}\">{$det['nm_anotacao']}</a>",
                    "<a href=\"educar_calendario_anotacao_cad.php?cod_calendario_anotacao={$det['cod_calendario_anotacao']}{$get}\">{$det['descricao']}</a>"
                ]);
            }
        }
        $this->addPaginador2(strUrl: 'educar_calendario_anotacao_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 620, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->acao = "go(\"educar_calendario_anotacao_cad.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}\")";
            $this->nome_acao = 'Nova Anotação';
            $this->array_botao = ['Dia Extra/Não Letivo','Calendário'];
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
