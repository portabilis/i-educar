<?php

namespace App\Http\Controllers;

use App\Jobs\EnrollmentsPromotionJob;
use App\Models\NotificationType;
use App\Services\NotificationService;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class EnrollmentsPromotionController extends Controller
{
    public function processEnrollmentsPromotionJobs(Request $request)
    {
        $data = $this->getResquetData($request);

        $updateScore = $request->boolean('atualizar_notas', false);

        $enrollmentsIds = $this->loadEnrollmentsByFilter($data);

        $userId = $request->user()->id;
        $databaseConnection = DB::getDefaultConnection();

        $jobs = [];
        foreach ($enrollmentsIds as $item) {
            $jobs[] = new EnrollmentsPromotionJob($userId, $item, $databaseConnection, $updateScore);
        }

        if (empty($jobs)) {
            return response()->json([
                'message' => 'Não foi possível encontrar matrículas com os parâmetros enviados',
                'status' => 'notice',
            ]);
        }

        $message = 'Processo de atualização de matrículas finalizado. Total de itens processados: ';
        Bus::batch($jobs)
            ->finally(function (Batch $batch) use ($userId, $message) {
                $message .= $batch->totalJobs . ' matrícula(s)';
                (new NotificationService())->createByUser($userId, $message, '', NotificationType::OTHER);
            })
            ->dispatch();

        return response()->json([
            'message' => 'Matrículas enviadas para atualização. O processo de demorar alguns minutos, aguarde a notificação antes de realizar a mesma atualização.',
            'status' => 'success',
        ]);
    }

    private function loadEnrollmentsByFilter(array $data): array
    {
        $sql = $this->getEnrollmentsByFilterSql();

        $dbInformation = DB::select($sql, $data);

        return array_map(fn ($item) => $item->cod_matricula, $dbInformation);
    }

    private function getEnrollmentsByFilterSql(): string
    {
        return 'SELECT m.cod_matricula FROM pmieducar.matricula AS m
                    INNER JOIN pmieducar.matricula_turma AS mt ON m.cod_matricula = mt.ref_cod_matricula
                    INNER JOIN pmieducar.serie as s on m.ref_ref_cod_serie = s.cod_serie
                    INNER JOIN modules.regra_avaliacao_serie_ano as ra on ra.serie_id = s.cod_serie and ra.ano_letivo = m.ano
                    INNER JOIN pmieducar.aluno ON aluno.cod_aluno = m.ref_cod_aluno
                 WHERE m.ano = :ano
                   AND m.ativo = 1
                   AND mt.ref_cod_matricula = m.cod_matricula
                   AND mt.ativo = 1
                   AND (CASE WHEN :matricula = 0  THEN true else :matricula = m.cod_matricula END)
                   AND (CASE WHEN :escolaId = 0  THEN true else :escolaId = m.ref_ref_cod_escola END)
                   AND (CASE WHEN :cursoId = 0  THEN true else :cursoId = m.ref_cod_curso END)
                   AND (CASE WHEN :serieId = 0  THEN true else :serieId = m.ref_ref_cod_serie END)
                   AND (CASE WHEN :turmaId = 0  THEN true else :turmaId = mt.ref_cod_turma END)
                   AND (CASE WHEN :matriculaSituacao = 10 THEN true
                             WHEN :matriculaSituacao = 9  THEN m.aprovado NOT IN (4,6) ELSE :turmaId = m.aprovado END)
                    AND (CASE WHEN :regraDeAvaliacao = 0  THEN true ELSE :regraDeAvaliacao = ra.regra_avaliacao_id END)
                ORDER BY ref_cod_matricula
                ';
    }

    private function getResquetData(Request $request): array
    {
        return [
            'ano' => (int) $request->input('ano'),
            'escolaId' => (int) $request->input('escola', 0),
            'cursoId' => (int) $request->input('curso', 0),
            'serieId' => (int) $request->input('serie', 0),
            'turmaId' => (int) $request->input('turma', 0),
            'matricula' => (int) $request->input('matricula', 0),
            'matriculaSituacao' => (int) $request->input('situacaoMatricula', 10),
            'regraDeAvaliacao' => (int) $request->input('regras_avaliacao_id', 0),
        ];
    }
}
