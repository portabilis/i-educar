<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/RDStationAPI.class.php';
require_once 'image_check.php';

class clsIndex extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . 'Configurações - Meus dados');
        $this->processoAp = '0';
    }
}

class indice extends clsCadastro
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

    public $caminho_det;

    public $caminho_lst;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $pessoaFisica = new clsPessoaFisica($this->pessoa_logada);
        $pessoaFisica = $pessoaFisica->detalhe();

        if ($pessoaFisica) {
            $this->nome = $pessoaFisica['nome'];

            if ($pessoaFisica) {
                $this->ddd_telefone = $pessoaFisica['ddd_1'];
                $this->telefone = $pessoaFisica['fone_1'];
                $this->ddd_celular = $pessoaFisica['ddd_mov'];
                $this->celular = $pessoaFisica['fone_mov'];
                $this->sexo = $pessoaFisica['sexo'];
            }

            $funcionario = new clsPortalFuncionario($this->pessoa_logada);
            $funcionario = $funcionario->detalhe();

            if ($funcionario) {
                $this->senha = $funcionario['senha'];
                $this->senha_confirma = $funcionario['senha'];
                $this->matricula = $funcionario['matricula'];
                $this->email = $funcionario['email'];

                $this->senha_old = $funcionario['senha'];
                $this->matricula_old = $funcionario['matricula'];
                $this->receber_novidades = $funcionario['receber_novidades'];
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
            $objFoto = new ClsCadastroFisicaFoto($this->pessoa_logada);
            $detalheFoto = $objFoto->detalhe();

            if (count($detalheFoto)) {
                $foto = $detalheFoto['caminho'];
            }
        } else {
            $foto = false;
        }

        if ($foto) {
            $this->campoRotulo('fotoAtual_', 'Foto atual', '<img height="117" src="' .$foto. '"/>');
            $this->inputsHelper()->checkbox('file_delete', ['label' => 'Excluir a foto']);
            $this->campoArquivo('file', 'Trocar foto', $this->arquivoFoto, 40, '<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho m&aacute;ximo: 150KB</span>');
        } else {
            $this->campoArquivo('file', 'Foto', $this->arquivoFoto, 40, '<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho m&aacute;ximo: 150KB</span>');
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
            'inline' => true
        ];

        $this->inputsHelper()->integer('ddd_telefone', $options);

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Telefone',
            'value' => $this->telefone,
            'max_length' => 11
        ];

        $this->inputsHelper()->integer('telefone', $options);

        $options = [
            'required' => false,
            'label' => '(DDD) Celular',
            'placeholder' => 'DDD',
            'value' => $this->ddd_celular,
            'max_length' => 3,
            'size' => 3,
            'inline' => true
        ];

        $this->inputsHelper()->integer('ddd_celular', $options);

        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => 'Celular',
            'value' => $this->celular,
            'max_length' => 11
        ];

        $this->inputsHelper()->integer('celular', $options);

        $this->campoTexto('email', 'E-mail', $this->email, 50, 100, true);
        $this->campoSenha('senha', 'Senha', $this->senha, true);
        $this->campoSenha('senha_confirma', 'Confirmação de senha', $this->senha_confirma, true);

        $lista_sexos = [
            '' => 'Selecione',
            'M' => 'Masculino',
            'F' => 'Feminino'
        ];

        $this->campoLista('sexo', 'Sexo', $lista_sexos, $this->sexo);
        $this->campoQuebra();

        if (is_null($this->receber_novidades)) {
            $this->receber_novidades = 1;
        }

        $this->inputsHelper()->checkbox('receber_novidades', [
            'label' => 'Desejo receber novidades do produto por e-mail',
            'value' => $this->receber_novidades
        ]);
    }

    public function Novo()
    {
        $this->Editar();
    }

    public function Editar()
    {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->mensagem = 'Formato do e-mail inválido.';

            return false;
        }
        if ($this->senha != $this->senha_confirma) {
            $this->mensagem = 'As senhas que você digitou não conferem.';

            return false;
        }

        if (strlen($this->senha) < 8) {
            $this->mensagem = 'Por favor informe uma senha mais segura, com pelo menos 8 caracteres.';

            return false;
        }

        if (strrpos($this->senha, $this->matricula)) {
            $this->mensagem = 'A senha informada &eacute; similar a sua matricula, informe outra senha.';

            return false;
        }

        if (!$this->validatePhoto()) {
            return false;
        }

        $this->savePhoto($this->pessoa_logada);

        $telefone = new clsPessoaTelefone($this->pessoa_logada, 1, str_replace('-', '', $this->telefone), $this->ddd_telefone);
        $telefone->cadastra();

        $celular = new clsPessoaTelefone($this->pessoa_logada, 3, str_replace('-', '', $this->celular), $this->ddd_celular);
        $celular->cadastra();

        $pessoa = new clsPessoa_($this->pessoa_logada);
        $pessoa->nome = $this->nome;
        $pessoa->edita();

        $pessoaFisica = new clsFisica($this->pessoa_logada, false, $this->sexo);
        $pessoaFisica->edita();

        $funcionario = new clsPortalFuncionario();

        if ($this->matricula != $this->matricula_old) {
            $existeMatricula = $funcionario->lista($this->matricula);

            if ($existeMatricula) {
                $this->mensagem = 'A matrícula informada já perdence a outro usuário.';

                return false;
            }

            $funcionario->matricula = $this->matricula;
        }
        $funcionario->ref_cod_pessoa_fj = $this->pessoa_logada;
        $funcionario->receber_novidades = ($this->receber_novidades ? 1 : 0);
        $funcionario->atualizou_cadastro = 1;
        $funcionario->email = $this->email;

        if ($this->senha_old != $this->senha) {
            $funcionario->senha = md5($this->senha);
        }

        $funcionario->edita();

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

        $configuracoes = new clsPmieducarConfiguracoesGerais();
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
            'private_token' => config('legacy.app.rdstation.private_token')
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
        die();
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

            return false;
        } else {
            $this->objPhoto = null;

            return true;
        }
    }

    //envia foto e salva caminha no banco
    public function savePhoto($id)
    {
        if ($this->objPhoto != null) {
            $caminhoFoto = $this->objPhoto->sendPicture();
            if ($caminhoFoto != '') {
                $obj = new clsCadastroFisicaFoto($id, $caminhoFoto);
                $detalheFoto = $obj->detalhe();
                if (is_array($detalheFoto) && count($detalheFoto) > 0) {
                    $obj->edita();
                } else {
                    $obj->cadastra();
                }

                return true;
            } else {
                echo '<script>alert(\'Foto não salva.\')</script>';

                return false;
            }
        } elseif ($this->file_delete == 'on') {
            $obj = new clsCadastroFisicaFoto($id);
            $obj->excluir();
        }
    }
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm($miolo);
$pagina->MakeAll();
