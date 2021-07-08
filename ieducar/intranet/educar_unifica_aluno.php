<?php

use App\Models\LogUnification;
use iEducar\Modules\Unification\StudentLogUnification;
use Illuminate\Support\Facades\DB;

return new class extends clsCadastro {
    public $pessoa_logada;
    public $tabela_alunos = [];
    public $aluno_duplicado;

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
            $this->campoRotulo('aluno_label', '', 'Pessoa aluno a ser unificada <span class="campo_obrigatorio">*</span>');
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

    public function Novo()
    {
        $this->validaPermissaoDaPagina();

        if (!count($cod_alunos)) {
            $this->mensagem = 'Informe no mínimo um aluno para unificação.<br />';

            return false;
        }

        DB::beginTransaction();
        $unificationId = $this->createLog($cod_aluno_principal, $cod_alunos, $this->pessoa_logada);
        App_Unificacao_Aluno::unifica($cod_aluno_principal, $cod_alunos, $this->pessoa_logada, new clsBanco(), $unificationId);

        try {
            DB::commit();
        } catch (Throwable $throable) {
            DB::rollBack();
            $this->mensagem = 'Não foi possível realizar a unificação';

            return false;
        }

        $this->mensagem = '<span>Alunos unificados com sucesso.</span>';
        $this->simpleRedirect(route('student-log-unification.index'));
    }

    private function createLog($mainId, $duplicatesId, $createdBy)
    {
        $log = new LogUnification();
        $log->type = StudentLogUnification::getType();
        $log->main_id = $mainId;
        $log->duplicates_id = json_encode($duplicatesId);
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
        $this->title = 'i-Educar - Unificação de alunos';
        $this->processoAp = '999847';
    }
};
