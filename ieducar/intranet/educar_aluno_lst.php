<?php

use App\Models\DataSearch\StudentFilter;
use App\Models\LegacyStudent;

return new class extends clsListagem
{
    public $titulo;

    public $limite;

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

        $this->campoNumero(nome: 'cod_aluno', campo: _cl(key: 'aluno.detalhe.codigo_aluno'), valor: $this->cod_aluno, tamanhovisivel: 20, tamanhomaximo: 9);

        if ($configuracoes['mostrar_codigo_inep_aluno']) {
            $this->campoNumero(nome: 'cod_inep', campo: 'Código INEP', valor: $this->cod_inep, tamanhovisivel: 20, tamanhomaximo: 255);
        }

        $this->campoRA(nome: 'aluno_estado_id', campo: 'Código rede estadual do aluno (RA)', valor: $this->aluno_estado_id);
        $this->campoTexto(nome: 'nome_aluno', campo: 'Nome do aluno', valor: $this->nome_aluno, tamanhovisivel: 50, tamanhomaximo: 255);
        $this->campoCheck(
            nome: 'similaridade',
            campo: 'Similaridade',
            valor: request()->has('similaridade'),
            desc: 'Ativar busca por similaridade',
            dica: 'Exibe grafias parecidas com o nome do aluno que você está buscando'
        );
        $this->campoData(nome: 'data_nascimento', campo: 'Data de Nascimento', valor: $this->data_nascimento);
        $this->campoCpf(nome: 'cpf_aluno', campo: 'CPF', valor: $this->cpf_aluno);
        $this->campoTexto(nome: 'rg_aluno', campo: 'RG', valor: $this->rg_aluno);
        $this->campoTexto(nome: 'nome_pai', campo: 'Nome do Pai', valor: $this->nome_pai, tamanhovisivel: 50, tamanhomaximo: 255);
        $this->campoTexto(nome: 'nome_mae', campo: 'Nome da Mãe', valor: $this->nome_mae, tamanhovisivel: 50, tamanhomaximo: 255);
        $this->campoTexto(nome: 'nome_responsavel', campo: 'Nome do Responsável', valor: $this->nome_responsavel, tamanhovisivel: 50, tamanhomaximo: 255);
        $this->campoRotulo(nome: 'filtros_matricula', campo: '<b>Filtros de alunos</b>');

        $this->inputsHelper()->integer(attrName: 'ano', inputOptions: ['required' => false, 'value' => $this->ano, 'max_length' => 4, 'label_hint' => 'Retorna alunos com matrículas no ano selecionado']);
        $this->inputsHelper()->dynamic(helperNames: 'instituicao', inputOptions: ['required' => false, 'value' => $this->ref_cod_instituicao]);
        $this->inputsHelper()->dynamic(helperNames: 'escolaSemFiltroPorUsuario', inputOptions: ['required' => false, 'value' => $this->ref_cod_escola, 'label_hint' => 'Retorna alunos com matrículas na escola selecionada']);
        $this->inputsHelper()->dynamic(helperNames: 'curso', inputOptions: ['required' => false, 'label_hint' => 'Retorna alunos com matrículas no curso selecionado']);
        $this->inputsHelper()->dynamic(helperNames: 'serie', inputOptions: ['required' => false, 'label_hint' => 'Retorna alunos com matrículas na série selecionada']);

        $obj_permissoes = new clsPermissoes();
        $cod_escola = $obj_permissoes->getEscola(int_idpes_usuario: $this->pessoa_logada);

        if ($cod_escola) {
            $this->campoCheck(nome: 'meus_alunos', campo: 'Meus Alunos', valor: $_GET['meus_alunos']);
            if ($_GET['meus_alunos']) {
                $this->ref_cod_escola = $cod_escola;
            }
        }

        $cabecalhos = ['Código Aluno',
            $configuracoes['mostrar_codigo_inep_aluno'] === 1 ? 'Código INEP' : null,
            'Nome do Aluno',
            'Nome da Mãe',
            'Nome do Responsável',
            'CPF Responsável',
        ];

        $this->addCabecalhos(coluna: array_filter(array: $cabecalhos));

        $validator_date = Validator::make(request()->only(keys: 'data_nascimento'), ['data_nascimento' => ['nullable', 'date_format:d/m/Y', 'after_or_equal:1990-01-01']]);
        if ($validator_date->fails()) {
            $this->data_nascimento = null;
        }
        $this->cod_aluno = preg_replace(pattern: '/\D/', replacement: '', subject: $this->cod_aluno);
        $this->cod_inep = preg_replace(pattern: '/\D/', replacement: '', subject: $this->cod_inep);
        $this->nome_aluno = $this->cleanNameSearch(name: $this->nome_aluno);
        $this->nome_pai = $this->cleanNameSearch(name: $this->nome_pai);
        $this->nome_mae = $this->cleanNameSearch(name: $this->nome_mae);

        $dataFilter = [
            'rg' => preg_replace(pattern: '/\D/', replacement: '', subject: $this->rg_aluno),
            'year' => $this->ano,
            'cpf' => preg_replace(pattern: '/\D/', replacement: '', subject: $this->cpf_aluno),
            'inep' => $this->cod_inep,
            'grade' => $this->ref_cod_serie,
            'school' => $this->ref_cod_escola,
            'course' => $this->ref_cod_curso,
            'birthdate' => $this->data_nascimento,
            'fatherName' => $this->nome_pai,
            'motherName' => $this->nome_mae,
            'studentName' => $this->nome_aluno,
            'studentCode' => (int) $this->cod_aluno > 0 ? $this->cod_aluno : null,
            'stateNetwork' => $this->aluno_estado_id,
            'responsableName' => $this->nome_responsavel,
            'perPage' => 20,
            'pageName' => $this->nome,
            'similarity' => request()->has('similaridade'),
        ];

        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $studentFilter = new StudentFilter(...$dataFilter);
        $students = LegacyStudent::query()->findStudentWithMultipleSearch(studentFilter: $studentFilter);

        foreach ($students as $student) {
            $nomeAluno = $student->person->name;
            $nomeSocial = $student->individual->nome_social;

            if ($nomeSocial) {
                $nomeAluno = $nomeSocial . '<br> <i>Nome de registro: </i>' . $nomeAluno;
            }

            $nomeResponsavel = mb_strtoupper(string: $student->getGuardianName() ?? '-');
            $cpfResponsavel = ucfirst(string: $student->getGuardianCpf());
            $nomeMae = mb_strtoupper(string: $student->individual->mother->name ?? '-');

            $linhas = array_filter(array: [
                "<a href=\"educar_aluno_det.php?cod_aluno=$student->cod_aluno\">$student->cod_aluno</a>",
                $configuracoes['mostrar_codigo_inep_aluno'] === 1 ? "<a href=\"educar_aluno_det.php?cod_aluno=$student->cod_aluno\">$student->inepNumber</a>" : null,
                "<a href=\"educar_aluno_det.php?cod_aluno=$student->cod_aluno\">$nomeAluno</a>",
                "<a href=\"educar_aluno_det.php?cod_aluno=$student->cod_aluno\">$nomeMae</a>",
                "<a href=\"educar_aluno_det.php?cod_aluno=$student->cod_aluno\">$nomeResponsavel</a>",
                "<a href=\"educar_aluno_det.php?cod_aluno=$student->cod_aluno\">$cpfResponsavel</a>",
            ]);

            $this->addLinhas(linha: $linhas);
        }

        $this->addPaginador2(strUrl: 'educar_aluno_lst.php', intTotalRegistros: $students->total(), mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        $bloquearCadastroAluno = dbBool(val: $configuracoes['bloquear_cadastro_aluno']);
        $usuarioTemPermissaoCadastro = $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7);
        $usuarioPodeCadastrar = $usuarioTemPermissaoCadastro && $bloquearCadastroAluno == false;

        if ($usuarioPodeCadastrar) {
            $this->acao = 'go("/module/Cadastro/aluno")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';
        $this->breadcrumb(currentPage: 'Alunos', breadcrumbs: ['/intranet/educar_index.php' => 'Escola']);
    }

    public function Formular()
    {
        $this->title = 'Aluno';
        $this->processoAp = '578';
    }

    public function cleanNameSearch($name)
    {
        return trim(string: preg_replace(pattern: '/\W/', replacement: ' ', subject: limpa_acentos(str_nome: $name)));
    }
};
