<?php

namespace App\Http\Controllers;

use App\Process;
use App\Services\PendingTransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendingTransferController extends Controller
{
    public function index(Request $request, PendingTransferService $service): JsonResponse
    {
        $query = $service->forUser($request->user());
        $page = $query->with([
            'oldRegistration.student.person',
            'oldRegistration.student.individual',
            'oldRegistration.school.person',
            'oldRegistration.grade',
            'destinationSchool.person',
        ])->orderBy('data_transferencia')->orderBy('cod_transferencia_solicitacao')
            ->paginate(20);
        $transfers = $page->getCollection()->map(function ($transfer) {
            $registration = $transfer->oldRegistration;

            return [
                'id' => $transfer->getKey(),
                'student' => $registration->student->person->name,
                'birth_date' => $registration->student->individual?->data_nasc?->format('d/m/Y'),
                'origin_school' => $registration->school->name,
                'destination_school' => $transfer->destinationSchool->name,
                'grade' => $registration->grade?->nm_serie,
                'transfer_date' => $transfer->data_transferencia?->format('d/m/Y'),
                'confirm_url' => route('transfers.pending.confirm', $transfer->getKey()),
            ];
        });

        return response()->json([
            'total' => $page->total(),
            'transfers' => $transfers,
            'next_url' => $page->nextPageUrl(),
            'previous_url' => $page->previousPageUrl(),
        ]);
    }

    public function confirm(Request $request, int $transfer, PendingTransferService $service): RedirectResponse
    {
        $this->authorize('modify', Process::REGISTRATIONS);

        $pending = DB::transaction(function () use ($request, $transfer, $service) {
            $pending = $service->forUser($request->user())->lockForUpdate()->findOrFail($transfer);

            if (!$pending->data_recebimento) {
                $pending->update([
                    'data_recebimento' => now(),
                    'ref_usuario_recebimento' => $request->user()->getKey(),
                ]);
            }

            return $pending;
        });

        return redirect()->to(url('intranet/educar_matricula_cad.php') . '?' . http_build_query([
            'ref_cod_aluno' => $pending->oldRegistration->ref_cod_aluno,
            'ano' => $pending->oldRegistration->ano,
            'transferencia_id' => $pending->getKey(),
        ]));
    }
}
