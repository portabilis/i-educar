<?php

use App\Models\LogUnification;
use App\Services\ValidationDataService;
use iEducar\Modules\Unification\StudentLogUnification;
use Illuminate\Support\Facades\DB;

return new class extends clsCadastro {
    public $pessoa_logada;
    public $tabela_alunos = [];
    public $aluno_duplicado;
    public $alunos;

    public function Inicializar()
    {
        $this->validaPermissaoDaPagina();

        $this->breadcrumb('Cadastrar unificação', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        return 'Novo';
    }

    public function Gerar()
    {
        $this->acao_enviar = 'carregaDadosAlunos()';
        $this->campoTabelaInicio('tabela_alunos', '', ['Aluno duplicado', 'Campo aluno duplicado'], $this->tabela_alunos);
        $this->campoRotulo('aluno_label', '', 'Aluno(a) a ser unificado(a)  <span class="campo_obrigatorio">*</span>');
        $this->campoTexto('aluno_duplicado', 'Aluno duplicado', $this->aluno_duplicado, 50, 255, false, true, false, '', '', '', 'onfocus');
        $this->campoTabelaFim();

        $styles = ['/modules/Cadastro/Assets/Stylesheets/UnificaAluno.css'];
        Portabilis_View_Helper_Application::loadStylesheet($this, $styles);
        $scripts = ['/modules/Portabilis/Assets/Javascripts/ClientApi.js'];
        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    private function validaPermissaoDaPagina()
    {
        (new clsPermissoes())
            ->permissao_cadastra(
                999847,
                $this->pessoa_logada,
                7,
                'index.php'
            );
    }

    private function validaDadosDaUnificacaoAluno($alunos): bool
    {
        foreach ($alunos as $item) {
            if (! isset($item['codAluno'])) {
                return false;
            }

            if (! isset($item['aluno_principal'])) {
                return false;
            }
        }

        return true;
    }

    public function Novo()
    {
        $this->validaPermissaoDaPagina();

        try {
            $alunos = json_decode($this->alunos, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $exception) {
            $this->mensagem = 'Informações inválidas para unificação';
            return false;
        }

        if (! $this->validaDadosDaUnificacaoAluno($alunos)) {
            $this->mensagem = 'Dados enviados inválidos, recarregue a tela e tente novamente!';
            return false;
        }

        $validationData = new ValidationDataService();

        if (! $validationData->verifyQuantityByKey($alunos,'aluno_principal', 0)) {
            $this->mensagem = 'Aluno principal não informado';
            return false;
        }

        if ($validationData->verifyQuantityByKey($alunos,'aluno_principal', 1)) {
            $this->mensagem = 'Não pode haver mais de uma aluno principal';
            return false;
        }

        if (! $validationData->verifyDataContainsDuplicatesByKey($alunos,'codAluno')) {
            $this->mensagem = 'Erro ao tentar unificar Alunos, foi inserido cadastro duplicados';
            return false;
        }

        $cod_aluno_principal = $this->buscaPessoaPrincipal($alunos);
        $cod_alunos = $this->buscaIdesDasPessoasParaUnificar($alunos);

        DB::beginTransaction();
        $unificationId = $this->createLog($cod_aluno_principal, $cod_alunos, $this->pessoa_logada);
        App_Unificacao_Aluno::unifica($cod_aluno_principal, $cod_alunos, $this->pessoa_logada, new clsBanco(), $unificationId);

        try {
            DB::commit();
        } catch (Throwable) {
            DB::rollBack();
            $this->mensagem = 'Não foi possível realizar a unificação';

            return false;
        }

        $this->mensagem = '<span>Alunos unificados com sucesso.</span>';
        $this->simpleRedirect(route('student-log-unification.index'));
    }

    private function buscaIdesDasPessoasParaUnificar($pessoas)
    {
        return array_values(array_map(static fn ($item) => (int) $item['codAluno'],
            array_filter($pessoas, static fn ($pessoas) => $pessoas['aluno_principal'] === false)
        ));
    }

    private function buscaPessoaPrincipal($pessoas)
    {
        $pessoas = array_values(array_filter($pessoas,
                static fn ($pessoas) => $pessoas['aluno_principal'] === true)
        );

        return (int) current($pessoas)['codAluno'];
    }

    private function createLog($mainId, $duplicatesId, $createdBy)
    {
        $log = new LogUnification();
        $log->type = StudentLogUnification::getType();
        $log->main_id = $mainId;
        $log->duplicates_id = json_encode(array_values($duplicatesId));
        $log->created_by = $createdBy;
        $log->updated_by = $createdBy;
        $log->save();

        return $log->id;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-unifica-aluno.js');
    }

    public function Formular()
    {
        $this->title = 'Unificação de alunos';
        $this->processoAp = '999847';
    }
};
