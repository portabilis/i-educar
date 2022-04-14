<?php

return new class extends clsDetalhe {
    public function Gerar()
    {
        $this->titulo = 'Agendas';

        $cod_agenda = $_GET['cod_agenda'] ?? null;

        $db = new clsBanco();
        $db2 = new clsBanco();

        if ($cod_agenda) {
            $db->Consulta("SELECT cod_agenda, nm_agenda, publica, envia_alerta, ref_ref_cod_pessoa_cad, data_cad, ref_ref_cod_pessoa_own FROM portal.agenda WHERE cod_agenda = '{$cod_agenda}'");
        }

        if ($cod_agenda && $db->ProximoRegistro()) {
            list($cod_agenda, $nm_agenda, $publica, $envia_alerta, $pessoa_cad, $data_cad, $pessoa_own) = $db->Tupla();

            $objPessoa = new clsPessoaFisica();
            list($nome) = $objPessoa->queryRapida($pessoa_cad, 'nome');

            $objPessoa_ = new clsPessoaFisica();
            list($nm_pessoa_own) = $objPessoa_->queryRapida($pessoa_own, 'nome');

            $this->addDetalhe(['Código da Agenda', $cod_agenda]);
            $this->addDetalhe(['Agenda', $nm_agenda]);
            $this->addDetalhe(['Pública', ($publica==0) ? $publica='Não' : $pubica = 'Sim' ]);
            $this->addDetalhe(['Envia Alerta', ($envia_alerta==0) ? $envia_alerta='Não' : $envia_alerta= 'Sim' ]);
            $this->addDetalhe(['Quem Cadastrou', $nome]);
            $this->addDetalhe(['Data do Cadastro', date('d/m/Y H:m:s', strtotime(substr($data_cad, 0, 19))) ]);
            $this->addDetalhe(['Dono da Agenda', $nm_pessoa_own]);

            $editores = '';
            if ($nm_pessoa_own) {
                $editores .= "<b>$nm_pessoa_own</b><br>";
            }

            $edit_array = [];
            $db2->Consulta("SELECT ref_ref_cod_pessoa_fj FROM portal.agenda_responsavel WHERE ref_cod_agenda = '{$cod_agenda}'");
            while ($db2->ProximoRegistro()) {
                list($nome) = $objPessoa->queryRapida($db2->Campo('ref_ref_cod_pessoa_fj'), 'nome');
                $edit_array[] = $nome;
            }

            if (! count($edit_array)) {
                if (! $nm_pessoa_own) {
                    $editores .= 'Nenhum editor cadastrado';
                }
            } else {
                asort($edit_array);
                reset($edit_array);
                $editores .= implode('<br>', $edit_array);
            }
            $this->addDetalhe(['Editores autorizados', $editores ]);
        } else {
            $this->addDetalhe([ 'Erro', 'Codigo de agenda inválido' ]);
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(554, $this->pessoa_logada, 7, null, true)) {
            $this->url_editar = "agenda_admin_cad.php?cod_agenda={$cod_agenda}";
            $this->url_novo = 'agenda_admin_cad.php';
        }

        $this->url_cancelar = 'agenda_admin_lst.php';

        $this->largura = '100%';

        $this->breadcrumb('Detalhe da agenda');
    }

    public function Formular()
    {
        $this->title = 'Agenda';
        $this->processoAp = '343';
    }
};
