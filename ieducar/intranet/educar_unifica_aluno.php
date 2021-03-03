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
        $retorno = 'Novo';

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            999847,
            $this->pessoa_logada,
            7,
            'index.php'
        );

        $this->breadcrumb('Cadastrar unificação', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->url_cancelar = route('student-log-unification.index');
        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        $this->inputsHelper()->dynamic('ano', ['required' => false, 'max_length' => 4]);
        $this->inputsHelper()->dynamic('instituicao', ['required' =>  false, 'show-select' => true]);
        $this->inputsHelper()->dynamic('escola', ['required' =>  false, 'show-select' => true, 'value' => 0]);
        $this->inputsHelper()->simpleSearchAluno(null, ['label' => 'Aluno principal' ]);
        $this->campoTabelaInicio('tabela_alunos', '', ['Aluno duplicado'], $this->tabela_alunos);
        $this->campoTexto('aluno_duplicado', 'Aluno duplicado', $this->aluno_duplicado, 50, 255, false, true, false, '', '', '', 'onfocus');
        $this->campoTabelaFim();
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            999847,
            $this->pessoa_logada,
            7,
            'index.php'
        );

        $cod_aluno_principal = (int) $this->aluno_id;

        if (!$cod_aluno_principal) {
            return;
        }

        $cod_alunos = [];

        //Monta um array com o código dos alunos selecionados na tabela
        foreach ($this->aluno_duplicado as $key => $value) {
            $explode = explode(' ', $value);

            if ($explode[0] == $cod_aluno_principal) {
                $this->mensagem = 'Impossivel de unificar alunos iguais.<br />';

                return false;
            }

            $cod_alunos[] = (int) $explode[0];
        }

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
        $this->titulo = 'i-Educar - Unificação de alunos';
        $this->processoAp = '999847';
    }
};
