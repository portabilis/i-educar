<?php

return new class extends clsDetalhe
{
    public function Gerar()
    {
        $this->titulo = 'Agendas';

        $cod_agenda = $_GET['cod_agenda'] ?? null;

        $db = new clsBanco();
        $db2 = new clsBanco();

        if ($cod_agenda) {
            $db->Consulta(consulta: "SELECT cod_agenda, nm_agenda, publica, envia_alerta, ref_ref_cod_pessoa_cad, data_cad, ref_ref_cod_pessoa_own FROM portal.agenda WHERE cod_agenda = '{$cod_agenda}'");
        }

        if ($cod_agenda && $db->ProximoRegistro()) {
            [$cod_agenda, $nm_agenda, $publica, $envia_alerta, $pessoa_cad, $data_cad, $pessoa_own] = $db->Tupla();

            $objPessoa = new clsPessoaFisica();
            [$nome] = $objPessoa->queryRapida($pessoa_cad, 'nome');

            $objPessoa_ = new clsPessoaFisica();
            [$nm_pessoa_own] = $objPessoa_->queryRapida($pessoa_own, 'nome');

            $this->addDetalhe(detalhe: ['Código da Agenda', $cod_agenda]);
            $this->addDetalhe(detalhe: ['Agenda', $nm_agenda]);
            $this->addDetalhe(detalhe: ['Pública', ($publica == 0) ? $publica = 'Não' : $pubica = 'Sim']);
            $this->addDetalhe(detalhe: ['Envia Alerta', ($envia_alerta == 0) ? $envia_alerta = 'Não' : $envia_alerta = 'Sim']);
            $this->addDetalhe(detalhe: ['Quem Cadastrou', $nome]);
            $this->addDetalhe(detalhe: ['Data do Cadastro', date(format: 'd/m/Y H:m:s', timestamp: strtotime(datetime: substr(string: $data_cad, offset: 0, length: 19)))]);
            $this->addDetalhe(detalhe: ['Dono da Agenda', $nm_pessoa_own]);

            $editores = '';
            if ($nm_pessoa_own) {
                $editores .= "<b>$nm_pessoa_own</b><br>";
            }

            $edit_array = [];
            $db2->Consulta(consulta: "SELECT ref_ref_cod_pessoa_fj FROM portal.agenda_responsavel WHERE ref_cod_agenda = '{$cod_agenda}'");
            while ($db2->ProximoRegistro()) {
                [$nome] = $objPessoa->queryRapida($db2->Campo(Nome: 'ref_ref_cod_pessoa_fj'), 'nome');
                $edit_array[] = $nome;
            }

            if (!count(value: $edit_array)) {
                if (!$nm_pessoa_own) {
                    $editores .= 'Nenhum editor cadastrado';
                }
            } else {
                asort(array: $edit_array);
                reset(array: $edit_array);
                $editores .= implode(separator: '<br>', array: $edit_array);
            }
            $this->addDetalhe(detalhe: ['Editores autorizados', $editores]);
        } else {
            $this->addDetalhe(detalhe: ['Erro', 'Codigo de agenda inválido']);
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 554, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: null, super_usuario: true)) {
            $this->url_editar = "agenda_admin_cad.php?cod_agenda={$cod_agenda}";
            $this->url_novo = 'agenda_admin_cad.php';
        }

        $this->url_cancelar = 'agenda_admin_lst.php';

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe da agenda');
    }

    public function Formular()
    {
        $this->title = 'Agenda';
        $this->processoAp = '343';
    }
};
