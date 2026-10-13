<?php

use App\Models\Builders\EmployeeBuilder;
use App\Models\Employee;
use App\Models\LegacyEmployee;
use App\Models\LegacyInstitution;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $cod_servidor;

    public $ref_idesco;

    public $ref_cod_funcao;

    public $carga_horaria;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_instituicao;

    public $nome_servidor;

    public $ref_cod_servidor;

    public $periodo;

    public $carga_horaria_usada;

    public function Gerar()
    {
        Session::put(key: [
            'campo1' => $_GET['campo1'] ?? Session::get(key: 'campo1'),
            'campo2' => $_GET['campo2'] ?? Session::get(key: 'campo2'),
            'ref_cod_instituicao' => $_GET['ref_cod_instituicao'] ? $_GET['ref_cod_instituicao'] : Session::get(key: 'ref_cod_instituicao'),
            'ref_cod_servidor' => $_GET['ref_cod_servidor'] ? $_GET['ref_cod_servidor'] : Session::get(key: 'ref_cod_servidor'),
        ]);

        if (!isset($_GET['tipo'])) {
            Session::forget(keys: [
                'setAllField1',
                'setAllField2',
                'tipo',
            ]);
        }

        $this->ref_cod_instituicao = Session::get(key: 'ref_cod_instituicao');
        $this->ref_cod_servidor = Session::get(key: 'ref_cod_servidor');

        Session::put(key: 'tipo', value: $_GET['tipo'] ?? Session::get(key: 'tipo') ?? '');

        $this->titulo = 'Servidores P&uacute;blicos - Listagem';
        // Passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = $val === '' ? null : $val;
        }
        $this->addCabecalhos(coluna: [
            'Nome do Servidor',
            'Matrícula',
            'Instituição',
        ]);
        $this->campoTexto(nome: 'nome_servidor', campo: 'Nome Servidor', valor: $this->nome_servidor, tamanhovisivel: 30, tamanhomaximo: 255);
        $this->campoOculto(nome: 'tipo', valor: $_GET['tipo']);
        // Paginador
        $this->limite = 20;
        $query = $this->consultaServidores();

        $resultado = $query
            ->orderBy('pessoa.nome')
            ->paginate(perPage: $this->limite, pageName: 'pagina_' . $this->nome);

        $lista = $resultado->getCollection();
        $total = $resultado->total();
        $nm_instituicao = null;
        $matriculasPorServidor = collect();

        if ($lista->isNotEmpty()) {
            $nm_instituicao = LegacyInstitution::query()
                ->whereKey($lista->first()->ref_cod_instituicao)
                ->value('nm_instituicao');

            $matriculasPorServidor = LegacyEmployee::query()
                ->whereIn('ref_cod_pessoa_fj', $lista->pluck('cod_servidor'))
                ->pluck('matricula', 'ref_cod_pessoa_fj');
        }

        foreach ($lista as $registro) {
            $registro['matricula'] = $matriculasPorServidor[$registro['cod_servidor']] ?? null;
            $campo1 = Session::get(key: 'campo1');
            $campo2 = Session::get(key: 'campo2');
            if (Session::get(key: 'tipo')) {
                if (is_string(value: $campo1) && is_string(value: $campo2)) {
                    $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}', '{$registro['nome']}'); addVal1('{$campo2}','{$registro['nome']}', '{$registro['cod_servidor']}'); fecha();\"";
                } elseif (is_string(value: $campo1)) {
                    $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}','{$registro['nome']}'); fecha();\"";
                }
            } else {
                if (is_string(value: $campo1) && is_string(value: $campo2)) {
                    $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}','{$registro['nome']}'); addVal1('{$campo2}','{$registro['nome']}','{$registro['cod_servidor']}'); fecha();\"";
                } elseif (is_string(value: $campo2)) {
                    $script = " onclick=\"addVal1('{$campo2}','{$registro['nome']}','{$registro['cod_servidor']}'); fecha();\"";
                } elseif (is_string(value: $campo1)) {
                    $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}','{$registro['nome']}'); fecha();\"";
                }
            }
            $this->addLinhas(linha: [
                "<a href=\"javascript:void(0);\" $script>{$registro['nome']}</a>",
                "<a href=\"javascript:void(0);\" $script>{$registro['matricula']}</a>",
                "<a href=\"javascript:void(0);\" $script>{$nm_instituicao}</a>",
            ]);
        }
        $this->addPaginador2(
            strUrl: 'educar_pesquisa_servidor_lst.php',
            intTotalRegistros: $total,
            mixVariaveisMantidas: $_GET,
            nome: $this->nome,
            intResultadosPorPagina: $this->limite
        );
        $this->largura = '100%';
    }

    private function consultaServidores(): EmployeeBuilder
    {
        $instituicao = $this->ref_cod_instituicao;

        $query = Employee::query()
            ->select(['servidor.cod_servidor', 'pessoa.nome', 'servidor.ref_cod_instituicao'])
            ->leftJoin('cadastro.pessoa', 'pessoa.idpes', 'servidor.cod_servidor')
            ->when(is_numeric(value: $this->ref_idesco), fn ($q) => $q->whereSchoolingDegree($this->ref_idesco))
            ->when(is_numeric(value: $this->carga_horaria), fn ($q) => $q->where('carga_horaria', $this->carga_horaria))
            ->when(is_numeric(value: $instituicao), fn ($q) => $q->whereInstitution($instituicao))
            ->when(is_string(value: $this->nome_servidor), fn ($q) => $q->whereName($this->nome_servidor))
            ->whereHas('employeeAllocations', function ($q) use ($instituicao) {
                $q->when(is_numeric(value: $instituicao), fn ($q) => $q->whereInstitution($instituicao));
                $q->active();
            })
            ->active();

        if ($this->ref_cod_servidor) {
            $query->whereNotIn('servidor.cod_servidor', $this->codigosServidores($this->ref_cod_servidor));
        }

        $query->whereHas('employeeRoles', function ($q) {
            $q->whereColumn('servidor_funcao.ref_ref_cod_instituicao', 'servidor.ref_cod_instituicao');
            $q->whereTeacherRole();
        });

        $query->whereExists(function ($q) {
            $this->consultaDisciplinas($q);
        });

        return $query;
    }

    private function consultaDisciplinas(QueryBuilder $query): void
    {
        $query->selectRaw('1')
            ->from('pmieducar.servidor_disciplina')
            ->whereColumn('servidor_disciplina.ref_cod_servidor', 'servidor.cod_servidor')
            ->whereColumn('servidor_disciplina.ref_ref_cod_instituicao', 'servidor.ref_cod_instituicao');

        $disciplinas = DB::table('pmieducar.servidor_disciplina')
            ->when(is_numeric(value: $this->ref_cod_servidor), fn ($q) => $q->where('ref_cod_servidor', $this->ref_cod_servidor))
            ->pluck('ref_cod_disciplina');

        if ($disciplinas->isEmpty()) {
            return;
        }

        $query->groupBy('servidor_disciplina.ref_cod_servidor')
            ->havingRaw('?::int[] <@ array_agg(servidor_disciplina.ref_cod_disciplina)', ['{' . $disciplinas->implode(',') . '}']);
    }

    private function codigosServidores($lista): array
    {
        if (!is_string(value: $lista) && !is_numeric(value: $lista)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($codigo) => (int) trim(string: $codigo),
            explode(separator: ',', string: (string) $lista)
        )));
    }

    public function makeExtra()
    {
        return file_get_contents(filename: __DIR__ . '/scripts/extra/educar-pesquisa-servidor-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Servidor';
        $this->processoAp = '0';
        $this->renderMenu = true;
        $this->renderMenuSuspenso = false;
    }
};
