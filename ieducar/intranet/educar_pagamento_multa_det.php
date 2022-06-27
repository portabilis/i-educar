<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $ref_usuario_cad;
    public $ref_cod_cliente;
    public $ref_cod_cliente_tipo;
    public $valor_pago;
    public $data_cadastro;

    public function Gerar()
    {
        $this->titulo = 'Pagamento Multa - Detalhe';

        $this->ref_cod_cliente      = $_GET['cod_cliente'];
        $this->ref_cod_cliente_tipo = $_GET['cod_cliente_tipo'];

        if (!$this->ref_cod_cliente || !$this->ref_cod_cliente_tipo) {
            $this->simpleRedirect('educar_pagamento_multa_lst.php');
        }

        $obj_tipo = new clsPmieducarClienteTipo($this->ref_cod_cliente_tipo);
        $det_tipo = $obj_tipo->detalhe();
        $obj_ref_cod_cliente = new clsPmieducarCliente();
        $lst_ref_cod_cliente = $obj_ref_cod_cliente->listaCompleta($this->ref_cod_cliente, null, null, null, null, null, null, null, null, null, 1, null, null, $this->ref_cod_cliente_tipo);
        if ($lst_ref_cod_cliente) {
            foreach ($lst_ref_cod_cliente as $registro) {
                $this->addDetalhe([ 'Cliente', "{$registro['nome']}"]);
                $this->addDetalhe([ 'Login', "{$registro['login']}"]);

                $obj_divida = new clsPmieducarExemplarEmprestimo();
                $lst_divida = $obj_divida->lista(null, null, null, $registro['cod_cliente'], null, null, null, null, null, null, null, $registro['cod_biblioteca'], true);
                if ($lst_divida) {
                    $tabela = '<TABLE>
                                       <TR align=center>
                                           <TD bgcolor=#ccdce6><B>Data de Devolução</B></TD>
                                           <TD bgcolor=#ccdce6><B>Título</B></TD>
                                           <TD bgcolor=#ccdce6><B>Biblioteca</B></TD>
                                           <TD bgcolor=#ccdce6><B>Valor</B></TD>
                                       </TR>';
                    $cont  = 0;
                    $total = 0;
                    $corpo = '';
                    foreach ($lst_divida as $divida) {
                        $total += $divida['valor_multa'];
                        if (($cont % 2) == 0) {
                            $color = ' bgcolor=#f5f9fd ';
                        } else {
                            $color = ' bgcolor=#FFFFFF ';
                        }
                        $obj_exemplar = new clsPmieducarExemplar($divida['ref_cod_exemplar']);
                        $det_exemplar = $obj_exemplar->detalhe();
                        if ($det_exemplar) {
                            $obj_acervo = new clsPmieducarAcervo($det_exemplar['ref_cod_acervo']);
                            $det_acervo = $obj_acervo->detalhe();
                            $obj_bib    = new clsPmieducarBiblioteca($det_acervo['ref_cod_biblioteca']);
                            $det_bib    = $obj_bib->detalhe();
                        }
                        $corpo .= "<TR>
                                            <TD {$color} align=left>".dataFromPgToBr($divida['data_devolucao'])."</TD>
                                            <TD {$color} align=left>{$det_acervo['titulo']}</TD>
                                            <TD {$color} align=left>{$det_bib['nm_biblioteca']}</TD>
                                            <TD {$color} align=right>".'R$'.number_format($divida['valor_multa'], 2, ',', '.').'</TD>
                                        </TR>';
                        $cont++;
                    }
                    $tabela .= $corpo;
                    if (($cont % 2) == 0) {
                        $color = ' bgcolor=#f5f9fd ';
                    } else {
                        $color = ' bgcolor=#FFFFFF ';
                    }
                    $tabela .= "<TR>
                                        <TD {$color} colspan=3 align=right > <B>Total de dívidas</B> </TD>
                                        <TD {$color} align=right > <B>".'R$'.number_format($total, 2, ',', '.').'</B> </TD>
                                    </TR>';
                    $obj_multa  = new clsPmieducarPagamentoMulta(null, null, $registro['cod_cliente'], null, null, $det_tipo['ref_cod_biblioteca']);
                    $total_pago =  $obj_multa->totalPago();
                    $cont++;
                    if (($cont % 2) == 0) {
                        $color = ' bgcolor=#f5f9fd ';
                    } else {
                        $color = ' bgcolor=#FFFFFF ';
                    }
                    $tabela .= "<TR>
                                        <TD {$color} colspan=3 align=right > <B>Total pago</B> </TD>
                                        <TD {$color} align=right > <B>".'R$'.number_format($total_pago, 2, ',', '.').'</B> </TD>
                                    </TR>';
                    $cont++;
                    if (($cont % 2) == 0) {
                        $color = ' bgcolor=#f5f9fd ';
                    } else {
                        $color = ' bgcolor=#FFFFFF ';
                    }
                    $obj_tot = new clsPmieducarExemplarEmprestimo();
                    $lst_tot = $obj_tot->listaDividaPagamentoCliente($registro['cod_cliente'], null, null, null, $det_tipo['ref_cod_biblioteca']);
                    $total_bib = 0;
                    if ($lst_tot) {
                        foreach ($lst_tot as $total_reg) {
                            $total_bib = $total_reg['valor_multa'];
                        }
                    }
                    $tabela .= "<TR>
                                        <TD {$color} colspan=3 align=right > <B>Total pendente</B> </TD>
                                        <TD {$color} align=right > <B>".'R$'.number_format(($total - $total_pago), 2, ',', '.').'</B> </TD>
                                    </TR>';
                    $tabela .= '</TABLE>';
                    if ($tabela) {
                        $this->addDetalhe([ 'Multa', "{$tabela}"]);
                    }
                }
                $this->ref_cod_cliente = $registro['cod_cliente'];
            }
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(622, $this->pessoa_logada, 11)) {
            $this->caption_novo = 'Pagar';
            $this->url_novo = "educar_pagamento_multa_cad.php?cod_cliente={$this->ref_cod_cliente}&cod_biblioteca={$det_tipo['ref_cod_biblioteca']}";
            $this->url_editar = false;
        }

        $this->url_cancelar = 'educar_pagamento_multa_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da dívida', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Pagamento Multa';
        $this->processoAp = '622';
    }
};
