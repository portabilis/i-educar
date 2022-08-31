<?php

use App\Models\LegacyStudent;
use iEducar\Modules\Enrollments\Model\EnrollmentStatusFilter;

return new class extends clsListagem {
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

    public $cod_inep;
    public $aluno_estado_id;
    public $cod_aluno;
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
    public $nome_pai;
    public $nome_mae;
    public $data_nascimento;
    public $ano;
    public $ref_cod_instituicao;
    public $ref_cod_escola;
    public $ref_cod_curso;
    public $ref_cod_serie;
    public $cpf_aluno;
    public $rg_aluno;
    public $situacao_matricula_id;

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
        $this->campoCpf('cpf_aluno', 'CPF', $this->cpf_aluno);
        $this->campoTexto('rg_aluno', 'RG', $this->rg_aluno);
        $this->campoTexto('nome_pai', 'Nome do Pai', $this->nome_pai, 50, 255);
        $this->campoTexto('nome_mae', 'Nome da Mãe', $this->nome_mae, 50, 255);
        $this->campoTexto('nome_responsavel', 'Nome do Responsável', $this->nome_responsavel, 50, 255);
        $this->campoRotulo('filtros_matricula', '<b>Filtros de alunos</b>');

        $this->inputsHelper()->integer('ano', ['required' => false, 'value'=> $this->ano,'max_length' => 4,'label_hint'=>'Retorna alunos com matrículas no ano selecionado']);
        $this->inputsHelper()->dynamic('instituicao', ['required' => false, 'value' => $this->ref_cod_instituicao]);
        $this->inputsHelper()->dynamic('escolaSemFiltroPorUsuario', ['required' => false, 'value' => $this->ref_cod_escola,'label_hint'=>'Retorna alunos com matrículas na escola selecionada']);
        $this->inputsHelper()->dynamic('curso', ['required' => false,'label_hint'=>'Retorna alunos com matrículas no curso selecionado']);
        $this->inputsHelper()->dynamic('serie', ['required' => false,'label_hint'=>'Retorna alunos com matrículas na série selecionada']);

        $obj_permissoes = new clsPermissoes();
        $cod_escola = $obj_permissoes->getEscola($this->pessoa_logada);

        if ($cod_escola) {
            $this->campoCheck('meus_alunos', 'Meus Alunos', $_GET['meus_alunos']);
            $ref_cod_escola = false;
            if ($_GET['meus_alunos']) {
                $ref_cod_escola = $cod_escola;
            }
        }

        $cabecalhos = ['Código Aluno',
            $configuracoes['mostrar_codigo_inep_aluno'] === 1 ? 'Código INEP' : null,
            'Nome do Aluno',
            'Nome da Mãe',
            'Nome do Responsável',
            'CPF Responsável'
        ];

        $this->addCabecalhos(array_filter($cabecalhos));

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $students = LegacyStudent::query()
            ->with(
                [
                    'individual' => function(Illuminate\Database\Eloquent\Relations\BelongsTo $query) {
                           $query->select(['idpes','idpes_mae','idpes_pai','nome_social']);
                           $query
                               ->with('pai:nome,idpes', 'pai.individual:cpf,idpes')
                               ->with('mae:nome,idpes', 'mae.individual:cpf,idpes')
                               ->with('responsavel:nome,idpes', 'responsavel.individual:cpf,idpes');
                       },
                    'person:idpes,nome',
                    'inep:cod_aluno,cod_aluno_inep'
                ]
            )
            ->paginate($this->limite,['ref_idpes','cod_aluno', 'tipo_responsavel'], "pagina_{$this->nome}");


        /** @var LegacyStudent $registro */
        foreach ($students as $registro) {
            $nomeAluno = $registro->person->name;
            $nomeSocial = $registro->individual->nome_social;

            if ($nomeSocial) {
                $nomeAluno = $nomeSocial . '<br> <i>Nome de registro: </i>' . $nomeAluno;
            }

            $nomeResponsavel = mb_strtoupper($registro->getGuardianName());
            $cpfResponsavel = $registro->getGuardianCpf();
            $nomeMae = mb_strtoupper($registro->individual->mae->name);

            $linhas = array_filter([
                "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">$registro->cod_aluno</a>",
                $configuracoes['mostrar_codigo_inep_aluno'] ===  1 ? "<a href=\"educar_aluno_det.php?cod_aluno={$registro->cod_aluno}\">{$registro->inepNumber}</a>" : null,
                "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">$nomeAluno</a>",
                "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">$nomeMae</a>",
                "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">$nomeResponsavel</a>",
                "<a href=\"educar_aluno_det.php?cod_aluno={$registro['cod_aluno']}\">$cpfResponsavel</a>"
                ]);

            $this->addLinhas($linhas);
        }


       $this->addPaginador2('educar_aluno_lst.php', $students->total(), $_GET, $this->nome, $this->limite);

        $bloquearCadastroAluno = dbBool($configuracoes['bloquear_cadastro_aluno']);
        $usuarioTemPermissaoCadastro = $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7);
        $usuarioPodeCadastrar = $usuarioTemPermissaoCadastro && $bloquearCadastroAluno == false;

        if ($usuarioPodeCadastrar) {
            $this->acao = 'go("/module/Cadastro/aluno")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';
        $this->breadcrumb('Alunos', ['/intranet/educar_index.php' => 'Escola']);
    }

    public function Formular()
    {
        $this->title = 'Aluno';
        $this->processoAp = '578';
    }
};
