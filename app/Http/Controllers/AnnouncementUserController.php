<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\LegacyEnrollment;
use App\Process;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnnouncementUserController extends Controller
{
    public function show(Request $request)
    {
        $this->breadcrumb('Avisos');
        $this->menu(Process::ANNOUNCEMENT);
        $announcement = Announcement::query()->latest()->first();
        $announcement->users()->sync([
            $request->user()->getKey() => ['read_at' => now()],
        ]);

        return view('announcement.user.show', [
            'announcement' => $announcement,
            'schools' => $this->getUserSchools($announcement->show_vacancy),
        ]);
    }

    private function getUserSchools(bool $show)
    {
        if (!$show) {
            return [];
        }

        return LegacyEnrollment::query()
            ->selectRaw("
                nm_turma,
                UPPER(pessoa.nome) as escola,
                string_agg(distinct nm_serie, ', ') as serie,
                string_agg(distinct nm_curso, ', ') as curso,
                max_aluno - COUNT(distinct matricula.cod_matricula) as vagas
            ")
            ->join('relatorio.view_situacao', function ($join) {
                $join->on('view_situacao.cod_matricula', 'ref_cod_matricula')
                    ->on('view_situacao.cod_turma', 'ref_cod_turma')
                    ->on('view_situacao.sequencial', 'matricula_turma.sequencial');
            })
            ->join('pmieducar.turma', fn ($join) => $join->on('turma.cod_turma', 'ref_cod_turma')->where('turma.ativo', 1)->where('turma.ano', Carbon::now()->year))
            ->join('pmieducar.escola', fn ($join) => $join->on('cod_escola', 'turma.ref_ref_cod_escola')->where('escola.ativo', 1))
            ->join('cadastro.pessoa', 'idpes', 'escola.ref_idpes')
            ->join('pmieducar.matricula', 'matricula.cod_matricula', 'ref_cod_matricula')
            ->join('pmieducar.serie', fn ($join) => $join->on('cod_serie', 'matricula.ref_ref_cod_serie')->where('matricula.ativo', 1))
            ->join('pmieducar.curso', fn ($join) => $join->on('cod_curso', 'matricula.ref_cod_curso')->where('curso.ativo', 1))
            ->when(Auth::user()->isSchooling(), fn ($q) => $q->whereIn('cod_escola', Auth::user()->schools()->pluck('ref_cod_escola')))
            ->having('max_aluno', '>', DB::raw('COUNT(distinct matricula.cod_matricula)'))
            ->orderBy('escola')
            ->orderBy('curso')
            ->orderBy('serie')
            ->where('matricula_turma.ativo', 1)
            ->orderBy('nm_turma')
            ->groupBy('nm_turma', 'max_aluno', 'pessoa.nome')
            ->get()
            ->groupBy([
                'escola',
                'curso',
                'serie',
            ]);
    }

    public function confirm(Request $request)
    {
        $announcement = Announcement::query()->latest()->first();
        $announcement->users()->sync([
            $request->user()->getKey() => ['confirmed_at' => now()],
        ]);

        return redirect('/');
    }
}
