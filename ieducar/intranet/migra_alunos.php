<?php

use App\Models\Individual;
use App\Models\LogUnification;
use iEducar\Modules\Unification\PersonLogUnification;
use iEducar\Modules\Unification\StudentLogUnification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

return new class {

    private $pessoa_logada;

    public function __construct()
    {
        $this->pessoa_logada = Auth::user()->id;
    }

    public function RenderHTML()
    {

        set_time_limit(0);
        ini_set('memory_limit', -1);

        $dados = $this->getStudents();

        foreach ($dados as $item) {
            $data = $this->splitInformations($item);
            if ($data === false || count($data) === 0) {
                continue;
            }
            $this->processStuddents($data);
        }


        return '';
    }

    private function splitInformations($item): ?array
    {
        $cleanData = substr(substr($item->alunos,1),0, -1);

        return explode(',', $cleanData);
    }

    private function findStudentPrincipal(array $studentCods)
    {
        return min($studentCods);
    }


    private function clearStudentList(string $pricipal, array $studentCods): array
    {
        foreach ($studentCods as $index => $value) {
            if ($value === $pricipal) {
                unset($studentCods[$index]);
            }
        }

        return array_values($studentCods);
    }

    private function processStuddents(array $studentCods)
    {
        $principal = $this->findStudentPrincipal($studentCods);
        $studentCods = $this->clearStudentList($principal, $studentCods);

        $this->unificaAlunos($principal, $studentCods);

        $alunosPrincipal = $this->getStudentDetails($principal);
        $codPessoaPrincipal = $alunosPrincipal['ref_idpes'];
        $codPessoas  = $this->buscaCodPessoas($studentCods);

        $this->unificaPessoas($codPessoaPrincipal, $codPessoas);

    }

    private function buscaCodPessoas($studentCods)
    {
        $data = [];
        foreach ($studentCods as $cod) {
            $alunosPrincipal = $this->getStudentDetails($cod);
            $codPessoaPrincipal = $alunosPrincipal['ref_idpes'];
            $data[] = $codPessoaPrincipal;
        }

        return $data;
    }

    private function unificaAlunos($principal, $cod_alunos)
    {
        DB::beginTransaction();
        $unificationId = $this->createLog($principal, $cod_alunos, $this->pessoa_logada);
        App_Unificacao_Aluno::unifica($principal, $cod_alunos, $this->pessoa_logada, new clsBanco(), $unificationId);

        try {
            DB::commit();
        } catch (Throwable $throable) {
            DB::rollBack();
            $this->mensagem = 'Não foi possível realizar a unificação';

            return false;
        }
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

    private function createLogPerson($mainId, $duplicatesId, $createdBy)
    {
        $log = new LogUnification();
        $log->type = PersonLogUnification::getType();
        $log->main_id = $mainId;
        $log->duplicates_id = json_encode($duplicatesId);
        $log->created_by = $createdBy;
        $log->updated_by = $createdBy;
        $log->duplicates_name = json_encode($this->getNamesOfUnifiedPeople($duplicatesId));
        $log->save();

        return $log->id;
    }


    /**
     * Retorna os nomes das pessoas unificadas
     *
     * @param integer[] $duplicatesId
     *
     * @return string[]
     */
    private function getNamesOfUnifiedPeople($duplicatesId)
    {
        $names = [];

        foreach ($duplicatesId as $personId) {
            $names[] = Individual::query()->findOrFail($personId)->real_name;
        }

        return $names;
    }

    private function unificaPessoas($codPessoaPrincipal, $codPessoas)
    {
        DB::beginTransaction();

        $unificationId = $this->createLogPerson($codPessoaPrincipal, $codPessoas, $this->pessoa_logada);
        $unificador = new App_Unificacao_Pessoa($codPessoaPrincipal, $codPessoas, $this->pessoa_logada, new clsBanco(), $unificationId);

        try {
            $unificador->unifica();
            DB::commit();
        } catch (CoreExt_Exception $exception) {
            $this->mensagem = $exception->getMessage();
            DB::rollBack();
            return false;
        }
    }

    private function getStudentDetails($id): array
    {
        return (new clsPmieducarAluno($id))->detalhe();
    }

    private function getStudents()
    {
        $query = <<<SQL
            SELECT  array_agg(alunos.cod_aluno) alunos FROM (

            SELECT
                a.cod_aluno,
                unaccent(upper(p.nome)) nome_aluno,
                unaccent(upper(pmae.nome)) nome_mae,
                unaccent(upper(ppai.nome)) nome_pai
            FROM pmieducar.aluno AS a

            JOIN cadastro.pessoa AS p ON a.ref_idpes = p.idpes
            JOIN cadastro.fisica AS f ON f.idpes = p.idpes

            JOIN cadastro.pessoa AS pmae ON pmae.idpes = f.idpes_mae
            JOIN cadastro.pessoa AS ppai ON ppai.idpes = f.idpes_pai
            ) as alunos

            GROUP BY alunos.nome_aluno, alunos.nome_mae, alunos.nome_pai
            HAVING count(alunos.nome_aluno) > 1
            ORDER BY alunos.nome_aluno
        SQL;

        foreach (DB::select($query) as $data) {
            yield $data;
        }
    }

    public function Formular()
    {
        $this->titulo = 'Migra Alunos';
        $this->processoAp = 0;
    }
};
