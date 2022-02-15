<?php

use App\Models\LegacyEmployee;
use App\Services\ChangeUserPasswordService;
use App\Services\ValidateUserPasswordService;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

return new class extends clsCadastro {
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
        $obj_permissoes->permissao_cadastra(561, $this->pessoa_logada, 7, 'educar_usuario_lst.php');
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

            $this->status = $this->ativo;

            $obj = new clsPmieducarUsuario($this->ref_pessoa);

            $registro = $obj->detalhe();

            if ($registro) {
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(555, $this->pessoa_logada, 7);
            }

            if ($det_funcionario !== false) {
                $retorno = 'Editar';
            }
        }

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' usuário', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        $this->montaBotoesDeAcao();

        return $retorno;
    }

    public function Gerar()
    {
        $obj_permissao = new clsPermissoes();

        $this->campoOculto('ref_pessoa', $this->ref_pessoa);

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

            $this->campoRotulo('nome', 'Nome', $this->nome);
        } else {
            $parametros = new clsParametrosPesquisas();
            $parametros->setSubmit(1);
            $parametros->setPessoa('F');
            $parametros->setPessoaNovo('S');
            $parametros->setPessoaEditar('N');
            $parametros->setPessoaTela('frame');
            $parametros->setPessoaCPF('N');
            $parametros->adicionaCampoTexto('nome', 'nome');
            $parametros->adicionaCampoTexto('nome_busca', 'nome');
            $parametros->adicionaCampoTexto('ref_pessoa', 'idpes');
            $this->campoTextoPesquisa('nome_busca', 'Nome', $this->nome, 30, 255, true, 'pesquisa_pessoa_lst.php', false, false, '', '', $parametros->serializaCampos() . '&busca=S', true);
            $this->campoOculto('nome', $this->nome);
            $this->campoOculto('ref_pessoa', $this->ref_pessoa);
        }

        $this->campoTexto('matricula', 'Matrícula', $this->matricula, 12, 12, true);
        $this->campoSenha('_senha', 'Senha', null, $cadastrando, empty($cadastrando) ? 'Preencha apenas se desejar alterar a senha' : '');

        if (empty($this->_senha)) {
            $this->inputsHelper()->checkbox('force_reset_password', ['label' => 'Forçar alteração de senha no primeiro acesso', $this->force_reset_password]);
        }

        $this->campoEmail('email', 'E-mail usuário', $this->email, 50, 50, false, false, false, 'Utilizado para redefinir a senha, caso o usúario esqueça<br />Este campo pode ser gravado em branco, neste caso será solicitado um e-mail ao usuário, após entrar no sistema.');
        $this->campoTexto('matricula_interna', 'Matrícula interna', $this->matricula_interna, 30, 30, false, false, false, 'Utilizado somente para registro, caso a instituição deseje que a matrícula interna deste funcionário seja registrada no sistema.');
        $this->campoData('data_expiracao', 'Data de expiração', $this->data_expiracao);

        $opcoes = [0 => 'Inativo', 1 => 'Ativo'];

        if (!$this->ref_cod_pessoa_fj == '') {
            $this->campoLista('ativo', 'Status', $opcoes, $this->status);
        } else {
            $this->campoLista('ativo', 'Status', $opcoes, 1);
        }

        $objFuncionarioVinculo = new clsPmieducarFuncionarioVinculo;
        $opcoes = ['' => 'Selecione'] + $objFuncionarioVinculo->lista();
        $this->campoLista('ref_cod_funcionario_vinculo', 'Vínculo', $opcoes, $this->ref_cod_funcionario_vinculo);

        $tempoExpiraSenha = config('legacy.app.user_accounts.default_password_expiration_period');

        if (is_numeric($tempoExpiraSenha)) {
            $this->campoOculto('tempo_expira_senha', $tempoExpiraSenha);
        } else {
            $opcoes = ['' => 'Selecione', 5 => '5', 30 => '30', 60 => '60', 90 => '90', 120 => '120', 180 => '180'];
            $this->campoLista('tempo_expira_senha', 'Dias p/ expirar a senha', $opcoes, $this->tempo_expira_senha);
        }

        $opcoes = ['' => 'Selecione'];

        $objTemp = new clsPmieducarTipoUsuario();
        $objTemp->setOrderby('nm_tipo ASC');

        /** @var User $user */
        $user = Auth::user();
        // verifica se pessoa logada é super-usuario
        if ($user->isAdmin()) {
            $lista = $objTemp->lista(null, null, null, null, null, null, null, null, 1);
        } else {
            $lista = $objTemp->lista(null, null, null, null, null, null, null, null, 1, $obj_permissao->nivel_acesso($this->pessoa_logada));
        }

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['cod_tipo_usuario']}"] = "{$registro['nm_tipo']}";
                $opcoes_["{$registro['cod_tipo_usuario']}"] = "{$registro['nivel']}";
            }
        }

        $tamanho = sizeof($opcoes_);

        echo "<script>\nvar cod_tipo_usuario = new Array({$tamanho});\n";

        foreach ($opcoes_ as $key => $valor) {
            echo "cod_tipo_usuario[{$key}] = {$valor};\n";
        }

        echo '</script>';

        $this->campoLista('ref_cod_tipo_usuario', 'Tipo de usuário', $opcoes, $this->ref_cod_tipo_usuario, '', null, null, null, null, true);

        $nivel = $obj_permissao->nivel_acesso($this->ref_pessoa);

        $this->campoOculto('nivel_usuario_', $nivel);

        $this->inputsHelper()->dynamic(['instituicao']);
        $this->inputsHelper()->multipleSearchEscola(null, [
            'label' => 'Escola(s)',
            'required' => false
        ]);

        $scripts = ['/modules/Cadastro/Assets/Javascripts/Usuario.js'];

        $this->acao_enviar = 'valida()';
        if (!$this->canChange($user, $this->ref_pessoa)) {
            $this->acao_enviar = null;
            $this->fexcluir = null;
            $scripts[] = '/modules/Cadastro/Assets/Javascripts/disableAllFields.js';
        }

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);

        $this->montaBotoesDeAcao();
    }

    public function Novo()
    {
        if ($this->email && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->mensagem = 'Formato do e-mail inválido.';

            return false;
        }

        if (!$this->validatesUniquenessOfMatricula($this->ref_pessoa, $this->matricula)) {
            return false;
        }

        try {
            $this->validatesPassword($this->_senha);
        } catch (ValidationException $ex) {
            $this->mensagem = $ex->validator->errors()->first();
            return false;
        }

        $senha = Hash::make($this->_senha);

        $obj_funcionario = new clsPortalFuncionario($this->ref_pessoa, $this->matricula, $senha, $this->ativo, null, null, null, null, null, null, null, null, null, null, $this->ref_cod_funcionario_vinculo, $this->tempo_expira_senha, Portabilis_Date_Utils::brToPgSQL($this->data_expiracao), 'NOW()', 'NOW()', $this->pessoa_logada, 0, 0, null, 0, 1, $this->email, $this->matricula_interna, !is_null($this->force_reset_password));

        if ($obj_funcionario->cadastra()) {
            if ($this->ref_cod_instituicao) {
                $obj = new clsPmieducarUsuario($this->ref_pessoa, null, $this->ref_cod_instituicao, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_tipo_usuario, null, null, 1);
            } else {
                $obj = new clsPmieducarUsuario($this->ref_pessoa, null, null, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_tipo_usuario, null, null, 1);
            }

            if ($obj->existe()) {
                $cadastrou = $obj->edita();
            } else {
                $cadastrou = $obj->cadastra();
            }

            $this->insereUsuarioEscolas($this->ref_pessoa, $this->escola);

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
        if (!$this->canChange($user, $this->ref_pessoa)) {
            return false;
        }

        if ($this->email && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->mensagem = 'Formato do e-mail inválido.';

            return false;
        }

        if (!$this->validatesUniquenessOfMatricula($this->ref_pessoa, $this->matricula)) {
            return false;
        }

        // Ao editar não é necessário trocar a senha, então apenas quando algo
        // for informado é que a mesma será alterada.
        if ($this->_senha) {
            $legacyEmployee = LegacyEmployee::find($this->ref_pessoa);
            $changeUserPasswordService = app(ChangeUserPasswordService::class);
            try {
                $changeUserPasswordService->execute($legacyEmployee, $this->_senha);
            } catch (ValidationException $ex){
                $this->mensagem = $ex->validator->errors()->first();
                return false;
            }
        }

        $data_reativa_conta = $this->hasChangeStatusUser() && $this->ativo == '1' ? 'NOW()' : null;

        $obj_funcionario = new clsPortalFuncionario($this->ref_pessoa, $this->matricula, null, $this->ativo, null, null, null, null, null, null, null, null, null, null, $this->ref_cod_funcionario_vinculo, $this->tempo_expira_senha, Portabilis_Date_Utils::brToPgSQL($this->data_expiracao), null, $data_reativa_conta, $this->pessoa_logada, 0, 0, null, 0, null, $this->email, $this->matricula_interna);

        if ($obj_funcionario->edita()) {
            if ($this->ref_cod_instituicao) {
                $obj = new clsPmieducarUsuario($this->ref_pessoa, null, $this->ref_cod_instituicao, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_tipo_usuario, null, null, 1);
            } else {
                $obj = new clsPmieducarUsuario($this->ref_pessoa, null, null, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_tipo_usuario, null, null, 1);
            }

            if ($obj->existe()) {
                $editou = $obj->edita();
            } else {
                $editou = $obj->cadastra();
            }

            $this->insereUsuarioEscolas($this->ref_pessoa, $this->escola);

            if ($this->nivel_usuario_ == 8) {
                $obj_tipo = new clsPmieducarTipoUsuario($this->ref_cod_tipo_usuario);
                $det_tipo = $obj_tipo->detalhe();
                if ($det_tipo['nivel'] != 8) {
                    $obj_usuario_bib = new clsPmieducarBibliotecaUsuario();
                    $lista_bibliotecas_usuario = $obj_usuario_bib->lista(null, $this->pessoa_logada);

                    if ($lista_bibliotecas_usuario) {
                        foreach ($lista_bibliotecas_usuario as $usuario) {
                            $obj_usuario_bib = new clsPmieducarBibliotecaUsuario($usuario['ref_cod_biblioteca'], $this->pessoa_logada);
                            if (!$obj_usuario_bib->excluir()) {
                                return false;
                            }
                        }
                    }
                }
            }

            if ($this->ref_cod_instituicao != $this->ref_cod_instituicao_) {
                $obj_biblio = new clsPmieducarBiblioteca();
                $lista_biblio_inst = $obj_biblio->lista(null, $this->ref_cod_instituicao_);
                if ($lista_biblio_inst) {
                    foreach ($lista_biblio_inst as $biblioteca) {
                        $obj_usuario_bib = new clsPmieducarBibliotecaUsuario($biblioteca['cod_biblioteca'], $this->pessoa_logada);
                        $obj_usuario_bib->excluir();
                    }
                }
            }

            if ($editou) {
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
        if (!$this->canChange($user, $this->ref_pessoa)) {
            return false;
        }

        $obj_funcionario = new clsPortalFuncionario($this->ref_pessoa);

        if ($obj_funcionario->excluir()) {
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
     * @param User    $currentUser
     * @param integer $changedUserId
     *
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
