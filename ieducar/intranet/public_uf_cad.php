<?php

use App\Models\State;
use iEducar\Legacy\InteractWithDatabase;
use iEducar\Legacy\SelectOptions;
use Illuminate\Support\Str;

return new class extends clsCadastro {
    use InteractWithDatabase, SelectOptions;

    public $id;
    public $sigla_uf;
    public $nome;
    public $geom;
    public $idpais;
    public $cod_ibge;

    public function model()
    {
        return State::class;
    }

    public function index()
    {
        return 'public_uf_lst.php';
    }

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->id = $_GET['id'];
        $this->sigla_uf = $_GET['sigla_uf'];

        if (is_numeric($this->id)) {
            $state = $this->find($this->id);

            $this->sigla_uf = $state->abbreviation;
            $this->nome = $state->name;
            $this->idpais = $state->country_id;
            $this->cod_ibge = $state->ibge_code;

            $retorno = 'Editar';
        }

        $this->url_cancelar = $retorno == 'Editar' ? "public_uf_det.php?id={$this->id}" : 'public_uf_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';
        $this->breadcrumb("{$nomeMenu} UF", [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $opcoes = ['' => 'Selecione'] + $this->getCountries();

        $this->campoOculto('id', $this->id);
        $this->campoLista('idpais', 'Pais', $opcoes, $this->idpais);
        $this->campoTexto('sigla_uf', 'Sigla Uf', $this->sigla_uf, 3, 3, true);
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 30, true);
        $this->campoNumero('cod_ibge', 'Código INEP', $this->cod_ibge);

        $scripts = [
            '/modules/Portabilis/Assets/Javascripts/cad_uf.js'
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    public function Novo()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido cadastro de UFs brasileiras, pois já estão previamente cadastrados.<br>';

            return false;
        }

        $exists = $this->newQuery()
            ->where('abbreviation', Str::upper($this->sigla_uf))
            ->where('country_id', request('idpais'))
            ->exists();

        if ($exists) {
            $this->mensagem = 'A sigla já existe para outro estado.<br>';

            return false;
        }

        return $this->create([
            'name' => request('nome'),
            'country_id' => request('idpais'),
            'ibge_code' => request('cod_ibge'),
            'abbreviation' => request('sigla_uf'),
        ]);
    }

    public function Editar()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido edição de UFs brasileiras, pois já estão previamente cadastrados.<br>';

            return false;
        }

        $exists = $this->newQuery()
            ->where('abbreviation', Str::upper($this->sigla_uf))
            ->where('country_id', request('idpais'))
            ->where('id', '<>', $this->id)
            ->exists();

        if ($exists) {
            $this->mensagem = 'A sigla já existe para outro estado.<br>';

            return false;
        }

        return $this->update($this->id, [
            'name' => request('nome'),
            'country_id' => request('idpais'),
            'ibge_code' => request('cod_ibge'),
            'abbreviation' => request('sigla_uf'),
        ]);
    }

    public function Excluir()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido exclusão de UFs brasileiras, pois já estão previamente cadastrados.<br>';

            return false;
        }

        return $this->delete($this->id);
    }

    public function Formular()
    {
        $this->title = 'Uf';
        $this->processoAp = 754;
    }
};
