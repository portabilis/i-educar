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
        $this->cod_pessoa_fj = is_numeric(value: $_GET['idpes']) ? (int) $_GET['idpes'] : null;
        $this->idpes_cad = $this->pessoa_logada;

        $this->retorno = 'Novo';

        if ($this->cod_pessoa_fj) {
            $this->busca_empresa = true;
            $objPessoaJuridica = new clsPessoaJuridica(int_idpes: $this->cod_pessoa_fj);
            $detalhePessoaJuridica = $objPessoaJuridica->detalhe();
            $this->email = $detalhePessoaJuridica['email'];
            $this->url = $detalhePessoaJuridica['url'];
            $this->insc_est = $detalhePessoaJuridica['insc_estadual'];
            $this->capital_social = $detalhePessoaJuridica['capital_social'];
            $this->razao_social = $detalhePessoaJuridica['nome'];
            $this->fantasia = $detalhePessoaJuridica['fantasia'];
            $this->cnpj = validaCNPJ(cnpj: $detalhePessoaJuridica['cnpj']) ? int2CNPJ(int: $detalhePessoaJuridica['cnpj']) : null;
            $this->ddd_telefone_1 = $detalhePessoaJuridica['ddd_1'];
            $this->telefone_1 = $detalhePessoaJuridica['fone_1'];
            $this->ddd_telefone_2 = $detalhePessoaJuridica['ddd_2'];
            $this->telefone_2 = $detalhePessoaJuridica['fone_2'];
            $this->ddd_telefone_mov = $detalhePessoaJuridica['ddd_mov'];
            $this->telefone_mov = $detalhePessoaJuridica['fone_mov'];
            $this->ddd_telefone_fax = $detalhePessoaJuridica['ddd_fax'];
            $this->telefone_fax = $detalhePessoaJuridica['fone_fax'];

            $this->loadAddress(person: $this->cod_pessoa_fj);

            $this->retorno = 'Editar';
        }

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $this->retorno === 'Editar' ? $this->retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: "{$nomeMenu} pessoa jurídica", breadcrumbs: [
            url(path: 'intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);

        return $this->retorno;
    }

    public function Gerar()
    {
        $this->url_cancelar = ($this->retorno === 'Editar') ? "empresas_det.php?cod_empresa={$this->cod_pessoa_fj}" : 'empresas_lst.php';

        $this->campoOculto(nome: 'cod_pessoa_fj', valor: $this->cod_pessoa_fj);
        $this->campoOculto(nome: 'idpes_cad', valor: $this->idpes_cad);

        // Dados da Empresa
        $this->campoTexto(nome: 'fantasia', campo: 'Nome Fantasia', valor: $this->fantasia, tamanhovisivel: '50', tamanhomaximo: '255', obrigatorio: true);
        $this->campoTexto(nome: 'razao_social', campo: 'Razão Social', valor: $this->razao_social, tamanhovisivel: '50', tamanhomaximo: '255', obrigatorio: true);
        $this->campoTexto(nome: 'capital_social', campo: 'Capital Social', valor: $this->capital_social, tamanhovisivel: '50', tamanhomaximo: '255');

        if ((new clsPermissoes)->nivel_acesso(int_idpes_usuario: Auth::id()) > App_Model_NivelTipoUsuario::INSTITUCIONAL) {
            $this->campoRotulo(nome: 'cnpj_', campo: 'CNPJ', valor: $this->cnpj);
            $this->campoOculto(nome: 'cnpj', valor: $this->cnpj);
        } else {
            $this->campoCnpj(nome: 'cnpj', campo: 'CNPJ', valor: $this->cnpj);
        }

        $this->viewAddress();

        $this->inputTelefone(type: '1', typeLabel: 'Telefone 1');
        $this->inputTelefone(type: '2', typeLabel: 'Telefone 2');
        $this->inputTelefone(type: 'mov', typeLabel: 'Celular');
        $this->inputTelefone(type: 'fax', typeLabel: 'Fax');

        // Dados da Empresa
        $this->campoTexto(nome: 'url', campo: 'Site', valor: $this->url, tamanhovisivel: '50', tamanhomaximo: '255');
        $this->campoTexto(nome: 'email', campo: 'E-mail', valor: $this->email, tamanhovisivel: '50', tamanhomaximo: '255');
        $this->campoTexto(nome: 'insc_est', campo: 'Inscrição Estadual', valor: $this->insc_est, tamanhovisivel: '20', tamanhomaximo: '30');

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: [
            '/vendor/legacy/Cadastro/Assets/Javascripts/Addresses.js',
        ]);
    }

    public function Novo()
    {
        if (!empty($this->cnpj) && validaCNPJ(cnpj: $this->cnpj) === false) {
            $this->mensagem = 'CNPJ inválido';

            return false;
        }

        $this->cnpj = validaCNPJ(cnpj: $this->cnpj) ? idFederal2int(str: urldecode(string: $this->cnpj)) : null;

        $contemPessoaJuridica = (new clsJuridica(idpes: false, cnpj: $this->cnpj))->detalhe();
        if ($this->cnpj !== null && $contemPessoaJuridica) {
            $this->mensagem = 'Já existe uma empresa cadastrada com este CNPJ.';

            return false;
        }

        if (!$this->validaCaracteresPermitidosComplemento()) {
            $this->mensagem = 'O campo foi preenchido com valor não permitido. O campo Complemento só permite os caracteres: ABCDEFGHIJKLMNOPQRSTUVWXYZ 0123456789 ª º – / . ,';

            return false;
        }

        if (!$this->validaDadosTelefones()) {
            $this->busca_empresa = true;

            return false;
        }

        $this->insc_est = idFederal2int(str: $this->insc_est);
        $this->idpes_cad = $this->pessoa_logada;

        $objPessoa = new clsPessoa_(
            int_idpes: false,
            str_nome: $this->razao_social,
            int_idpes_cad: $this->idpes_cad,
            str_url: $this->url,
            int_tipo: 'J',
            int_idpes_rev: false,
            str_data_rev: false,
            str_email: $this->email
        );

        $this->cod_pessoa_fj = $objPessoa->cadastra();

        (new clsJuridica(
            idpes: $this->cod_pessoa_fj,
            cnpj: $this->cnpj,
            fantasia: $this->fantasia,
            insc_estadual: $this->insc_est,
            capital_social: $this->capital_social
        ))->cadastra();

        if ($this->telefone_1) {
            $this->cadastraTelefone(codPessoaJuridica: $this->cod_pessoa_fj, tipo: 1, telefone: $this->telefone_1, dddTelefone: $this->ddd_telefone_1);
        }

        if ($this->telefone_2) {
            $this->cadastraTelefone(codPessoaJuridica: $this->cod_pessoa_fj, tipo: 2, telefone: $this->telefone_2, dddTelefone: $this->ddd_telefone_2);
        }

        if ($this->telefone_mov) {
            $this->cadastraTelefone(codPessoaJuridica: $this->cod_pessoa_fj, tipo: 3, telefone: $this->telefone_mov, dddTelefone: $this->ddd_telefone_mov);
        }

        if ($this->telefone_fax) {
            $this->cadastraTelefone(codPessoaJuridica: $this->cod_pessoa_fj, tipo: 4, telefone: $this->telefone_fax, dddTelefone: $this->ddd_telefone_fax);
        }

        $this->saveAddress(person: $this->cod_pessoa_fj);

        $this->mensagem = 'Cadastro salvo com sucesso.';

        $this->simpleRedirect(url: 'empresas_lst.php');

        return true;
    }

    private function cadastraTelefone($codPessoaJuridica, $tipo, $telefone, $dddTelefone)
    {
        $telefone = $this->limpaDadosTelefone(telefone: $telefone);

        if ($this->validaDadosTelefone(telefone: $telefone)) {
            (new clsPessoaTelefone(
                int_idpes: $codPessoaJuridica,
                int_tipo: $tipo,
                str_fone: $telefone,
                str_ddd: $dddTelefone
            ))->cadastra();
        }
    }

    private function limpaDadosTelefone($telefone)
    {
        return trim(string: str_replace(search: '-', replace: '', subject: $telefone));
    }

    private function validaDadosTelefone($telefone)
    {
        return is_numeric(value: $telefone) && (strlen(string: $telefone) < 12);
    }

    protected function validaCaracteresPermitidosComplemento()
    {
        if (empty($this->complement)) {
            return true;
        }
        $pattern = '/^[a-zA-Z0-9ªº\/–\ .,-]+$/';

        return preg_match(pattern: $pattern, subject: $this->complement);
    }

    public function Editar()
    {
        if (!empty($this->cnpj) && validaCNPJ(cnpj: $this->cnpj) === false) {
            $this->mensagem = 'CNPJ inválido';

            return false;
        }

        $this->cnpj = validaCNPJ(cnpj: $this->cnpj) ? idFederal2int(str: urldecode(string: $this->cnpj)) : null;

        if (!$this->validaCaracteresPermitidosComplemento()) {
            $this->mensagem = 'O campo foi preenchido com valor não permitido. O campo Complemento só permite os caracteres: ABCDEFGHIJKLMNOPQRSTUVWXYZ 0123456789 ª º – / . ,';

            return false;
        }

        $objJuridica = new clsJuridica(idpes: false, cnpj: $this->cnpj);

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

        $this->insc_est = idFederal2int(str: $this->insc_est);

        $objPessoa = new clsPessoa_(
            int_idpes: $this->cod_pessoa_fj,
            str_nome: $this->razao_social,
            int_idpes_cad: $this->idpes_cad,
            str_url: $this->url,
            int_tipo: 'J',
            int_idpes_rev: false,
            str_data_rev: false,
            str_email: $this->email
        );
        $objPessoa->edita();

        $objJuridica = new clsJuridica(
            idpes: $this->cod_pessoa_fj,
            cnpj: $this->cnpj,
            fantasia: $this->fantasia,
            insc_estadual: $this->insc_est,
            capital_social: $this->capital_social
        );
        $objJuridica->edita();

        if ($this->telefone_1) {
            $this->telefone_1 = str_replace(search: '-', replace: '', subject: $this->telefone_1);
            $this->telefone_1 = trim(string: $this->telefone_1);
            if (is_numeric(value: $this->telefone_1) && (strlen(string: $this->telefone_1) < 12)) {
                $objTelefone = new clsPessoaTelefone(int_idpes: $this->cod_pessoa_fj, int_tipo: 1, str_fone: $this->telefone_1, str_ddd: $this->ddd_telefone_1);
                if ($objTelefone->detalhe()) {
                    $objTelefone->edita();
                } else {
                    $objTelefone->cadastra();
                }
            }
        }
        if ($this->telefone_2) {
            $this->telefone_2 = str_replace(search: '-', replace: '', subject: $this->telefone_2);
            $this->telefone_2 = trim(string: $this->telefone_2);
            if (is_numeric(value: $this->telefone_2) && (strlen(string: $this->telefone_2) < 12)) {
                $objTelefone = new clsPessoaTelefone(int_idpes: $this->cod_pessoa_fj, int_tipo: 2, str_fone: $this->telefone_2, str_ddd: $this->ddd_telefone_2);
                if ($objTelefone->detalhe()) {
                    $objTelefone->edita();
                } else {
                    $objTelefone->cadastra();
                }
            }
        }
        if ($this->telefone_mov) {
            $this->telefone_mov = str_replace(search: '-', replace: '', subject: $this->telefone_mov);
            $this->telefone_mov = trim(string: $this->telefone_mov);
            if (is_numeric(value: $this->telefone_mov) && (strlen(string: $this->telefone_mov) < 12)) {
                $objTelefone = new clsPessoaTelefone(
                    int_idpes: $this->cod_pessoa_fj,
                    int_tipo: 3,
                    str_fone: $this->telefone_mov,
                    str_ddd: $this->ddd_telefone_mov
                );
                if ($objTelefone->detalhe()) {
                    $objTelefone->edita();
                } else {
                    $objTelefone->cadastra();
                }
            }
        }
        if ($this->telefone_fax) {
            $this->telefone_fax = str_replace(search: '-', replace: '', subject: $this->telefone_fax);
            $this->telefone_fax = trim(string: $this->telefone_fax);
            if (is_numeric(value: $this->telefone_fax) && (strlen(string: $this->telefone_fax) < 12)) {
                $objTelefone = new clsPessoaTelefone(
                    int_idpes: $this->cod_pessoa_fj,
                    int_tipo: 4,
                    str_fone: $this->telefone_fax,
                    str_ddd: $this->ddd_telefone_fax
                );
                if ($objTelefone->detalhe()) {
                    $objTelefone->edita();
                } else {
                    $objTelefone->cadastra();
                }
            }
        }

        $this->saveAddress(person: $this->cod_pessoa_fj, optionalFields: true);
        $this->mensagem = 'Edição efetuada com sucesso.';
        $this->simpleRedirect(url: 'empresas_lst.php');
    }

    public function Excluir()
    {
        $this->simpleRedirect(url: 'empresas_lst.php');
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
            'inline' => true,
        ];

        $this->inputsHelper()->integer(attrName: "ddd_telefone_{$type}", inputOptions: $options);

        // telefone

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => $typeLabel,
            'value' => $this->{"telefone_{$type}"},
            'max_length' => 11,
        ];

        $this->inputsHelper()->integer(attrName: "telefone_{$type}", inputOptions: $options);
    }

    protected function validaDadosTelefones()
    {
        return $this->validaDDDTelefone(valorDDD: $this->ddd_telefone_1, valorTelefone: $this->telefone_1, nomeCampo: 'Telefone 1') &&
            $this->validaDDDTelefone(valorDDD: $this->ddd_telefone_2, valorTelefone: $this->telefone_2, nomeCampo: 'Telefone 2') &&
            $this->validaDDDTelefone(valorDDD: $this->ddd_telefone_mov, valorTelefone: $this->telefone_mov, nomeCampo: 'Celular') &&
            $this->validaDDDTelefone(valorDDD: $this->ddd_telefone_fax, valorTelefone: $this->telefone_fax, nomeCampo: 'Fax');
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
