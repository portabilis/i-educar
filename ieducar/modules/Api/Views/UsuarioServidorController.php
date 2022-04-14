<?php

use App\Services\ValidateUserPasswordService;

class UsuarioServidorController extends ApiCoreController
{
    public function cadastrarUsuarioServidor ()
    {
        $email = $this->getRequest()->email;
        $ref_cod_instituicao = $this->getRequest()->ref_cod_instituicao;
        $ref_pessoa = $this->getRequest()->ref_pessoa;
        $matricula = $this->getRequest()->matricula;
        $_senha = $this->getRequest()->_senha;
        $ativo = $this->getRequest()->ativo;
        $ref_cod_funcionario_vinculo = $this->getRequest()->ref_cod_funcionario_vinculo;
        $tempo_expira_senha = $this->getRequest()->tempo_expira_senha;
        $data_expiracao = $this->getRequest()->data_expiracao;
        $pessoa_logada = $this->getRequest()->pessoa_logada;
        $matricula_interna = $this->getRequest()->matricula_interna;
        $force_reset_password = $this->getRequest()->force_reset_password;
        $ref_cod_tipo_usuario = $this->getRequest()->ref_cod_tipo_usuario;
        $escolas = $this->getRequest()->escolas;

        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['result' => 'Formato do e-mail inválido.'];
        }

        if (!$this->validatesUniquenessOfMatricula($ref_pessoa, $matricula)) {
            return false;
        }

        try {
            $this->validatesPassword($_senha);
        } catch (ValidationException $ex) {
            return ['result' => $ex->validator->errors()->first()];
        }

        $senha = Hash::make($_senha);

        $obj_funcionario = new clsPortalFuncionario($ref_pessoa, $matricula, $senha, $ativo, null, null, null, null, null, null, null, null, null, null, $ref_cod_funcionario_vinculo, $tempo_expira_senha, Portabilis_Date_Utils::brToPgSQL($data_expiracao), 'NOW()', 'NOW()', $pessoa_logada, 0, 0, null, 0, 1, $email, $matricula_interna, !is_null($force_reset_password));

        if ($obj_funcionario->cadastra()) {
            if ($this->ref_cod_instituicao) {
                $obj = new clsPmieducarUsuario($ref_pessoa, null, $ref_cod_instituicao, $pessoa_logada, $pessoa_logada, $ref_cod_tipo_usuario, null, null, 1);
            } else {
                $obj = new clsPmieducarUsuario($ref_pessoa, null, null, $pessoa_logada, $pessoa_logada, $ref_cod_tipo_usuario, null, null, 1);
            }

            if ($obj->existe()) {
                $cadastrou = $obj->edita();
            } else {
                $cadastrou = $obj->cadastra();
            }

            $this->insereUsuarioEscolas($ref_pessoa, $escolas);

            if ($cadastrou) {
                $result = [];
                $result["cod_servidor"] = $ref_pessoa;
                $result["matricula"] = $matricula;
                return ['result' => $result];
            }
            else
                return false;
        }

        return false;
    }

    public function desativarUsuarioServidor () {
        $cod_servidor = $this->getRequest()->cod_servidor;

        if (is_numeric($cod_servidor)) {
            $obj = new clsPortalFuncionario($cod_servidor);
            $resultado = $obj->desativar();

            return ['result' => $resultado];
        } else {
            return ['result' => 'Código do servidor é inválido.'];
        }
    }

    public function ativarUsuarioServidor () {
        $cod_servidor = $this->getRequest()->cod_servidor;

        if (is_numeric($cod_servidor)) {
            $obj = new clsPortalFuncionario($cod_servidor);
            $resultado = $obj->ativar();

            return ['result' => $resultado];
        } else {
            return ['result' => 'Código do servidor é inválido.'];
        }
    }

    public function validatesUniquenessOfMatricula($pessoaId, $matricula)
    {
        $sql = "select 1 from portal.funcionario where lower(matricula) = lower('$matricula') and ref_cod_pessoa_fj != $pessoaId";
        $db = new clsBanco();

        if ($db->CampoUnico($sql) == '1') {
            return ['result' => "A matrícula '$matricula' já foi usada, por favor, informe outra."];
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

    public function validatesPassword($password)
    {
        $validateUserPasswordService = app(ValidateUserPasswordService::class);
        $validateUserPasswordService->execute($password);
    }

    public function Gerar()
    {
        if ($this->isRequestFor('post', 'cadastrar-usuario-servidor')) {
            $this->appendResponse($this->cadastrarUsuarioServidor());
        } else if ($this->isRequestFor('post', 'desativar-usuario-servidor')) {
            $this->appendResponse($this->desativarUsuarioServidor());
        } else if ($this->isRequestFor('post', 'ativar-usuario-servidor')) {
            $this->appendResponse($this->ativarUsuarioServidor());
        }
    }
}
