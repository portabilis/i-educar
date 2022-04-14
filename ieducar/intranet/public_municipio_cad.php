<?php

use App\Models\City;
use iEducar\Legacy\InteractWithDatabase;
use iEducar\Legacy\SelectOptions;

return new class extends clsCadastro {
    use InteractWithDatabase, SelectOptions;

    public $idmun;
    public $nome;
    public $iduf;
    public $cod_ibge;
    public $idpais;

    public function model()
    {
        return City::class;
    }

    public function index()
    {
        return 'public_municipio_lst.php';
    }

    public function Inicializar()
    {
        $retorno = 'Novo';
        $this->idmun = $_GET['idmun'];

        if (is_numeric($this->idmun)) {
            $city = $this->find($this->idmun);

            $this->cod_ibge = $city->ibge_code;
            $this->nome = $city->name;
            $this->iduf = $city->state_id;
            $this->idpais = $city->state->country_id;

            $retorno = 'Editar';
        }
        $this->url_cancelar = $retorno == 'Editar'
            ? 'public_municipio_det.php?idmun=' . $this->idmun
            : 'public_municipio_lst.php';

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb("{$nomeMenu} município", [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('idmun', $this->idmun);

        $opcoes = ['' => 'Selecione'] + $this->getCountries();

        $this->campoLista('idpais', 'Pais', $opcoes, $this->idpais);

        $opcoes = ['' => 'Selecione'];

        if ($this->idpais) {
            $opcoes += $this->getStates($this->idpais);
        }

        $this->campoLista('iduf', 'Estado', $opcoes, $this->iduf);
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 60, true);
        $this->campoNumero('cod_ibge', 'Código INEP', $this->cod_ibge, 7, 7);
    }

    public function Novo()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido cadastro de municípios brasileiros, pois já estão previamente cadastrados.<br>';

            return false;
        }

        return $this->create([
            'name' => request('nome'),
            'state_id' => request('iduf'),
            'ibge_code' => request('cod_ibge'),
        ]);
    }

    public function Editar()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido edição de municípios brasileiros, pois já estão previamente cadastrados.<br>';

            return false;
        }

        return $this->update($this->idmun, [
            'name' => request('nome'),
            'state_id' => request('iduf'),
            'ibge_code' => request('cod_ibge'),
        ]);
    }

    public function Excluir()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido exclusão de municípios brasileiros, pois já estão previamente cadastrados.<br>';

            return false;
        }

        return $this->delete($this->idmun);
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/public-municipio-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Município';
        $this->processoAp = 755;
    }
};
