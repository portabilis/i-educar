<?php

use App\Models\Country;
use iEducar\Legacy\InteractWithDatabase;

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Pais");
        $this->processoAp = '753';
    }
}

class indice extends clsCadastro
{
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
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
         $_SERVER['SERVER_NAME'].'/intranet' => 'Início',
         'educar_enderecamento_index.php'    => 'Endereçamento',
         ''        => "{$nomeMenu} país"
    ]);
        $this->enviaLocalizacao($localizacao->montar());

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
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
