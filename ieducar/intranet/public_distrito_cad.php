<?php

use App\Models\District;
use iEducar\Legacy\InteractWithDatabase;
use iEducar\Legacy\SelectOptions;

return new class extends clsCadastro {
    use InteractWithDatabase, SelectOptions;

    public $idmun;
    public $geom;
    public $iddis;
    public $nome;
    public $cod_ibge;
    public $idpes_rev;
    public $data_rev;
    public $origem_gravacao;
    public $idpes_cad;
    public $data_cad;
    public $operacao;
    public $idpais;
    public $iduf;

    public function model()
    {
        return District::class;
    }

    public function index()
    {
        return 'public_distrito_lst.php';
    }

    public function Inicializar()
    {
        $retorno = 'Novo';
        $this->iddis = $_GET['iddis'];

        if (is_numeric($this->iddis)) {
            $district = $this->find($this->iddis);

            $this->nome = $district->name;
            $this->idmun = $district->city_id;
            $this->iduf = $district->city->state_id;
            $this->idpais = $district->city->state->country_id;
            $this->cod_ibge = $district->ibge_code;

            $retorno = 'Editar';
        }

        $this->url_cancelar = $retorno == 'Editar'
            ? 'public_distrito_det.php?iddis=' . $this->iddis
            : 'public_distrito_lst.php';

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb("{$nomeMenu} distrito", [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('iddis', $this->iddis);

        $opcoes = ['' => 'Selecione'] + $this->getCountries();

        $this->campoLista('idpais', 'Pais', $opcoes, $this->idpais);

        $opcoes = ['' => 'Selecione'];

        if ($this->idpais) {
            $opcoes += $this->getStates($this->idpais);
        }

        $this->campoLista('iduf', 'Estado', $opcoes, $this->iduf);

        $opcoes = ['' => 'Selecione'];

        if ($this->iduf) {
            $opcoes += $this->getCities($this->iduf);
        }

        $this->campoLista('idmun', 'Município', $opcoes, $this->idmun);
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 255, true);
        $this->campoTexto('cod_ibge', 'Código INEP', $this->cod_ibge, 7, 7, null, null, null, 'Somente números');
    }

    public function Novo()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido cadastro de distritos brasileiros, pois já estão previamente cadastrados.<br>';

            return false;
        }

        return $this->create([
            'name' => request('nome'),
            'ibge_code' => request('cod_ibge'),
            'city_id' => request('idmun'),
        ]);
    }

    public function Editar()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido edição de distritos brasileiros, pois já estão previamente cadastrados.<br>';

            return false;
        }

        return $this->update($this->iddis, [
            'name' => request('nome'),
            'ibge_code' => request('cod_ibge'),
            'city_id' => request('idmun'),
        ]);
    }

    public function Excluir()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido exclusão de distritos brasileiros, pois já estão previamente cadastrados.<br>';

            return false;
        }

        return $this->delete($this->iddis);
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/public-distrito-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Distrito';
        $this->processoAp = 759;
    }
};
