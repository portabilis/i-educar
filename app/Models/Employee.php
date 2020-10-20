<?php

namespace App\Models;

use App\Functions;
use App\ServerCourseMinister;
use App\ServerFunction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Employee extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.servidor';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_servidor';

    /**
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'cod_servidor',
        'ref_cod_instituicao',
        'data_cadastro',
        'carga_horaria',
    ];

    /**
     * @return BelongsTo
     */
    public function inep()
    {
        return $this->belongsTo(EmployeeInep::class, 'cod_servidor', 'cod_servidor');
    }

    /**
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->cod_servidor;
    }

    /**
     * @return BelongsToMany
     */
    public function schools()
    {
        return $this->belongsToMany(
            LegacySchool::class,
            'pmieducar.servidor_alocacao',
            'ref_cod_servidor',
            'ref_cod_escola'
        )->withPivot('ref_ref_cod_instituicao', 'ano')
            ->where('servidor_alocacao.ativo', 1);
    }

    /**
     * @return BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(LegacyPerson::class, 'cod_servidor');
    }

    /**
     * @return BelongsTo
     */
    public function schoolingDegree()
    {
        return $this->belongsTo(LegacySchoolingDegree::class, 'ref_idesco');
    }

    public function graduations()
    {
        return $this->hasMany(EmployeeGraduation::class, 'employee_id');
    }

    /**
     * @return BelongsToMany
     */
    public function disciplines()
    {
        return $this->belongsToMany(
            LegacyDiscipline::class,
            'pmieducar.servidor_disciplina',
            'ref_cod_servidor',
            'ref_cod_disciplina'
        )->withPivot('ref_ref_cod_instituicao', 'ref_cod_curso');
    }

    public function serverFunction($cod_servidor)
    {
        $users = DB::table('pmieducar.servidor_funcao')
                        ->select(DB::raw('nm_funcao, pmieducar.servidor_funcao.matricula, nm_curso, array_agg(nome) as nome, professor'))
                        ->join('pmieducar.servidor_curso_ministra', 'pmieducar.servidor_funcao.ref_cod_servidor', '=', 'pmieducar.servidor_curso_ministra.ref_cod_servidor')
                        ->join('pmieducar.curso', 'pmieducar.curso.cod_curso', '=', 'pmieducar.servidor_curso_ministra.ref_cod_curso')
                        ->join('pmieducar.funcao', 'pmieducar.funcao.cod_funcao', '=', 'pmieducar.servidor_funcao.ref_cod_funcao')
                        ->join('pmieducar.servidor_disciplina', 'pmieducar.servidor_disciplina.ref_cod_servidor', '=', 'pmieducar.servidor_funcao.ref_cod_servidor')
                        ->join('modules.componente_curricular', 'modules.componente_curricular.id', '=', 'pmieducar.servidor_disciplina.ref_cod_disciplina')
                        ->where([['pmieducar.servidor_funcao.ref_cod_servidor', '=',  $cod_servidor]])
                        ->groupBy('professor', 'nm_funcao', 'pmieducar.servidor_funcao.matricula', 'nm_curso')
                        ->get();

        return $users;
    }

}
