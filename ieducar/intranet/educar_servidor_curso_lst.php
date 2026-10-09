<?php

use App\Models\Employee;
use App\Models\LegacyCourse;
use Illuminate\Support\Facades\Session;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $cod_servidor;

    public $ref_cod_instituicao;

    public $ref_idesco;

    public $ref_cod_funcao;

    public $carga_horaria;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_curso;

    public $ref_cod_disciplina;

    public $cursos_servidor;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_servidor = $_GET['ref_cod_servidor'];
        $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];
        $this->ref_cod_servidor_funcao = $_GET['ref_cod_servidor_funcao'] ?: 0;

        $obj_permissoes = new clsPermissoes;

        $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, 'educar_servidor_lst.php');

        if (is_numeric($this->cod_servidor) && is_numeric($this->ref_cod_instituicao)) {
            $servidorExiste = Employee::query()
                ->whereEmployee($this->cod_servidor)
                ->whereInstitution($this->ref_cod_instituicao)
                ->exists();

            if ($servidorExiste) {
                $retorno = 'Editar';
            }
        }

        $funcoes = Session::get("servant:{$this->cod_servidor}", []);
        $funcoes = $funcoes[$this->ref_cod_servidor_funcao] ?? [];
        $this->cursos_servidor = array_keys($funcoes);

        if (!$this->cursos_servidor) {
            $this->cursos_servidor = Session::get('cursos_por_funcao')[$this->ref_cod_servidor_funcao]['cursos_servidor'];
        }

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
        $this->campoOculto('ref_cod_servidor_funcao', $this->ref_cod_servidor_funcao);

        $cursos = LegacyCourse::query()
            ->active()
            ->when(is_numeric($this->ref_cod_instituicao), fn ($query) => $query->whereInstitution((int) $this->ref_cod_instituicao))
            ->orderByName()
            ->pluck('nm_curso', 'cod_curso');

        $opcoes_curso = ['' => 'Selecione'] + $cursos->all();

        $arr_valores = [];

        if ($this->cursos_servidor) {
            foreach ($this->cursos_servidor as $curso) {
                $arr_valores[] = [$curso];
            }
        }

        $this->campoTabelaInicio(
            'cursos_ministra',
            'Cursos Ministrados',
            ['Curso'],
            $arr_valores,
            ''
        );

        $this->campoLista(
            'ref_cod_curso',
            'Curso',
            $opcoes_curso,
            $this->ref_cod_curso,
            '',
            '',
            '',
            ''
        );

        $this->campoTabelaFim();
    }

    public function Novo()
    {
        // Não testável: o caminho termina em `exit`, que encerraria a suíte de testes.
        // @codeCoverageIgnoreStart
        $curso_servidor = [];
        if ($this->ref_cod_curso) {
            foreach ($this->ref_cod_curso as $curso) {
                $curso_servidor[$curso] = $curso;
            }
        }

        Session::put([
            'cursos_por_funcao' => [
                $this->ref_cod_servidor_funcao => [
                    'cursos_servidor' => $curso_servidor,
                ],
            ],
            'cod_servidor' => $this->cod_servidor,
        ]);
        Session::save();
        Session::start();

        echo '<script>parent.fechaExpansivel(' . json_encode((string) request('div'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . ');</script>';
        exit();
        // @codeCoverageIgnoreEnd
    }

    public function Editar()
    {
        // Não testável: o caminho termina em `exit`, que encerraria a suíte de testes.
        // @codeCoverageIgnoreStart
        $this->Novo();
        // @codeCoverageIgnoreEnd
    }

    public function Excluir()
    {
        return false;
    }

    public function Formular()
    {
        $this->title = 'Servidor Curso';
        $this->processoAp = 0;
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
