<?php

use iEducar\Modules\Addressing\LegacyAddressingFields;
use Illuminate\Support\Facades\Auth;

return new class extends clsCadastro
{
    use LegacyAddressingFields;

    // Dados do Juridico
    public $cod_pessoa_fj;
    public $razao_social;
    public $cnpj;
    public $fantasia;
    public $capital_social;
    public $insc_est;

    // Dados da Pessoa
    public $email;
    public $tipo_pessoa;
    public $idpes_cad;
    public $url;

    //Telefones
    public $ddd_telefone_1;
    public $telefone_1;
    public $ddd_telefone_2;
    public $telefone_2;
    public $ddd_telefone_mov;
    public $telefone_mov;
    public $ddd_telefone_fax;
    public $telefone_fax;

    // Variaveis de Controle
    public $busca_empresa;
    public $retorno;

    public function Inicializar()
    {
        $this->cod_pessoa_fj = is_numeric($_GET['idpes']) ? (int)$_GET['idpes'] : null;
        $this->idpes_cad = $this->pessoa_logada;

        $this->retorno = 'Novo';

        if ($this->cod_pessoa_fj) {
            $this->busca_empresa = true;
            $objPessoaJuridica = new clsPessoaJuridica($this->cod_pessoa_fj);
            $detalhePessoaJuridica = $objPessoaJuridica->detalhe();
            $this->email = $detalhePessoaJuridica['email'];
            $this->url = $detalhePessoaJuridica['url'];
            $this->insc_est = $detalhePessoaJuridica['insc_estadual'];
            $this->capital_social = $detalhePessoaJuridica['capital_social'];
            $this->razao_social = $detalhePessoaJuridica['nome'];
            $this->fantasia = $detalhePessoaJuridica['fantasia'];
            $this->cnpj = validaCNPJ($detalhePessoaJuridica['cnpj']) ? int2CNPJ($detalhePessoaJuridica['cnpj']) : null;
            $this->ddd_telefone_1 = $detalhePessoaJuridica['ddd_1'];
            $this->telefone_1 = $detalhePessoaJuridica['fone_1'];
            $this->ddd_telefone_2 = $detalhePessoaJuridica['ddd_2'];
            $this->telefone_2 = $detalhePessoaJuridica['fone_2'];
            $this->ddd_telefone_mov = $detalhePessoaJuridica['ddd_mov'];
            $this->telefone_mov = $detalhePessoaJuridica['fone_mov'];
            $this->ddd_telefone_fax = $detalhePessoaJuridica['ddd_fax'];
            $this->telefone_fax = $detalhePessoaJuridica['fone_fax'];

            $this->loadAddress($this->cod_pessoa_fj);

            $this->retorno = 'Editar';
        }

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $this->retorno === 'Editar' ? $this->retorno : 'Cadastrar';

        $this->breadcrumb("{$nomeMenu} pessoa jurídica", [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);

        return $this->retorno;
    }

    public function Gerar()
    {
        $this->url_cancelar = ($this->retorno === 'Editar') ? "empresas_det.php?cod_empresa={$this->cod_pessoa_fj}" : 'empresas_lst.php';

        $this->campoOculto('cod_pessoa_fj', $this->cod_pessoa_fj);
        $this->campoOculto('idpes_cad', $this->idpes_cad);

        // Dados da Empresa
        $this->campoTexto('fantasia', 'Nome Fantasia', $this->fantasia, '50', '255', true);
        $this->campoTexto('razao_social', 'Razão Social', $this->razao_social, '50', '255', true);
        $this->campoTexto('capital_social', 'Capital Social', $this->capital_social, '50', '255');

        if ((new clsPermissoes)->nivel_acesso(Auth::id()) > App_Model_NivelTipoUsuario::INSTITUCIONAL) {
            $this->campoRotulo('cnpj_', 'CNPJ', $this->cnpj);
            $this->campoOculto('cnpj', $this->cnpj);
        } else {
            $this->campoCnpj('cnpj', 'CNPJ', $this->cnpj);
        }

        $this->viewAddress();

        $this->inputTelefone('1', 'Telefone 1');
        $this->inputTelefone('2', 'Telefone 2');
        $this->inputTelefone('mov', 'Celular');
        $this->inputTelefone('fax', 'Fax');

        // Dados da Empresa
        $this->campoTexto('url', 'Site', $this->url, '50', '255');
        $this->campoTexto('email', 'E-mail', $this->email, '50', '255');
        $this->campoTexto('insc_est', 'Inscrição Estadual', $this->insc_est, '20', '30');

        Portabilis_View_Helper_Application::loadJavascript($this, [
            '/modules/Cadastro/Assets/Javascripts/Addresses.js',
        ]);
    }

    public function Novo()
    {
        if (!empty($this->cnpj) && validaCNPJ($this->cnpj) === false) {
            $this->mensagem = 'CNPJ inválido';
            return false;
        }

        $this->cnpj = validaCNPJ($this->cnpj) ? idFederal2int(urldecode($this->cnpj)) : null;

        $contemPessoaJuridica = (new clsJuridica(false, $this->cnpj))->detalhe();
        if ($this->cnpj !== null && $contemPessoaJuridica) {
            $this->mensagem = 'Já existe uma empresa cadastrada com este CNPJ.';
            return false;
        }

        $this->insc_est = idFederal2int($this->insc_est);
        $this->idpes_cad = $this->pessoa_logada;

        $objPessoa = new clsPessoa_(
            false,
            $this->razao_social,
            $this->idpes_cad,
            $this->url,
            'J',
            false,
            false,
            $this->email
        );

        $this->cod_pessoa_fj = $objPessoa->cadastra();

        (new clsJuridica(
            $this->cod_pessoa_fj,
            $this->cnpj,
            $this->fantasia,
            $this->insc_est,
            $this->capital_social
        ))->cadastra();


        if ($this->telefone_1) {
            $this->cadastraTelefone($this->cod_pessoa_fj, 1, $this->telefone_1, $this->ddd_telefone_1);
        }

        if ($this->telefone_2) {
            $this->cadastraTelefone($this->cod_pessoa_fj, 2, $this->telefone_2, $this->ddd_telefone_2);
        }

        if ($this->telefone_mov) {
            $this->cadastraTelefone($this->cod_pessoa_fj, 3, $this->telefone_mov, $this->ddd_telefone_mov);
        }

        if ($this->telefone_fax) {
            $this->cadastraTelefone($this->cod_pessoa_fj, 4, $this->telefone_fax, $this->ddd_telefone_fax);
        }

        $this->saveAddress($this->cod_pessoa_fj);

        $this->mensagem = 'Cadastro salvo com sucesso.';

        $this->simpleRedirect('empresas_lst.php');

        return true;
    }

    private function cadastraTelefone($codPessoaJuridica, $tipo, $telefone, $dddTelefone)
    {
        $telefone = $this->limpaDadosTelefone($telefone);

        if ($this->validaDadosTelefone($telefone)) {
            (new clsPessoaTelefone(
                $codPessoaJuridica,
                $tipo,
                $telefone,
                $dddTelefone
            ))->cadastra();
        }
    }

    private function limpaDadosTelefone($telefone)
    {
        return trim(str_replace('-', '', $telefone));
    }

    private function validaDadosTelefone($telefone)
    {
        return is_numeric($telefone) && (strlen($telefone) < 12);
    }

    public function Editar()
    {
        if (!empty($this->cnpj) && validaCNPJ($this->cnpj) === false) {
            $this->mensagem = 'CNPJ inválido';
            return false;
        }

        $this->cnpj = validaCNPJ($this->cnpj) ? idFederal2int(urldecode($this->cnpj)) : null;

        $objJuridica = new clsJuridica(false, $this->cnpj);

        $detalhe = $objJuridica->detalhe();

        if ($detalhe && $this->cod_pessoa_fj != $detalhe['idpes']) {
            $this->mensagem = 'Já existe uma empresa cadastrada com este CNPJ.';

            return false;
        }

        if (!$this->validaDadosTelefones()) {
            // variável buscar_empresa é usada para definir os campos que aparecem na tela, quando false apresenta apenas o campo de CNPJ
            // por tanto é preciso setar para true para que a mensagem de erro seja apresentada com os demais campos normalmente.
            $this->busca_empresa = true;

            return false;
        }

        $this->insc_est = idFederal2int($this->insc_est);

        $objPessoa = new clsPessoa_(
            $this->cod_pessoa_fj,
            $this->razao_social,
            $this->idpes_cad,
            $this->url,
            'J',
            false,
            false,
            $this->email
        );
        $objPessoa->edita();

        $objJuridica = new clsJuridica(
            $this->cod_pessoa_fj,
            $this->cnpj,
            $this->fantasia,
            $this->insc_est,
            $this->capital_social
        );
        $objJuridica->edita();

        if ($this->telefone_1) {
            $this->telefone_1 = str_replace('-', '', $this->telefone_1);
            $this->telefone_1 = trim($this->telefone_1);
            if (is_numeric($this->telefone_1) && (strlen($this->telefone_1) < 12)) {
                $objTelefone = new clsPessoaTelefone($this->cod_pessoa_fj, 1, $this->telefone_1, $this->ddd_telefone_1);
                if ($objTelefone->detalhe()) {
                    $objTelefone->edita();
                } else {
                    $objTelefone->cadastra();
                }
            }
        }
        if ($this->telefone_2) {
            $this->telefone_2 = str_replace('-', '', $this->telefone_2);
            $this->telefone_2 = trim($this->telefone_2);
            if (is_numeric($this->telefone_2) && (strlen($this->telefone_2) < 12)) {
                $objTelefone = new clsPessoaTelefone($this->cod_pessoa_fj, 2, $this->telefone_2, $this->ddd_telefone_2);
                if ($objTelefone->detalhe()) {
                    $objTelefone->edita();
                } else {
                    $objTelefone->cadastra();
                }
            }
        }
        if ($this->telefone_mov) {
            $this->telefone_mov = str_replace('-', '', $this->telefone_mov);
            $this->telefone_mov = trim($this->telefone_mov);
            if (is_numeric($this->telefone_mov) && (strlen($this->telefone_mov) < 12)) {
                $objTelefone = new clsPessoaTelefone(
                    $this->cod_pessoa_fj,
                    3,
                    $this->telefone_mov,
                    $this->ddd_telefone_mov
                );
                if ($objTelefone->detalhe()) {
                    $objTelefone->edita();
                } else {
                    $objTelefone->cadastra();
                }
            }
        }
        if ($this->telefone_fax) {
            $this->telefone_fax = str_replace('-', '', $this->telefone_fax);
            $this->telefone_fax = trim($this->telefone_fax);
            if (is_numeric($this->telefone_fax) && (strlen($this->telefone_fax) < 12)) {
                $objTelefone = new clsPessoaTelefone(
                    $this->cod_pessoa_fj,
                    4,
                    $this->telefone_fax,
                    $this->ddd_telefone_fax
                );
                if ($objTelefone->detalhe()) {
                    $objTelefone->edita();
                } else {
                    $objTelefone->cadastra();
                }
            }
        }

        $this->saveAddress($this->cod_pessoa_fj);
        $this->mensagem = 'Edição efetuada com sucesso.';
        $this->simpleRedirect('empresas_lst.php');
    }

    public function Excluir()
    {
        $this->simpleRedirect('empresas_lst.php');
    }

    protected function inputTelefone($type, $typeLabel = '')
    {
        if (!$typeLabel) {
            $typeLabel = "Telefone {$type}";
        }

        // ddd

        $options = [
            'required' => false,
            'label' => "(DDD) / {$typeLabel}",
            'placeholder' => 'DDD',
            'value' => $this->{"ddd_telefone_{$type}"},
            'max_length' => 3,
            'size' => 3,
            'inline' => true
        ];

        $this->inputsHelper()->integer("ddd_telefone_{$type}", $options);

        // telefone

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => $typeLabel,
            'value' => $this->{"telefone_{$type}"},
            'max_length' => 11
        ];

        $this->inputsHelper()->integer("telefone_{$type}", $options);
    }

    protected function validaDadosTelefones()
    {
        return $this->validaDDDTelefone($this->ddd_telefone_1, $this->telefone_1, 'Telefone 1') &&
            $this->validaDDDTelefone($this->ddd_telefone_2, $this->telefone_2, 'Telefone 2') &&
            $this->validaDDDTelefone($this->ddd_telefone_mov, $this->telefone_mov, 'Celular') &&
            $this->validaDDDTelefone($this->ddd_telefone_fax, $this->telefone_fax, 'Fax');
    }

    protected function validaDDDTelefone($valorDDD, $valorTelefone, $nomeCampo)
    {
        $msgRequereTelefone = "O campo: {$nomeCampo}, deve ser preenchido quando o DDD estiver preenchido.";
        $msgRequereDDD = "O campo: DDD, deve ser preenchido quando o {$nomeCampo} estiver preenchido.";

        if (!empty($valorDDD) && empty($valorTelefone)) {
            $this->mensagem = $msgRequereTelefone;

            return false;
        }

        if (empty($valorDDD) && !empty($valorTelefone)) {
            $this->mensagem = $msgRequereDDD;

            return false;
        }

        return true;
    }

    public function Formular()
    {
        $this->_titulo = 'Pessoa Jurídica - Cadastro';
        $this->processoAp = 41;
    }
};
