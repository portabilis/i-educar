<?php

use iEducar\Modules\Addressing\LegacyAddressingFields;

$desvio_diretorio = '';
require_once('include/clsBase.inc.php');
require_once('include/clsCadastro.inc.php');
require_once('include/clsBanco.inc.php');

class clsIndex extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Empresas!");
        $this->processoAp = 41;
    }
}

class indice extends clsCadastro
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
        $this->busca_empresa = $_POST['busca_empresa'];
        $this->cod_pessoa_fj = $_GET['idpes'];
        $this->idpes_cad = $this->pessoa_logada;

        if ($this->busca_empresa) {
            $this->cnpj = $this->busca_empresa;
            $this->busca_empresa = idFederal2int($this->busca_empresa);
            $this->retorno = 'Novo';
            $objPessoa = new clsPessoaJuridica();
            list($this->cod_pessoa_fj) = $objPessoa->queryRapidaCNPJ($this->busca_empresa, 'idpes');
        }

        if ($this->cod_pessoa_fj) {
            $this->busca_empresa = true;
            $objPessoaJuridica = new clsPessoaJuridica($this->cod_pessoa_fj);
            $detalhePessoaJuridica = $objPessoaJuridica->detalhe();
            //echo "<pre>";
            //print_r($detalhePessoaJuridica);
            //die();
            $this->email = $detalhePessoaJuridica['email'];
            $this->url = $detalhePessoaJuridica['url'];
            $this->insc_est = $detalhePessoaJuridica['insc_estadual'];
            $this->capital_social = $detalhePessoaJuridica['capital_social'];
            $this->razao_social = $detalhePessoaJuridica['nome'];
            $this->fantasia = $detalhePessoaJuridica['fantasia'];
            $this->cnpj = int2CNPJ($detalhePessoaJuridica['cnpj']);
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

        $nomeMenu = $this->retorno == 'Editar' ? $this->retorno : 'Cadastrar';
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'] . '/intranet' => 'In&iacute;cio',
            'educar_pessoas_index.php' => 'Pessoas',
            '' => "$nomeMenu pessoa jur&iacute;dica"
        ]);
        $this->enviaLocalizacao($localizacao->montar());

        return $this->retorno;
    }

    public function Gerar()
    {
        if (!$this->busca_empresa) {
            $this->campoCnpj('busca_empresa', 'CNPJ', $this->busca_empresa, true);
        } else {
            $this->url_cancelar = ($this->retorno == 'Editar') ? "empresas_det.php?cod_empresa={$this->cod_pessoa_fj}" : 'empresas_lst.php';

            $this->campoOculto('cod_pessoa_fj', $this->cod_pessoa_fj);
            $this->campoOculto('idpes_cad', $this->idpes_cad);

            // Dados da Empresa
            $this->campoTexto('fantasia', 'Nome Fantasia', $this->fantasia, '50', '255', true);
            $this->campoTexto('razao_social', 'Raz&atilde;o Social', $this->razao_social, '50', '255', true);
            $this->campoTexto('capital_social', 'Capital Social', $this->capital_social, '50', '255');

            $nivelUsuario = (new clsPermissoes)->nivel_acesso($this->getSession()->id_pessoa);
            if (!$this->cod_pessoa_fj || $nivelUsuario > App_Model_NivelTipoUsuario::INSTITUCIONAL) {
                $this->campoRotulo('cnpj_', 'CNPJ', $this->cnpj);
                $this->campoOculto('cnpj', $this->cnpj);
            } else {
                $this->campoCnpj('cnpj', 'CNPJ', $this->cnpj, true);
            }

            $this->viewAddress();

            $this->inputTelefone('1', 'Telefone 1');
            $this->inputTelefone('2', 'Telefone 2');
            $this->inputTelefone('mov', 'Celular');
            $this->inputTelefone('fax', 'Fax');

            // Dados da Empresa

            $this->campoTexto('url', 'Site', $this->url, '50', '255', false);
            $this->campoTexto('email', 'E-mail', $this->email, '50', '255', false);
            $this->campoTexto('insc_est', 'Inscri&ccedil;&atilde;o Estadual', $this->insc_est, '20', '30', false);
        }

        Portabilis_View_Helper_Application::loadJavascript($this, [
            '/modules/Cadastro/Assets/Javascripts/Addresses.js',
        ]);
    }

    public function Novo()
    {
        $this->cnpj = idFederal2int(urldecode($this->cnpj));
        $objJuridica = new clsJuridica(false, $this->cnpj);
        $detalhJuridica = $objJuridica->detalhe();
        if (!$detalhJuridica) {
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

            $objJuridica = new clsJuridica(
                $this->cod_pessoa_fj,
                $this->cnpj,
                $this->fantasia,
                $this->insc_est,
                $this->capital_social
            );
            $objJuridica->cadastra();

            if ($this->telefone_1) {
                $this->telefone_1 = str_replace('-', '', $this->telefone_1);
                $this->telefone_1 = trim($this->telefone_1);
                if (is_numeric($this->telefone_1) && (strlen($this->telefone_1) < 12)) {
                    $objTelefone = new clsPessoaTelefone(
                        $this->cod_pessoa_fj,
                        1,
                        $this->telefone_1,
                        $this->ddd_telefone_1
                    );
                    $objTelefone->cadastra();
                }
            }
            if ($this->telefone_2) {
                $this->telefone_2 = str_replace('-', '', $this->telefone_2);
                $this->telefone_2 = trim($this->telefone_2);
                if (is_numeric($this->telefone_2) && (strlen($this->telefone_2) < 12)) {
                    $objTelefone = new clsPessoaTelefone(
                        $this->cod_pessoa_fj,
                        2,
                        $this->telefone_2,
                        $this->ddd_telefone_2
                    );
                    $objTelefone->cadastra();
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
                    $objTelefone->cadastra();
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
                    $objTelefone->cadastra();
                }
            }

            $this->saveAddress($this->cod_pessoa_fj);

            $this->simpleRedirect('empresas_lst.php');
        }

        $this->mensagem = 'Ja existe uma empresa cadastrada com este CNPJ. ';

        return false;
    }

    public function Editar()
    {
        $this->cnpj = idFederal2int(urldecode($this->cnpj));
        $objJuridica = new clsJuridica(false, $this->cnpj);

        if ($objJuridica->detalhe()) {
            $this->mensagem = 'Ja existe uma empresa cadastrada com este CNPJ. ';

            return false;
        }

        if (!$this->validaDadosTelefones()) {
            // variável buscar_empresa é usada para definir os campos que aparecem na tela, quando false apresenta apenas o campo de CNPJ
            // por tanto é preciso setar para true para que a mensagem de erro seja apresentada com os demais campos normalmente.
            $this->busca_empresa = true;

            return false;
        }

        $this->cnpj = idFederal2int($this->cnpj);
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

    protected function validaDDDTelefone($valorDDD = null, $valorTelefone = null, $nomeCampo)
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
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm($miolo);

$pagina->MakeAll();
