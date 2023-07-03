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

    public $agenda_display;

    public $pessoa_logada;

    public function Inicializar()
    {
        $retorno = 'Editar';
        $this->url_cancelar = 'agenda.php';
        $this->nome_url_cancelar = 'Voltar';

        $this->breadcrumb(currentPage: 'Editar preferências da agenda');

        return $retorno;
    }

    public function Gerar()
    {
        $db = new clsBanco();
        $db2 = new clsBanco();

        $objAgenda = new clsAgenda(int_cod_editor: $this->pessoa_logada, int_cod_pessoa_dono: $this->pessoa_logada);
        $this->cod_agenda = $objAgenda->getCodAgenda();
        $this->envia_alerta = $objAgenda->getEnviaAlerta();
        $this->nm_agenda = $objAgenda->getNome();

        $this->campoOculto(nome: 'cod_agenda', valor: $this->cod_agenda);
        $this->campoLista(nome: 'envia_alerta', campo: 'Envia Alerta', valor: ['Não', 'Sim'], default: $this->envia_alerta);

        $db->Consulta(consulta: "SELECT ref_cod_agenda FROM agenda_responsavel WHERE ref_ref_cod_pessoa_fj = '{$this->pessoa_logada}' AND principal = 1");
        if ($db->ProximoRegistro()) {
            [$this->agenda_display] = $db->Tupla();
        } else {
            $this->agenda_display = $this->cod_agenda;
        }

        $agendas = [];
        $agendas[$this->cod_agenda] = "Minha agenda: {$this->nm_agenda}";
        $db->Consulta(consulta: "SELECT ref_cod_agenda, principal FROM agenda_responsavel WHERE ref_ref_cod_pessoa_fj = '{$this->pessoa_logada}'");
        while ($db->ProximoRegistro()) {
            [$cod_agenda, $principal] = $db->Tupla();
            $agendas[$cod_agenda] = $db2->CampoUnico(consulta: "SELECT nm_agenda FROM agenda WHERE cod_agenda = '{$cod_agenda}'");
            if ($principal) {
                $this->agenda_display = $cod_agenda;
            }
        }
        $this->campoLista(nome: 'agenda_display', campo: 'Agenda exibida na pagina principal', valor: $agendas, default: $this->agenda_display);
    }

    public function Novo()
    {
        return false;
    }

    public function Editar()
    {
        $objAgenda = new clsAgenda(int_cod_editor: $this->pessoa_logada, int_cod_pessoa_dono: $this->pessoa_logada);
        $this->cod_agenda = $objAgenda->getCodAgenda();

        $set = '';
        $db = new clsBanco();

        if (is_numeric(value: $this->envia_alerta)) {
            $set .= ", envia_alerta = '{$this->envia_alerta}'";
        }

        if (is_numeric(value: $this->agenda_display)) {
            $db->Consulta(consulta: "UPDATE agenda_responsavel SET principal = 0 WHERE ref_ref_cod_pessoa_fj = '{$this->pessoa_logada}'");
            $db->Consulta(consulta: "UPDATE agenda_responsavel SET principal = 1 WHERE ref_ref_cod_pessoa_fj = '{$this->pessoa_logada}' AND ref_cod_agenda = '{$this->agenda_display}'");
        }

        $db->Consulta(consulta: "UPDATE portal.agenda SET ref_ref_cod_pessoa_exc = '{$this->pessoa_logada}', data_edicao = NOW() $set WHERE cod_agenda = '{$this->cod_agenda}'");
        $this->simpleRedirect(url: 'agenda.php');
    }

    public function Formular()
    {
        $this->title = 'Agenda - Preferencias';
        $this->processoAp = '345';
    }
};
