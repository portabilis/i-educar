<?php

use App\Facades\Asset;
use App\Models\LegacyEmployee;
use App\Models\LegacyIndividualPicture;
use App\Models\LegacyPhone;
use App\Services\ChangeUserPasswordService;
use App\Services\PhoneService;
use App\Services\UrlPresigner;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

return new class extends clsCadastro
{
    public $nome;

    public $ddd_telefone;

    public $telefone;

    public $ddd_celular;

    public $celular;

    public $email;

    public $senha;

    public $senha_confirma;

    public $sexo;

    public $senha_old;

    public $matricula;

    public $matricula_old;

    public $receber_novidades;

    public $objPhoto;

    public $arquivoFoto;

    public $file_delete;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $pessoaFisica = new clsPessoaFisica($this->pessoa_logada);
        $pessoaFisica = $pessoaFisica->detalhe();

        if ($pessoaFisica) {
            $this->nome = $pessoaFisica['nome'];
            $this->ddd_telefone = $pessoaFisica['ddd_1'];
            $this->telefone = $pessoaFisica['fone_1'];
            $this->ddd_celular = $pessoaFisica['ddd_mov'];
            $this->celular = $pessoaFisica['fone_mov'];
            $this->sexo = $pessoaFisica['sexo'];

            $funcionario = LegacyEmployee::find($this->pessoa_logada);

            if ($funcionario) {
                $this->senha = $funcionario->senha;
                $this->senha_confirma = $funcionario->senha;
                $this->matricula = $funcionario->matricula;
                $this->email = $funcionario->email;

                $this->senha_old = $funcionario->senha;
                $this->matricula_old = $funcionario->matricula;
                $this->receber_novidades = $funcionario->receber_novidades;
            }
        }

        $this->url_cancelar = 'index.php';
        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb('Meus dados', []);

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('senha_old', $this->senha_old);
        $this->campoOculto('matricula_old', $this->matricula_old);

        $foto = false;

        if (is_numeric($this->pessoa_logada)) {
            $foto = LegacyIndividualPicture::whereKey($this->pessoa_logada)->value('caminho') ?? false;
        }

        if ($foto) {
            $this->campoRotulo('fotoAtual_', 'Foto atual', '<img height="117" src="' . (new UrlPresigner)->getPresignedUrl($foto) . '"/>');
            $this->inputsHelper()->checkbox('file_delete', ['label' => 'Excluir a foto']);
            $this->campoArquivo('file', 'Trocar foto', $this->arquivoFoto, 40, '<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho máximo: 150KB</span>');
        } else {
            $this->campoArquivo('file', 'Foto', $this->arquivoFoto, 40, '<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho máximo: 150KB</span>');
        }

        $this->campoTexto('nome', 'Nome', $this->nome, 50, 150, true);
        $this->campoTexto('matricula', 'Matrícula', $this->matricula, 25, 12, true);

        $options = [
            'required' => false,
            'label' => '(DDD) Telefone',
            'placeholder' => 'DDD',
            'value' => $this->ddd_telefone,
            'max_length' => 3,
            'size' => 3,
            'inline' => true,
        ];

        $this->inputsHelper()->integer('ddd_telefone', $options);

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Telefone',
            'value' => $this->telefone,
            'max_length' => 11,
        ];

        $this->inputsHelper()->integer('telefone', $options);

        $options = [
            'required' => false,
            'label' => '(DDD) Celular',
            'placeholder' => 'DDD',
            'value' => $this->ddd_celular,
            'max_length' => 3,
            'size' => 3,
            'inline' => true,
        ];

        $this->inputsHelper()->integer('ddd_celular', $options);

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Celular',
            'value' => $this->celular,
            'max_length' => 11,
        ];

        $this->inputsHelper()->integer('celular', $options);

        $this->campoTexto('email', 'E-mail', $this->email, 50, 100, true);
        $this->campoSenha('senha', 'Senha', $this->senha, true);
        $this->campoSenha('senha_confirma', 'Confirmação de senha', $this->senha_confirma, true);

        $lista_sexos = [
            '' => 'Selecione',
            'M' => 'Masculino',
            'F' => 'Feminino',
        ];

        $this->campoLista('sexo', 'Sexo', $lista_sexos, $this->sexo);
        $this->campoQuebra();

        if (is_null($this->receber_novidades)) {
            $this->receber_novidades = 1;
        }

        $this->inputsHelper()->checkbox('receber_novidades', [
            'label' => 'Desejo receber novidades do produto por e-mail',
            'value' => $this->receber_novidades,
        ]);
    }

    public function Novo()
    {
        $this->Editar();
    }

    public function Editar()
    {
        if (!$this->validatePhoto()) {
            return false;
        }

        $this->savePhoto($this->pessoa_logada);

        app(PhoneService::class)->save(
            personId: $this->pessoa_logada,
            type: LegacyPhone::TYPE_LANDLINE,
            ddd: $this->ddd_telefone,
            phone: $this->telefone
        );

        app(PhoneService::class)->save(
            personId: $this->pessoa_logada,
            type: LegacyPhone::TYPE_MOBILE_ALT,
            ddd: $this->ddd_celular,
            phone: $this->celular
        );

        $pessoa = new clsPessoa_($this->pessoa_logada);
        $pessoa->nome = $this->nome;
        $pessoa->edita();

        $pessoaFisica = new clsFisica($this->pessoa_logada, false, $this->sexo);
        $pessoaFisica->edita();

        if ($this->matricula != $this->matricula_old) {
            $existeMatricula = LegacyEmployee::query()
                ->where('matricula', 'like', "%{$this->matricula}%")
                ->where('ativo', 1)
                ->exists();

            if ($existeMatricula) {
                $this->mensagem = 'A matrícula informada já pertence a outro usuário.';

                return false;
            }
        }

        $senha_old = urldecode($this->senha_old);

        if ($senha_old != $this->senha) {
            if ($this->senha !== $this->senha_confirma) {
                $this->mensagem = 'O campo de confirmação de senha deve ser igual ao campo de confirmação da senha.';

                return false;
            }
            $legacyEmployee = LegacyEmployee::find($this->pessoa_logada);
            $changeUserPasswordService = app(ChangeUserPasswordService::class);
            try {
                $changeUserPasswordService->execute($legacyEmployee, $this->senha);
            } catch (ValidationException $ex) {
                $this->mensagem = $ex->validator->errors()->first();

                return false;
            }
        }

        $dadosAtualizar = [
            'receber_novidades' => $this->receber_novidades ? 1 : 0,
            'atualizou_cadastro' => 1,
        ];

        if (is_string($this->email)) {
            $dadosAtualizar['email'] = $this->email;
        }

        if ($this->matricula != $this->matricula_old && is_string($this->matricula)) {
            $dadosAtualizar['matricula'] = $this->matricula;
        }

        LegacyEmployee::whereKey($this->pessoa_logada)->update($dadosAtualizar);

        $usuario = new clsPmieducarUsuario($this->pessoa_logada);
        $usuario = $usuario->detalhe();

        if ($usuario) {
            $instituicao = new clsPmieducarInstituicao($usuario['ref_cod_instituicao']);
            $instituicao = $instituicao->detalhe();

            $instituicao = $instituicao['nm_instituicao'];

            $escola = new clsPmieducarEscola($usuario['ref_cod_escola']);
            $escola = $escola->detalhe();

            $escola = $escola['nome'];
        }

        $configuracoes = new clsPmieducarConfiguracoesGerais;
        $configuracoes = $configuracoes->detalhe();

        $permiteRelacionamentoPosvendas = $configuracoes['permite_relacionamento_posvendas'] ? 'Sim' : 'Não';

        $dados = [
            'nome' => $this->nome,
            'empresa' => $instituicao,
            'cargo' => $escola,
            'telefone' => $this->telefone ? "$this->ddd_telefone $this->telefone" : null,
            'celular' => $this->celular ? "$this->ddd_celular $this->celular" : null,
            'Assuntos de interesse' => $this->receber_novidades ? 'Todos os assuntos relacionados ao i-Educar' : 'Nenhum',
            'Permite relacionamento direto no pós-venda?' => $permiteRelacionamentoPosvendas,
        ];

        $rdStationParams = [
            'token' => config('legacy.app.rdstation.token'),
            'private_token' => config('legacy.app.rdstation.private_token'),
        ];

        if (!empty($rdStationParams['token']) && !empty($rdStationParams['private_token'])) {
            $rdAPI = new RDStationAPI(
                $rdStationParams['private_token'],
                $rdStationParams['token']
            );

            $rdAPI->sendNewLead($this->email, $dados);
            $rdAPI->updateLeadStage($this->email, 2);
        }

        $this->mensagem .= 'Edição efetuada com sucesso.<br>';
        header('Location: index.php');
        exit();
    }

    // Retorna true caso a foto seja válida
    public function validatePhoto()
    {
        $this->arquivoFoto = $_FILES['file'];

        if (!empty($this->arquivoFoto['name'])) {
            $this->arquivoFoto['name'] = mb_strtolower($this->arquivoFoto['name'], 'UTF-8');
            $this->objPhoto = new PictureController($this->arquivoFoto);

            if ($this->objPhoto->validatePicture()) {
                return true;
            } else {
                $this->mensagem = $this->objPhoto->getErrorMessage();

                return false;
            }
        } else {
            $this->objPhoto = null;

            return true;
        }
    }

    // envia foto e salva caminha no banco
    public function savePhoto($id)
    {
        $caminhoFoto = Asset::get('intranet/imagens/user-perfil.png');
        if ($this->objPhoto != null) {
            $caminhoFoto = $this->objPhoto->sendPicture();
            if ($caminhoFoto != '') {
                if (is_numeric($id) && is_string($caminhoFoto)) {
                    LegacyIndividualPicture::updateOrCreate(['idpes' => $id], ['caminho' => $caminhoFoto]);
                }
            } else {
                echo '<script>alert(\'Foto não salva.\')</script>';

                return false;
            }
            $caminhoFoto = (new UrlPresigner)->getPresignedUrl($caminhoFoto);
        } elseif ($this->file_delete == 'on') {
            LegacyIndividualPicture::whereKey($id)->delete();
        }

        Session::put('logged_user_picture', $caminhoFoto);
        Session::save();

        return true;
    }

    public function Formular()
    {
        $this->title = 'Meus dados';
        $this->processoAp = '0';
    }
};
