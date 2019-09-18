<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'Educacenso/Model/AlunoDataMapper.php';
require_once 'Portabilis/Utils/CustomLabel.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} i-Educar - Aluno");
        $this->processoAp = '578';
    }
}

class indice extends clsListagem
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset;

    public $cod_aluno;
    public $ref_idpes_responsavel;
    public $ref_cod_aluno_beneficio;
    public $ref_cod_religiao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_idpes;
    public $ativo;
    public $nome_aluno;
    public $mat_aluno;
    public $identidade;
    public $matriculado;
    public $inativado;
    public $nome_responsavel;
    public $cpf_responsavel;
    public $nome_pai;
    public $nome_mae;
    public $data_nascimento;
    public $ano;
    public $ref_cod_instituicao;
    public $ref_cod_escola;
    public $ref_cod_curso;
    public $ref_cod_serie;

    public function Gerar()
    {
        $this->titulo = 'Aluno - Listagem';

        $configuracoes = new clsPmieducarConfiguracoesGerais();
        $configuracoes = $configuracoes->detalhe();

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $this->campoNumero('cod_aluno', _cl('aluno.detalhe.codigo_aluno'), $this->cod_aluno, 20, 9, false);

        if ($configuracoes['mostrar_codigo_inep_aluno']) {
            $this->campoNumero('cod_inep', 'Código INEP', $this->cod_inep, 20, 255, false);
        }

        $this->campoRA('aluno_estado_id', 'Código rede estadual do aluno (RA)', $this->aluno_estado_id, false);
        $this->campoTexto('nome_aluno', 'Nome do aluno', $this->nome_aluno, 50, 255, false);
        $this->campoData('data_nascimento', 'Data de Nascimento', $this->data_nascimento);
        $this->campoTexto('nome_pai', 'Nome do Pai', $this->nome_pai, 50, 255);
        $this->campoTexto('nome_mae', 'Nome da Mãe', $this->nome_mae, 50, 255);
        $this->campoTexto('nome_responsavel', 'Nome do Responsável', $this->nome_responsavel, 50, 255);
        $this->campoRotulo('filtros_matricula', '<b>Filtros de matrículas em andamento</b>');

        $this->inputsHelper()->integer('ano', ['required' => false, 'value' => $this->ano, 'max_length' => 4]);
        $this->inputsHelper()->dynamic('instituicao', ['required' => false, 'show-select' => true, 'value' => $this->ref_cod_instituicao]);
        $this->inputsHelper()->dynamic(
            'escola', [
                'required' => false,
                'show-select' => true,
                'value' => $this->ref_cod_escola
            ]
        );
        $this->inputsHelper()->dynamic(['curso', 'serie'], ['required' => false]);

        //$this->inputsHelper()->select('periodo', array('required' => false, 'value' => $this->periodo, 'resources' => array(null => 'Selecione', 1 => 'Matutino', 2 => 'Vespertino', 3 => 'Noturno', 4 => 'Integral' )));

        $obj_permissoes = new clsPermissoes();
        $cod_escola = $obj_permissoes->getEscola($this->pessoa_logada);

        if ($cod_escola) {
            $this->campoCheck('meus_alunos', 'Meus Alunos', $_GET['meus_alunos']);
            $ref_cod_escola = false;
            if ($_GET['meus_alunos']) {
                $ref_cod_escola = $cod_escola;
            }
        }

        $array_matriculado = ['S' => 'Sim', 'N' => 'Não'];
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if (!$configuracoes['mostrar_codigo_inep_aluno']) {
            $cabecalhos = ['Código Aluno',
                'Nome do Aluno',
                'Nome da Mãe',
                'Nome do Responsável',
                'CPF Responsável',];
        } else {
            $cabecalhos = ['Código Aluno',
                'Código INEP',
                'Nome do Aluno',
                'Nome da Mãe',
                'Nome do Responsável',
                'CPF Responsável',];
        }

        $this->addCabecalhos($cabecalhos);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $aluno = new clsPmieducarAluno();
        $aluno->setLimite($this->limite, $this->offset);

        $alunos = $aluno->lista2(
            $this->cod_aluno,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            null,
            $this->nome_aluno,
            null,
            idFederal2int($this->cpf_responsavel),
            null,
            null,
            null,
            $ref_cod_escola,
            null,
            $this->data_nascimento,
            $this->nome_pai,
            $this->nome_mae,
            $this->nome_responsavel,
            $this->cod_inep,
            $this->aluno_estado_id,
            $this->ano,
            $this->ref_cod_instituicao,
            $this->ref_cod_escola,
            $this->ref_cod_curso,
            $this->ref_cod_serie
        );

        $total = $aluno->_total;

        foreach ($alunos as $registro) {
            $alunoInepId = $this->tryLoadAlunoInepId($registro['cod_aluno']);
            $nomeAluno = strtoupper($registro['nome_aluno']);
            $nomeSocial = strtoupper($registro['nome_social']);

            if ($nomeSocial) {
                $nomeAluno = $nomeSocial . '<br> <i>Nome de registro: </i>' . $nomeAluno;
            }
            $nomeMae = strtoupper($this->loadNomeMae($registro));

            // responsavel
            $aluno->cod_aluno = $registro['cod_aluno'];
            $responsavel = $aluno->getResponsavelAluno();
            $nomeResponsavel = strtoupper($responsavel['nome_responsavel']);

            if (!$configuracoes['mostrar_codigo_inep_aluno']) {
                $linhas = [
                    "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">{$registro['cod_aluno']}</a>",
                    "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">{$nomeAluno}</a>",
                    "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">{$nomeMae}</a>",
                    "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">{$nomeResponsavel}</a>",
                    "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">{$responsavel['cpf_responsavel']}</a>"
                ];
            } else {
                $linhas = [
                    "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">{$registro['cod_aluno']}</a>",
                    "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">{$alunoInepId}</a>",
                    "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">{$nomeAluno}</a>",
                    "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">{$nomeMae}</a>",
                    "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">{$nomeResponsavel}</a>",
                    "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">{$responsavel['cpf_responsavel']}</a>"
                ];
            }

            $this->addLinhas($linhas);
        }

        $this->addPaginador2('educar_aluno_lst.php', $total, $_GET, $this->nome, $this->limite);

        $bloquearCadastroAluno = dbBool($configuracoes['bloquear_cadastro_aluno']);
        $usuarioTemPermissaoCadastro = $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7);
        $usuarioPodeCadastrar = $usuarioTemPermissaoCadastro && $bloquearCadastroAluno == false;

        // Verifica se o usuário tem permissão para cadastrar um aluno.
        // O sistema irá validar o cadastro de permissões e o parâmetro
        // "bloquear_cadastro_aluno" da instituição.

        if ($usuarioPodeCadastrar) {
            $this->acao = 'go("/module/Cadastro/aluno")';
            $this->nome_acao = 'Novo';
        }

        if ($_GET) {
            $this->array_botao_script = ['dataExport("formcadastro", "students")'];
            $this->array_botao = ['Exportar para planilha'];
            $this->array_botao_id = ['export-btn'];
        }

        $this->largura = '100%';

        Portabilis_View_Helper_Application::loadJavascript($this, ['/intranet/scripts/exporter.js']);

        $this->breadcrumb('Alunos', ['/intranet/educar_index.php' => 'Escola']);
    }

    protected function loadNomeMae($aluno)
    {
        $nome = $aluno['nm_mae'];

        $pessoaAluno = new clsFisica($aluno['ref_idpes']);
        $pessoaAluno = $pessoaAluno->detalhe();

        if ($pessoaAluno['idpes_mae']) {
            $pessoaMae = new clsPessoaFj($pessoaAluno['idpes_mae']);
            $pessoaMae = $pessoaMae->detalhe();
            $nome = $pessoaMae['nome'];
        }

        return $nome;
    }

    protected function tryLoadAlunoInepId($alunoId)
    {
        $dataMapper = new Educacenso_Model_AlunoDataMapper();

        try {
            $alunoInep = $dataMapper->find(['cod_aluno' => $alunoId]);
            $id = $alunoInep->alunoInep;
        } catch (Exception $e) {
            $id = '';
        }

        return $id;
    }
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm($miolo);
// gera o html
$pagina->MakeAll();
