<?php

namespace App\Http\Controllers;

use App\Models\Individual;
use App\Models\LogUnification;
use Illuminate\Http\Request;

class PersonLogUnificationController extends Controller
{
    public function index(Request $request)
    {
        $this->breadcrumb('Log de unificações de pessoa', [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);

        $this->menu(9998878);

        $unificationsQuery = LogUnification::query()->with('main')
            ->where('type', Individual::class)
            ->when($request->get('name'), function ($query, $name) {

                $query->whereHas('personMain', function ($personQuery) use ($name) {
                    $personQuery->where('slug', 'ilike', '%' . $name . '%');
                })->orWhere('duplicates_name', 'ilike', '%' . $name . '%');

            })->when($request->get('cpf'), function ($query, $cpf) {

                $query->whereHas('personMain', function ($personQuery) use ($cpf) {
                    $personQuery->whereHas('individual', function ($individualQuery) use ($cpf) {
                        $individualQuery->where('cpf', str_replace(['.','-'], '', $cpf));
                    });
                });

            });

        return view('unification.person.index', ['unifications' => $unificationsQuery->paginate(20)]);
    }

    public function show(LogUnification $unification)
    {
        $this->breadcrumb('Detalhe da unificação', [
            url('intranet/educar_index.php') => 'Escola',
            route('person-log-unification.index') => 'Log de unificações de aluno',
        ]);

        $this->menu(999847);

        return view('unification.person.show', ['unification' => $unification]);
    }
}
