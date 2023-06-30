<?php

return new class extends clsCadastro
{
    public $cod_agenda;

    public $ref_ref_cod_pessoa_exc;

    public $ref_ref_cod_pessoa_cad;

    public $nm_agenda;

    public $publica;

    public $envia_alerta;

    public $data_cad;

    public $data_edicao;

    public $ref_ref_cod_pessoa_own;

    public $dono;

    public $editar;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->editar = false;
        if (isset($_GET['cod_agenda'])) {
            $this->cod_agenda = $_GET['cod_agenda'];
            $db = new clsBanco();
            $db->Consulta(consulta: "SELECT cod_agenda, ref_ref_cod_pessoa_exc, ref_ref_cod_pessoa_cad,  nm_agenda, publica, envia_alerta, data_cad, data_edicao, ref_ref_cod_pessoa_own FROM portal.agenda WHERE cod_agenda='{$this->cod_agenda}'");
            if ($db->ProximoRegistro()) {
                [$this->cod_agenda, $this->ref_ref_cod_pessoa_exc, $this->ref_ref_cod_pessoa_cad, $this->nm_agenda, $this->publica, $this->envia_alerta, $this->data_cad, $this->data_edicao, $this->ref_ref_cod_pessoa_own] = $db->Tupla();
                $this->fexcluir = true;
                $retorno = 'Editar';
                $this->editar = true;
            }
            if (isset($_GET['edit_rem']) && is_numeric(value: $_GET['edit_rem'])) {
                $db->Consulta(consulta: "DELETE FROM portal.agenda_responsavel WHERE ref_ref_cod_pessoa_fj = '{$_GET['edit_rem']}' AND ref_cod_agenda = '{$this->cod_agenda}'");
                $this->mensagem = 'Editor removido';
            }
            if (isset($_POST['novo_editor']) && is_numeric(value: $_POST['novo_editor'])) {
                $db->Consulta(consulta: "SELECT 1 FROM portal.agenda_responsavel WHERE ref_ref_cod_pessoa_fj = '{$_POST['novo_editor']}' AND ref_cod_agenda = '{$this->cod_agenda}'");
                if (!$db->ProximoRegistro()) {
                    $db->Consulta(consulta: "SELECT 1 FROM agenda WHERE ref_ref_cod_pessoa_own = '{$_POST['novo_editor']}' AND cod_agenda = '{$this->cod_agenda}'");
                    if (!$db->ProximoRegistro()) {
                        $db->Consulta(consulta: "INSERT INTO agenda_responsavel ( ref_ref_cod_pessoa_fj, ref_cod_agenda ) VALUES ( '{$_POST['novo_editor']}', '{$this->cod_agenda}' )");
                    } else {
                        $this->mensagem = 'O dono da agenda já é considerado um editor da mesma.';
                    }
                } else {
                    $this->mensagem = 'Este editor já está cadastrado';
                }
            }
        }

        if ($retorno == 'Editar') {
            $this->url_cancelar = "agenda_admin_det.php?cod_agenda={$this->cod_agenda}";
        } else {
            $this->url_cancelar = 'agenda_admin_lst.php';
        }
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: "$nomeMenu agenda");

        return $retorno;
    }

    public function Gerar()
    {
        $db = new clsBanco();
        $objPessoa = new clsPessoaFisica();

        $this->campoOculto(nome: 'pessoaFj', valor: $this->pessoaFj);
        $this->campoOculto(nome: 'cod_agenda', valor: $this->cod_agenda);

        $this->campoTexto(nome: 'nm_agenda', campo: 'Nome da Agenda', valor: $this->nm_agenda, tamanhovisivel: 50, tamanhomaximo: 50);

        $this->campoLista(nome: 'publica', campo: 'Pública', valor: ['Não', 'Sim'], default: $this->publica);

        $this->campoLista(nome: 'envia_alerta', campo: 'Envia Alerta', valor: ['Não', 'Sim'], default: $this->envia_alerta);

        $i = 0;
        if ($this->ref_ref_cod_pessoa_own) {
            [$nome] = $objPessoa->queryRapida($this->ref_ref_cod_pessoa_own, 'nome');
            $this->campoTextoInv(nome: "editor{$i}", campo: 'Editores', valor: $nome, tamanhovisivel: 50, tamanhomaximo: 255);
        }

        $lista = ['Pesquise a pessoa clicando no botao ao lado'];

        if ($this->cod_agenda) {
            $db->Consulta(consulta: "SELECT ref_ref_cod_pessoa_fj FROM portal.agenda_responsavel WHERE ref_cod_agenda = '{$this->cod_agenda}'");
            while ($db->ProximoRegistro()) {
                $i++;
                [$idpes] = $db->Tupla();
                [$nome] = $objPessoa->queryRapida($idpes, 'nome');
                $this->campoTextoInv(nome: "editor{$i}", campo: 'Editores', valor: $nome, tamanhovisivel: 50, tamanhomaximo: 255, obrigatorio: false, expressao: false, duplo: false, descricao: false, descricao2: "<a href=\"agenda_admin_cad.php?cod_agenda={$this->cod_agenda}&edit_rem=$idpes\">remover</a>");
            }
            //$this->campoListaPesq( "novo_editor", "Novo Editor", $lista, 0, "pesquisa_funcionario.php", false, false, false, "&nbsp; &nbsp; &nbsp; <a href=\"javascript:var idpes = document.getElementById('novo_editor').value; if( idpes != 0 ) { document.location.href='agenda_admin_cad.php?cod_agenda={$this->cod_agenda}&edit_add=' + idpes; } else { alert( 'Selecione a pessoa clicando na imagem da Lupa' ); }\">Adicionar</a>" );
            $parametros = new clsParametrosPesquisas();
            $parametros->setSubmit(submit: 1);
            $parametros->adicionaCampoSelect(campo_nome: 'novo_editor', campo_indice: 'ref_cod_pessoa_fj', campo_valor: 'nome');
            $this->campoListaPesq(nome: 'novo_editor', campo: 'Novo Editor', valor: $lista, default: 0, caminho: 'pesquisa_funcionario_lst.php', acao: '', duplo: false, descricao: '', descricao2: '', flag: null, pag_cadastro: null, disabled: '', div: false, serializedcampos: $parametros->serializaCampos());
            //$this->campoLista( "edit_add", "Editores", $lista, "", "", false, "", "<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'pesquisa_funcionario_lst.php?campos=$serializedcampos\'></iframe>' );\">", false, true );
            unset($campos);
        } else {
            //$this->campoListaPesq( "dono", "Dono da agenda", $lista, 0, "pesquisa_funcionario.php" );
            $parametros = new clsParametrosPesquisas();
            $parametros->setSubmit(submit: 0);
            $parametros->adicionaCampoSelect(campo_nome: 'dono', campo_indice: 'ref_cod_pessoa_fj', campo_valor: 'nome');
            $this->campoListaPesq(nome: 'dono', campo: 'Dono da agenda', valor: $lista, default: 0, caminho: 'pesquisa_funcionario_lst.php', acao: '', duplo: false, descricao: '', descricao2: '', flag: null, pag_cadastro: null, disabled: '', div: false, serializedcampos: $parametros->serializaCampos());
            //$this->campoLista( "dono", "Dono da agenda", $lista, "", "", false, "", "<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'pesquisa_funcionario_lst.php?campos=$serializedcampos\'></iframe>' );\">", false, true );
        }
    }

    public function Novo()
    {
        $campos = '';
        $values = '';
        $db = new clsBanco();

        if ($this->nm_agenda) {
            if (is_string(value: $this->nm_agencia)) {
                $campos .= ', nm_agencia';
                $values .= ", '{$this->nm_agencia}'";
            }

            if (is_numeric(value: $this->dono)) {
                if ($this->dono) {
                    $campos .= ', ref_ref_cod_pessoa_own';
                    $values .= ", '{$this->dono}'";
                } else {
                    $campos .= ', ref_ref_cod_pessoa_own';
                    $values .= ', NULL';
                }
            }

            if (is_numeric(value: $this->publica)) {
                $campos .= ', publica';
                $values .= ", '{$this->publica}'";
            }

            if (is_numeric(value: $this->envia_alerta)) {
                $campos .= ', envia_alerta';
                $values .= ", '{$this->envia_alerta}'";
            }

            $db->Consulta(consulta: "INSERT INTO portal.agenda( ref_ref_cod_pessoa_cad, data_cad, nm_agenda $campos) VALUES( '{$this->pessoa_logada}', NOW(), '{$this->nm_agenda}' $values)");
            $id_agenda = $db->insertId(str_sequencia: 'portal.agenda_cod_agenda_seq');

            $db->Consulta(consulta: "INSERT INTO portal.agenda_responsavel( ref_ref_cod_pessoa_fj, ref_cod_agenda ) VALUES( '{$this->pessoa_logada}', '{$id_agenda}' )");

            $this->simpleRedirect(url: 'agenda_admin_lst.php');
        } else {
            return false;
        }
    }

    public function Editar()
    {
        $set = '';
        $db = new clsBanco();

        if (is_numeric(value: $this->cod_agenda)) {
            if (is_string(value: $this->nm_agenda)) {
                $set .= ", nm_agenda = '{$this->nm_agenda}'";
            }

            if (is_numeric(value: $this->publica)) {
                $set .= ", publica = '{$this->publica}'";
            }

            if (is_numeric(value: $this->envia_alerta)) {
                $set .= ", envia_alerta = '{$this->envia_alerta}'";
            }

            $db->Consulta(consulta: "UPDATE portal.agenda SET ref_ref_cod_pessoa_exc = '{$this->pessoa_logada}', data_edicao = NOW() $set WHERE cod_agenda = '{$this->cod_agenda}'");
            $this->simpleRedirect(url: 'agenda_admin_lst.php');
        } else {
            $this->mensagem = 'Codigo de Diaria invalido!';

            return false;
        }

        return true;
    }

    public function Excluir()
    {
        if (is_numeric(value: $this->cod_agenda)) {
            $db = new clsBanco();

            $db->Consulta(consulta: "DELETE FROM portal.agenda_compromisso WHERE ref_cod_agenda={$this->cod_agenda}");
            $db->Consulta(consulta: "DELETE FROM portal.agenda_responsavel WHERE ref_cod_agenda={$this->cod_agenda}");

            $db->Consulta(consulta: "DELETE FROM portal.agenda WHERE cod_agenda={$this->cod_agenda}");
            $this->simpleRedirect(url: 'agenda_admin_lst.php');
        }
        $this->mensagem = 'Codigo da Agenda inválido!';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Agenda';
        $this->processoAp = '343';
    }
};
