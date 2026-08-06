<?php

use App\Models\LegacyOrganization;
use App\Models\LegacyPerson;
use App\Models\LegacyPhone;
use App\Services\PhoneService;
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

    // Telefones
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
            $pessoa = LegacyPerson::with(['organization', 'phones'])->find($this->cod_pessoa_fj);

            if ($pessoa) {
                $this->email = $pessoa->email;
                $this->url = $pessoa->url;
                $this->razao_social = $pessoa->nome;
                $this->insc_est = $pessoa->organization?->insc_estadual;
                $this->capital_social = $pessoa->organization?->capital_social;
                $this->fantasia = $pessoa->organization?->fantasia;
                $cnpj = $pessoa->organization?->cnpj;
                $this->cnpj = validaCNPJ(cnpj: $cnpj) ? int2CNPJ(int: $cnpj) : null;

                foreach ($pessoa->phones as $phone) {
                    if ($sufixo = $phone->legacy_suffix) {
                        $this->{"ddd_telefone_{$sufixo}"} = $phone->ddd;
                        $this->{"telefone_{$sufixo}"} = $phone->fone;
                    }
                }
            }

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
        if (!$this->validaFormatoUrl(url: $this->url)) {
            $this->mensagem = 'O campo Site deve conter uma URL válida (ex: https://www.exemplo.com.br).';
            $this->busca_empresa = true;

            return false;
        }

        if (!empty($this->email) && filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->mensagem = 'O campo E-mail deve conter um endereço de e-mail válido.';
            $this->busca_empresa = true;

            return false;
        }

        if (empty($this->razao_social) || strlen(trim(string: $this->razao_social)) < 3) {
            $this->mensagem = 'O campo Razão Social deve conter no mínimo 3 caracteres.';
            $this->busca_empresa = true;

            return false;
        }

        if (!$this->validaTamanhoMinimoCampo(valor: $this->fantasia)) {
            $this->mensagem = 'O campo Nome Fantasia deve conter no mínimo 3 caracteres.';
            $this->busca_empresa = true;

            return false;
        }

        if (empty($this->fantasia) || strlen(trim(string: $this->fantasia)) < 3) {
            $this->mensagem = 'O campo Nome Fantasia deve conter no mínimo 3 caracteres.';
            $this->busca_empresa = true;

            return false;
        }

        if (!empty($this->capital_social) && !is_numeric(str_replace(search: [',', '.'], replace: '', subject: $this->capital_social))) {
            $this->mensagem = 'O campo Capital Social deve conter apenas valores numéricos.';
            $this->busca_empresa = true;

            return false;
        }

        if (!$this->validaValorPositivo(valor: $this->capital_social)) {
            $this->mensagem = 'O campo Capital Social não pode conter valores negativos.';
            $this->busca_empresa = true;

            return false;
        }

        if (!empty($this->insc_est)) {
            $inscricaoLimpa = trim(strtoupper($this->insc_est));
            if ($inscricaoLimpa !== 'ISENTO' && !is_numeric(str_replace(['.', '-', ' '], '', $inscricaoLimpa))) {
                $this->mensagem = 'O campo Inscrição Estadual deve conter apenas números ou a palavra ISENTO.';
                $this->busca_empresa = true;

                return false;
            }
        }

        if (!empty($this->cnpj) && validaCNPJ(cnpj: $this->cnpj) === false) {
            $this->mensagem = 'CNPJ inválido';

            return false;
        }

        $this->cnpj = validaCNPJ(cnpj: $this->cnpj) ? normalizaCnpj(cnpj: urldecode(string: $this->cnpj)) : null;

        if ($this->cnpj !== null && LegacyOrganization::where('cnpj', $this->cnpj)->exists()) {
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

        if (!$this->razao_social) {
            return false;
        }

        $this->insc_est = idFederal2int(str: $this->insc_est);

        $this->cod_pessoa_fj = LegacyPerson::create([
            'nome' => $this->razao_social,
            'tipo' => 'J',
            'url' => $this->url ?: null,
            'email' => $this->email ?: null,
            'idpes_cad' => Auth::id(),
        ])->idpes;

        if (is_numeric($this->cod_pessoa_fj) && Auth::check() && LegacyPerson::whereKey($this->cod_pessoa_fj)->exists()) {
            LegacyOrganization::create([
                'idpes' => $this->cod_pessoa_fj,
                'cnpj' => $this->cnpj,
                'fantasia' => $this->fantasia,
                'insc_estadual' => is_numeric($this->insc_est) ? $this->insc_est : null,
                'capital_social' => $this->capital_social,
                'idpes_cad' => Auth::id(),
            ]);
        }

        app(PhoneService::class)->save(
            personId: $this->cod_pessoa_fj,
            type: LegacyPhone::TYPE_LANDLINE,
            ddd: $this->ddd_telefone_1,
            phone: $this->telefone_1
        );

        app(PhoneService::class)->save(
            personId: $this->cod_pessoa_fj,
            type: LegacyPhone::TYPE_MOBILE,
            ddd: $this->ddd_telefone_2,
            phone: $this->telefone_2
        );

        app(PhoneService::class)->save(
            personId: $this->cod_pessoa_fj,
            type: LegacyPhone::TYPE_MOBILE_ALT,
            ddd: $this->ddd_telefone_mov,
            phone: $this->telefone_mov
        );

        app(PhoneService::class)->save(
            personId: $this->cod_pessoa_fj,
            type: LegacyPhone::TYPE_FAX,
            ddd: $this->ddd_telefone_fax,
            phone: $this->telefone_fax
        );

        $this->saveAddress(person: $this->cod_pessoa_fj);

        $this->mensagem = 'Cadastro salvo com sucesso.';

        $this->simpleRedirect(url: 'empresas_lst.php');

        return true;
    }

    /**
     * Valida se uma URL possui formato válido
     *
     * @param string $url A URL a validar
     * @return bool True se válida, false caso contrário
     */
    private function validaFormatoUrl($url)
    {
        if (empty($url)) {
            return true;
        }

        return filter_var(value: $url, options: FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Valida se um campo de texto possui tamanho mínimo
     *
     * @param string $valor O valor do campo a validar
     * @param int $tamanhoMinimo O tamanho mínimo permitido
     * @return bool True se válido, false caso contrário
     */
    private function validaTamanhoMinimoCampo($valor, $tamanhoMinimo = 3)
    {
        return !empty($valor) && strlen(trim(string: $valor)) >= $tamanhoMinimo;
    }

    /**
     * Valida se um valor numérico é positivo (maior ou igual a zero)
     *
     * @param string|float $valor O valor a validar
     * @return bool True se válido (positivo ou zero), false caso contrário
     */
    private function validaValorPositivo($valor)
    {
        if (empty($valor)) {
            return true;
        }

        $valorNumerico = (float) str_replace(search: [',', '.'], replace: ['.', ''], subject: $valor);
        return $valorNumerico >= 0;
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
        if (!$this->validaFormatoUrl(url: $this->url)) {
            $this->mensagem = 'O campo Site deve conter uma URL válida (ex: https://www.exemplo.com.br).';
            $this->busca_empresa = true;

            return false;
        }

        if (!empty($this->email) && filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->mensagem = 'O campo E-mail deve conter um endereço de e-mail válido.';
            $this->busca_empresa = true;

            return false;
        }

        if (empty($this->razao_social) || strlen(trim(string: $this->razao_social)) < 3) {
            $this->mensagem = 'O campo Razão Social deve conter no mínimo 3 caracteres.';
            $this->busca_empresa = true;

            return false;
        }

        if (!$this->validaTamanhoMinimoCampo(valor: $this->fantasia)) {
            $this->mensagem = 'O campo Nome Fantasia deve conter no mínimo 3 caracteres.';
            $this->busca_empresa = true;

            return false;
        }

        if (empty($this->fantasia) || strlen(trim(string: $this->fantasia)) < 3) {
            $this->mensagem = 'O campo Nome Fantasia deve conter no mínimo 3 caracteres.';
            $this->busca_empresa = true;

            return false;
        }

        if (!empty($this->capital_social) && !is_numeric(str_replace(search: [',', '.'], replace: '', subject: $this->capital_social))) {
            $this->mensagem = 'O campo Capital Social deve conter apenas valores numéricos.';
            $this->busca_empresa = true;

            return false;
        }

        if (!$this->validaValorPositivo(valor: $this->capital_social)) {
            $this->mensagem = 'O campo Capital Social não pode conter valores negativos.';
            $this->busca_empresa = true;

            return false;
        }

        if (!empty($this->insc_est)) {
            $inscricaoLimpa = trim(strtoupper($this->insc_est));
            if ($inscricaoLimpa !== 'ISENTO' && !is_numeric(str_replace(['.', '-', ' '], '', $inscricaoLimpa))) {
                $this->mensagem = 'O campo Inscrição Estadual deve conter apenas números ou a palavra ISENTO.';
                $this->busca_empresa = true;

                return false;
            }
        }

        if (!empty($this->cnpj) && validaCNPJ(cnpj: $this->cnpj) === false) {
            $this->mensagem = 'CNPJ inválido';

            return false;
        }

        $this->cnpj = validaCNPJ(cnpj: $this->cnpj) ? normalizaCnpj(cnpj: urldecode(string: $this->cnpj)) : null;

        if (!$this->validaCaracteresPermitidosComplemento()) {
            $this->mensagem = 'O campo foi preenchido com valor não permitido. O campo Complemento só permite os caracteres: ABCDEFGHIJKLMNOPQRSTUVWXYZ 0123456789 ª º – / . ,';

            return false;
        }

        if ($this->cnpj !== null) {
            $idpesCnpjExistente = LegacyOrganization::where('cnpj', $this->cnpj)->value('idpes');
            if ($idpesCnpjExistente !== null && $this->cod_pessoa_fj != $idpesCnpjExistente) {
                $this->mensagem = 'Já existe uma empresa cadastrada com este CNPJ.';

                return false;
            }
        }

        if (!$this->validaDadosTelefones()) {
            // variável buscar_empresa é usada para definir os campos que aparecem na tela, quando false apresenta apenas o campo de CNPJ
            // por tanto é preciso setar para true para que a mensagem de erro seja apresentada com os demais campos normalmente.
            $this->busca_empresa = true;

            return false;
        }

        if (!$this->razao_social) {
            return false;
        }

        $this->insc_est = idFederal2int(str: $this->insc_est);

        LegacyPerson::find($this->cod_pessoa_fj)?->update([
            'nome' => $this->razao_social,
            'url' => $this->url ?: null,
            'email' => $this->email ?: null,
            'idpes_rev' => Auth::id(),
        ]);

        if (is_numeric($this->cod_pessoa_fj) && Auth::check()) {
            LegacyOrganization::find($this->cod_pessoa_fj)?->update([
                'cnpj' => $this->cnpj,
                'fantasia' => $this->fantasia,
                'capital_social' => $this->capital_social,
                'insc_estadual' => (is_numeric($this->insc_est) && $this->insc_est) ? $this->insc_est : null,
                'idpes_rev' => Auth::id(),
            ]);
        }

        app(PhoneService::class)->save(
            personId: $this->cod_pessoa_fj,
            type: LegacyPhone::TYPE_LANDLINE,
            ddd: $this->ddd_telefone_1,
            phone: $this->telefone_1
        );

        app(PhoneService::class)->save(
            personId: $this->cod_pessoa_fj,
            type: LegacyPhone::TYPE_MOBILE,
            ddd: $this->ddd_telefone_2,
            phone: $this->telefone_2
        );

        app(PhoneService::class)->save(
            personId: $this->cod_pessoa_fj,
            type: LegacyPhone::TYPE_MOBILE_ALT,
            ddd: $this->ddd_telefone_mov,
            phone: $this->telefone_mov
        );

        app(PhoneService::class)->save(
            personId: $this->cod_pessoa_fj,
            type: LegacyPhone::TYPE_FAX,
            ddd: $this->ddd_telefone_fax,
            phone: $this->telefone_fax
        );

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
