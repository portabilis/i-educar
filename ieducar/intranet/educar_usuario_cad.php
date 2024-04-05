<?php

use App\Events\UserDeleted;
use App\Events\UserUpdated;
use App\Models\LegacyEmployee;
use App\Services\ChangeUserPasswordService;
use App\Services\ValidateUserPasswordService;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

return new class extends clsCadastro
{
    public $ref_pessoa;

    public $nome;

    public $matricula;

    public $_senha;

    public $ativo;

    public $ref_cod_funcionario_vinculo;

    public $matricula_interna;

    public $data_expiracao;

    public $escola;

    public $force_reset_password;

    public function Inicializar()
    {
        $retorno = 'Novo';
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 561, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_usuario_lst.php');
        $this->ref_pessoa = $_POST['ref_pessoa'];

        if ($_GET['ref_pessoa']) {
            $this->ref_pessoa = $_GET['ref_pessoa'];
        }

        if (is_numeric($this->ref_pessoa)) {
            $obj_funcionario = new clsPortalFuncionario($this->ref_pessoa);
            $det_funcionario = $obj_funcionario->detalhe();

            if ($det_funcionario) {
                foreach ($det_funcionario as $campo => $valor) {
                    $this->$campo = $valor;
                }

                $this->_senha = $this->senha;
                $this->fexcluir = true;
            }

            if ($this->data_expiracao) {
                $this->data_expiracao = Portabilis_Date_Utils::pgSQLToBr($this->data_expiracao);
            }

            $obj = new clsPmieducarUsuario($this->ref_pessoa);

            $registro = $obj->detalhe();

            if ($registro) {
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(int_processo_ap: 555, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7);
            }

            if ($det_funcionario !== false) {
                $retorno = 'Editar';
            }
        }

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' usuário', breadcrumbs: [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        $this->montaBotoesDeAcao();

        return $retorno;
    }

    public function Gerar()
    {
        $obj_permissao = new clsPermissoes();

        $this->campoOculto(nome: 'ref_pessoa', valor: $this->ref_pessoa);

        $cadastrando = true;

        if (is_numeric($this->ref_pessoa)) {
            $cadastrando = false;
        }

        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }

        if ($_GET['ref_pessoa']) {
            $obj_funcionario = new clsPessoaFj($this->ref_pessoa);
            $det_funcionario = $obj_funcionario->detalhe();

            $this->nome = $det_funcionario['nome'];

            $this->campoRotulo(nome: 'nome', campo: 'Nome', valor: $this->nome);
        } else {
            $parametros = new clsParametrosPesquisas();
            $parametros->setSubmit(1);
            $parametros->setPessoa('F');
            $parametros->setPessoaNovo('S');
            $parametros->setPessoaEditar('N');
            $parametros->setPessoaTela('frame');
            $parametros->setPessoaCPF('N');
            $parametros->adicionaCampoTexto(campo_nome: 'nome', campo_valor: 'nome');
            $parametros->adicionaCampoTexto(campo_nome: 'nome_busca', campo_valor: 'nome');
            $parametros->adicionaCampoTexto(campo_nome: 'ref_pessoa', campo_valor: 'idpes');
            $this->campoTextoPesquisa(nome: 'nome_busca', campo: 'Nome', valor: $this->nome, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true, caminho: 'pesquisa_pessoa_lst.php', serializedcampos: $parametros->serializaCampos() . '&busca=S', disabled: true);
            $this->campoOculto(nome: 'nome', valor: $this->nome);
            $this->campoOculto(nome: 'ref_pessoa', valor: $this->ref_pessoa);
        }

        $this->campoTexto(nome: 'matricula', campo: 'Matrícula', valor: $this->matricula, tamanhovisivel: 12, tamanhomaximo: 12, obrigatorio: true);
        $this->campoSenha(nome: '_senha', campo: 'Senha', valor: null, obrigatorio: $cadastrando, descricao: empty($cadastrando) ? 'Preencha apenas se desejar alterar a senha' : '');

        if (empty($this->_senha)) {
            $this->inputsHelper()->checkbox(attrName: 'force_reset_password', inputOptions: ['label' => 'Forçar alteração de senha no primeiro acesso', $this->force_reset_password]);
        }

        $this->campoEmail(nome: 'email', campo: 'E-mail usuário', valor: $this->email, tamanhovisivel: 50, tamanhomaximo: 50, descricao: 'Utilizado para redefinir a senha, caso o usúario esqueça<br />Este campo pode ser gravado em branco, neste caso será solicitado um e-mail ao usuário, após entrar no sistema.');
        $this->campoTexto(nome: 'matricula_interna', campo: 'Matrícula interna', valor: $this->matricula_interna, tamanhovisivel: 30, tamanhomaximo: 30, descricao: 'Utilizado somente para registro, caso a instituição deseje que a matrícula interna deste funcionário seja registrada no sistema.');
        $this->campoData(nome: 'data_expiracao', campo: 'Data de expiração', valor: $this->data_expiracao);

        $opcoes = [0 => 'Inativo', 1 => 'Ativo'];

        if (!$this->ref_cod_pessoa_fj == '') {
            $this->campoLista(nome: 'ativo', campo: 'Status', valor: $opcoes, default: $this->ativo);
        } else {
            $this->campoLista(nome: 'ativo', campo: 'Status', valor: $opcoes, default: 1);
        }

        $objFuncionarioVinculo = new clsPmieducarFuncionarioVinculo;
        $opcoes = ['' => 'Selecione'] + $objFuncionarioVinculo->lista();
        $this->campoLista(nome: 'ref_cod_funcionario_vinculo', campo: 'Vínculo', valor: $opcoes, default: $this->ref_cod_funcionario_vinculo);

        $tempoExpiraSenha = config('legacy.app.user_accounts.default_password_expiration_period');

        if (is_numeric($tempoExpiraSenha)) {
            $this->campoOculto(nome: 'tempo_expira_senha', valor: $tempoExpiraSenha);
        } else {
            $opcoes = ['' => 'Selecione', 5 => '5', 30 => '30', 60 => '60', 90 => '90', 120 => '120', 180 => '180'];
            $this->campoLista(nome: 'tempo_expira_senha', campo: 'Dias p/ expirar a senha', valor: $opcoes, default: $this->tempo_expira_senha);
        }

        $opcoes = ['' => 'Selecione'];

        $objTemp = new clsPmieducarTipoUsuario();
        $objTemp->setOrderby('nm_tipo ASC');

        /** @var User $user */
        $user = Auth::user();
        // verifica se pessoa logada é super-usuario
        if ($user->isAdmin()) {
            $lista = $objTemp->lista(int_ativo: 1);
        } else {
            $lista = $objTemp->lista(int_ativo: 1, int_nivel_menor: $obj_permissao->nivel_acesso($this->pessoa_logada));
        }

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['cod_tipo_usuario']}"] = "{$registro['nm_tipo']}";
                $opcoes_["{$registro['cod_tipo_usuario']}"] = "{$registro['nivel']}";
            }
        }

        $tamanho = count($opcoes_);

        echo "<script>\nvar cod_tipo_usuario = new Array({$tamanho});\n";

        foreach ($opcoes_ as $key => $valor) {
            echo "cod_tipo_usuario[{$key}] = {$valor};\n";
        }

        echo '</script>';

        $this->campoLista(nome: 'ref_cod_tipo_usuario', campo: 'Tipo de usuário', valor: $opcoes, default: $this->ref_cod_tipo_usuario, duplo: null, descricao: null, complemento: null, desabilitado: null);

        $nivel = $obj_permissao->nivel_acesso($this->ref_pessoa);

        $this->campoOculto(nome: 'nivel_usuario_', valor: $nivel);

        $this->inputsHelper()->dynamic(['instituicao']);
        $this->inputsHelper()->multipleSearchEscola(attrName: null, inputOptions: [
            'label' => 'Escola(s)',
            'required' => false,
        ]);

        $scripts = ['/vendor/legacy/Cadastro/Assets/Javascripts/Usuario.js'];

        $this->acao_enviar = 'valida()';
        if (!$this->canChange(currentUser: $user, changedUserId: $this->ref_pessoa)) {
            $this->acao_enviar = null;
            $this->fexcluir = null;
            $scripts[] = '/vendor/legacy/Cadastro/Assets/Javascripts/disableAllFields.js';
        }

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: $scripts);

        $this->montaBotoesDeAcao();
    }

    public function Novo()
    {
        if ($this->email && !filter_var(value: $this->email, filter: FILTER_VALIDATE_EMAIL)) {
            $this->mensagem = 'Formato do e-mail inválido.';

            return false;
        }

        if (!$this->validatesUniquenessOfMatricula(pessoaId: $this->ref_pessoa, matricula: $this->matricula)) {
            return false;
        }

        try {
            $this->validatesPassword($this->_senha);
        } catch (ValidationException $ex) {
            $this->mensagem = $ex->validator->errors()->first();

            return false;
        }

        $senha = Hash::make($this->_senha);

        $obj_funcionario = new clsPortalFuncionario(ref_cod_pessoa_fj: $this->ref_pessoa, matricula: $this->matricula, senha: $senha, ativo: $this->ativo, ref_sec: null, ramal: null, sequencial: null, opcao_menu: null, ref_cod_administracao_secretaria: null, ref_ref_cod_administracao_secretaria: null, ref_cod_departamento: null, ref_ref_ref_cod_administracao_secretaria: null, ref_ref_cod_departamento: null, ref_cod_setor: null, ref_cod_funcionario_vinculo: $this->ref_cod_funcionario_vinculo, tempo_expira_senha: $this->tempo_expira_senha, data_expiracao: Portabilis_Date_Utils::brToPgSQL($this->data_expiracao), data_troca_senha: 'NOW()', data_reativa_conta: 'NOW()', ref_ref_cod_pessoa_fj: $this->pessoa_logada, proibido: 0, ref_cod_setor_new: 0, matricula_new: null, matricula_permanente: 0, tipo_menu: 1, email: $this->email, matricula_interna: $this->matricula_interna, forceResetPassword: !is_null($this->force_reset_password));

        if ($obj_funcionario->cadastra()) {
            if ($this->ref_cod_instituicao) {
                $obj = new clsPmieducarUsuario(cod_usuario: $this->ref_pessoa, ref_cod_escola: null, ref_cod_instituicao: $this->ref_cod_instituicao, ref_funcionario_cad: $this->pessoa_logada, ref_funcionario_exc: $this->pessoa_logada, ref_cod_tipo_usuario: $this->ref_cod_tipo_usuario, data_cadastro: null, data_exclusao: null, ativo: 1);
            } else {
                $obj = new clsPmieducarUsuario(cod_usuario: $this->ref_pessoa, ref_cod_escola: null, ref_cod_instituicao: null, ref_funcionario_cad: $this->pessoa_logada, ref_funcionario_exc: $this->pessoa_logada, ref_cod_tipo_usuario: $this->ref_cod_tipo_usuario, data_cadastro: null, data_exclusao: null, ativo: 1);
            }

            if ($obj->existe()) {
                $cadastrou = $obj->edita();
            } else {
                $cadastrou = $obj->cadastra();
            }

            $this->insereUsuarioEscolas(codUsuario: $this->ref_pessoa, escolas: $this->escola);

            if ($cadastrou) {
                $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
                $this->simpleRedirect('educar_usuario_lst.php');
            }
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$this->canChange(currentUser: $user, changedUserId: $this->ref_pessoa)) {
            return false;
        }

        if ($this->email && !filter_var(value: $this->email, filter: FILTER_VALIDATE_EMAIL)) {
            $this->mensagem = 'Formato do e-mail inválido.';

            return false;
        }

        if (!$this->validatesUniquenessOfMatricula(pessoaId: $this->ref_pessoa, matricula: $this->matricula)) {
            return false;
        }

        // Ao editar não é necessário trocar a senha, então apenas quando algo
        // for informado é que a mesma será alterada.
        if ($this->_senha) {
            $legacyEmployee = LegacyEmployee::find($this->ref_pessoa);
            $changeUserPasswordService = app(ChangeUserPasswordService::class);
            try {
                $changeUserPasswordService->execute(legacyEmployee: $legacyEmployee, password: $this->_senha);
            } catch (ValidationException $ex) {
                $this->mensagem = $ex->validator->errors()->first();

                return false;
            }
        }

        $data_reativa_conta = $this->hasChangeStatusUser() && $this->ativo == '1' ? 'NOW()' : null;

        $obj_funcionario = new clsPortalFuncionario(ref_cod_pessoa_fj: $this->ref_pessoa, matricula: $this->matricula, senha: null, ativo: $this->ativo, ref_sec: null, ramal: null, sequencial: null, opcao_menu: null, ref_cod_administracao_secretaria: null, ref_ref_cod_administracao_secretaria: null, ref_cod_departamento: null, ref_ref_ref_cod_administracao_secretaria: null, ref_ref_cod_departamento: null, ref_cod_setor: null, ref_cod_funcionario_vinculo: $this->ref_cod_funcionario_vinculo, tempo_expira_senha: $this->tempo_expira_senha, data_expiracao: Portabilis_Date_Utils::brToPgSQL($this->data_expiracao), data_troca_senha: null, data_reativa_conta: $data_reativa_conta, ref_ref_cod_pessoa_fj: $this->pessoa_logada, proibido: 0, ref_cod_setor_new: 0, matricula_new: null, matricula_permanente: 0, tipo_menu: null, email: $this->email, matricula_interna: $this->matricula_interna);

        if ($obj_funcionario->edita()) {
            if ($this->ref_cod_instituicao) {
                $obj = new clsPmieducarUsuario(
                    cod_usuario: $this->ref_pessoa,
                    ref_cod_escola: null,
                    ref_cod_instituicao: $this->ref_cod_instituicao,
                    ref_funcionario_cad: $this->pessoa_logada,
                    ref_funcionario_exc: $this->pessoa_logada,
                    ref_cod_tipo_usuario: $this->ref_cod_tipo_usuario,
                    data_cadastro: null,
                    data_exclusao: null,
                    ativo: $this->ativo
                );
            } else {
                $obj = new clsPmieducarUsuario(
                    cod_usuario: $this->ref_pessoa,
                    ref_cod_escola: null,
                    ref_cod_instituicao: null,
                    ref_funcionario_cad: $this->pessoa_logada,
                    ref_funcionario_exc: $this->pessoa_logada,
                    ref_cod_tipo_usuario: $this->ref_cod_tipo_usuario,
                    data_cadastro: null,
                    data_exclusao: null,
                    ativo: $this->ativo
                );
            }

            if ($obj->existe()) {
                $editou = $obj->edita();
            } else {
                $editou = $obj->cadastra();
            }

            $this->insereUsuarioEscolas(codUsuario: $this->ref_pessoa, escolas: $this->escola);

            if ($editou) {
                UserUpdated::dispatch(User::findOrFail($this->ref_pessoa));

                $this->mensagem .= 'Edição efetuada com sucesso.<br>';
                $this->simpleRedirect('educar_usuario_lst.php');
            }
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$this->canChange(currentUser: $user, changedUserId: $this->ref_pessoa)) {
            return false;
        }

        $obj_funcionario = new clsPortalFuncionario($this->ref_pessoa);

        if ($obj_funcionario->excluir()) {
            UserDeleted::dispatch(User::findOrFail($this->ref_pessoa));

            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_usuario_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function validatesUniquenessOfMatricula($pessoaId, $matricula)
    {
        $sql = "select 1 from portal.funcionario where lower(matricula) = lower('$matricula') and ref_cod_pessoa_fj != $pessoaId";
        $db = new clsBanco();

        if ($db->CampoUnico($sql) == '1') {
            $this->mensagem = "A matrícula '$matricula' já foi usada, por favor, informe outra.";

            return false;
        }

        return true;
    }

    public function excluiTodosVinculosEscola($codUsuario)
    {
        $usuarioEscola = new clsPmieducarEscolaUsuario();
        $usuarioEscola->excluirTodos($codUsuario);
    }

    public function insereUsuarioEscolas($codUsuario, $escolas)
    {
        $this->excluiTodosVinculosEscola($codUsuario);

        foreach ($escolas as $e) {
            $usuarioEscola = new clsPmieducarEscolaUsuario();
            $usuarioEscola->ref_cod_usuario = $codUsuario;
            $usuarioEscola->ref_cod_escola = $e;
            $usuarioEscola->cadastra();
        }
    }

    /**
     * Verifica se o usuário logado pode alterar o usuário em questão
     *
     * Caso algum usuário com nível diferente de admin tentar alterar dados do usuário admin,
     * esse método retornará false
     *
     * @param int $changedUserId
     * @return bool
     */
    private function canChange(User $currentUser, $changedUserId)
    {
        if (!$changedUserId) {
            return true;
        }

        if ($currentUser->isAdmin()) {
            return true;
        }

        /** @var User $changedUser */
        $changedUser = User::find($changedUserId);

        if (empty($changedUser)) {
            return true;
        }

        if (!$changedUser->isAdmin()) {
            return true;
        }

        return false;
    }

    public function Formular()
    {
        $this->title = 'Cadastro de usuários';
        $this->processoAp = 555;
    }

    public function hasChangeStatusUser(): bool
    {
        $legacyEmployer = LegacyEmployee::find($this->ref_pessoa);

        return $legacyEmployer->ativo != $this->ativo;
    }

    public function validatesPassword($password)
    {
        $validateUserPasswordService = app(ValidateUserPasswordService::class);
        $validateUserPasswordService->execute($password);
    }

    private function montaBotoesDeAcao(): void
    {
        $funcionario = (new clsPortalFuncionario($this->ref_pessoa))->detalhe();
        $usuario = (new clsPmieducarUsuario($this->ref_pessoa))->detalhe();

        $edita = false;
        if ($funcionario !== false && $usuario !== false) {
            $edita = true;
        }

        $this->url_cancelar = $edita
            ? "educar_usuario_det.php?ref_pessoa={$this->ref_pessoa}"
            : 'educar_usuario_lst.php';

        $this->fexcluir = $edita;

        $this->nome_url_cancelar = 'Cancelar';
    }
};
