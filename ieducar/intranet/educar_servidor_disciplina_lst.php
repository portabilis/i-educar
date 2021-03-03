<?php

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
    public $cursos_disciplina;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_servidor = $_GET['ref_cod_servidor'];
        $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            635,
            $this->pessoa_logada,
            7,
            'educar_servidor_lst.php'
        );

        if (is_numeric($this->cod_servidor) && is_numeric($this->ref_cod_instituicao)) {
            $obj = new clsPmieducarServidor(
                $this->cod_servidor,
                null,
                null,
                null,
                null,
                null,
                null,
                $this->ref_cod_instituicao
            );

            $registro  = $obj->detalhe();
            if ($registro) {
                $retorno = 'Editar';
            }
        }

        $this->cursos_disciplina = Session::get('cursos_disciplina');

        if (!$this->cursos_disciplina) {
            $obj_servidor_disciplina = new clsPmieducarServidorDisciplina();
            $lst_servidor_disciplina = $obj_servidor_disciplina->lista(
                null,
                $this->ref_cod_instituicao,
                $this->cod_servidor
            );

            if ($lst_servidor_disciplina) {
                foreach ($lst_servidor_disciplina as $disciplina) {
                    $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();
                    $componente = $componenteMapper->find($disciplina['ref_cod_disciplina']);

                    $this->cursos_disciplina[$disciplina['ref_cod_curso']][$disciplina['ref_cod_disciplina']] = $disciplina['ref_cod_disciplina'];
                }
            }
        }

        if ($this->cursos_disciplina) {
            foreach ($this->cursos_disciplina as $curso => $disciplinas) {
                if ($disciplinas) {
                    foreach ($disciplinas as $disciplina) {
                        $this->ref_cod_curso[] = $curso;
                        $this->ref_cod_disciplina[] = $disciplina;
                    }
                }
            }
        }

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
        $opcoes = $opcoes_curso = ['' => 'Selecione'];

        $obj_cursos = new clsPmieducarCurso();
        $obj_cursos->setOrderby('nm_curso');
        $lst_cursos = $obj_cursos->lista(
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            null,
            $this->ref_cod_instituicao
        );

        if ($lst_cursos) {
            foreach ($lst_cursos as $curso) {
                $opcoes_curso[$curso['cod_curso']] = $curso['nm_curso'];
            }
        }

        $lst_opcoes = [];
        $arr_valores = [];

        if ($this->cursos_disciplina) {
            foreach ($this->cursos_disciplina as $curso => $disciplinas) {
                if ($disciplinas) {
                    foreach ($disciplinas as $disciplina) {
                        $arr_valores[] = [$curso, $disciplina];
                    }
                }
            }
        }

        if ($this->ref_cod_curso) {
            $cursosDifferente = array_unique($this->ref_cod_curso);
            foreach ($cursosDifferente as $curso) {
                $obj_componentes = new clsModulesComponenteCurricular;
                $componentes     = $obj_componentes->listaComponentesPorCurso($this->ref_cod_instituicao, $curso);
                $opcoes_disc = [];
                $opcoes_disc['todas_disciplinas']  = 'Todas as disciplinas';

                $total_componentes = count($componentes);
                for ($i=0; $i < $total_componentes; $i++) {
                    $opcoes_disc[$componentes[$i]['id']]  = $componentes[$i]['nome'];
                }
                $disciplinasCurso[$curso] = [$opcoes_curso, $opcoes_disc];
            }
            foreach ($this->ref_cod_curso as $curso) {
                $lst_opcoes[] = $disciplinasCurso[$curso];
            }
        }

        $this->campoTabelaInicio(
            'funcao',
            'Componentes Curriculares',
            ['Curso', 'Componente Curricular'],
            $arr_valores,
            '',
            $lst_opcoes
        );

        // Cursos
        $this->campoLista(
            'ref_cod_curso',
            'Curso',
            $opcoes_curso,
            $this->ref_cod_curso,
            'trocaCurso(this)',
            '',
            '',
            ''
        );

        // Disciplinas
        $this->campoLista(
            'ref_cod_disciplina',
            'Componente Curricular',
            $opcoes,
            $this->ref_cod_disciplina,
            '',
            '',
            '',
            ''
        );

        $this->campoTabelaFim();
    }

    public function Novo()
    {
        $cursos_disciplina = [];

        $curso_servidor = Session::get('cursos_servidor');

        if ($this->ref_cod_curso) {
            for ($i = 0, $loop = count($this->ref_cod_curso); $i < $loop; $i++) {
                if ($this->ref_cod_disciplina[$i] == 'todas_disciplinas') {
                    $componenteAnoDataMapper = new ComponenteCurricular_Model_AnoEscolarDataMapper();
                    $componentes = $componenteAnoDataMapper->findComponentePorCurso($this->ref_cod_curso[$i]);

                    foreach ($componentes as $componente) {
                        $curso = $this->ref_cod_curso[$i];
                        $curso_servidor[$curso] = $curso;
                        $disciplina = $componente->id;
                        $cursos_disciplina[$curso][$disciplina] = $disciplina;
                    }
                } else {
                    $curso = $this->ref_cod_curso[$i];
                    $curso_servidor[$curso] = $curso;
                    $disciplina = $this->ref_cod_disciplina[$i];
                    $cursos_disciplina[$curso][$disciplina] = $disciplina;
                }
            }
        }

        Session::put([
        'cursos_disciplina' => $cursos_disciplina,
        'cod_servidor' => $this->cod_servidor,
        'cursos_servidor' => $curso_servidor,
    ]);
        Session::save();
        Session::start();

        echo "<script>parent.fechaExpansivel('{$_GET['div']}');</script>";
        die;
    }

    public function Editar()
    {
        return $this->Novo();
    }

    public function Excluir()
    {
        return false;

    }

    public function Formular()
    {
        $this->titulo = "i-Educar - Servidor Disciplina";
        $this->processoAp         = 0;
        $this->renderMenu         = false;
        $this->renderMenuSuspenso = false;
    }
};
