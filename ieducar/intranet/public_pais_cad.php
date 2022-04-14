<?php

use App\Models\Country;
use iEducar\Legacy\InteractWithDatabase;

return new class extends clsCadastro {
    use InteractWithDatabase;

    public $idpais;
    public $nome;
    public $geom;
    public $cod_ibge;

    public function model()
    {
        return Country::class;
    }

    public function index()
    {
        return 'public_pais_lst.php';
    }

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->idpais = $_GET['idpais'];

        if (is_numeric($this->idpais)) {
            $country = $this->find($this->idpais);

            $this->nome = $country->name;
            $this->cod_ibge = $country->ibge_code;
            $retorno = 'Editar';
        }

        $this->url_cancelar = $retorno == 'Editar' ? "public_pais_det.php?idpais={$this->idpais}" : 'public_pais_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb("{$nomeMenu} país", [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('idpais', $this->idpais);

        // text
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 60, true);

        $this->inputsHelper()->integer(
            'cod_ibge',
            [
                'label' => 'Código INEP',
                'required' => false,
                'label_hint' => 'Somente números',
                'max_length' => 12,
                'placeholder' => 'INEP'
            ]
        );

        $this->campoNumero('cod_ibge', 'Código INEP', $this->cod_ibge, 30, 8, true);
    }

    public function Novo()
    {
        return $this->create([
            'name' => request('nome'),
            'ibge_code' => request('cod_ibge'),
        ]);
    }

    public function Editar()
    {
        return $this->update($this->idpais, [
            'name' => request('nome'),
            'ibge_code' => request('cod_ibge'),
        ]);
    }

    public function Excluir()
    {
        return $this->delete($this->idpais);
    }

    public function Formular()
    {
        $this->title = 'Pais';
        $this->processoAp = '753';
    }
};
