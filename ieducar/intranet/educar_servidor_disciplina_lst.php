<?php

use Illuminate\Support\Facades\Session;

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Servidor Disciplina');
        $this->processoAp = 0;
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
}

class indice extends clsCadastro
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
        $this->ref_cod_funcao = $this->getQueryString('cod_funcao');

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

            $registro = $obj->detalhe();
            if ($registro) {
                $retorno = 'Editar';
            }
        }

        $this->cursos_disciplina = Session::get('cursos_disciplina');
        if (!empty($this->ref_cod_funcao)) {
            $this->cursos_disciplina = collect($this->cursos_disciplina)->filter(function ($disciplinas) {
                $result = collect($disciplinas)->filter(function ($funcao) {
                    return $funcao == $this->ref_cod_funcao;
                });

                return $result->count();
            })->toArray();
        }

        if (!empty($this->ref_cod_funcao) && (!$this->cursos_disciplina || !in_array($this->ref_cod_funcao, Session::get('cod_funcao', [])))) {
            $obj_servidor_disciplina = new clsPmieducarServidorDisciplina();
            $lst_servidor_disciplina = $obj_servidor_disciplina->lista(
                null,
                $this->ref_cod_instituicao,
                $this->cod_servidor,
                null,
                $this->ref_cod_funcao
            );

            if ($lst_servidor_disciplina) {
                foreach ($lst_servidor_disciplina as $disciplina) {
                    $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();
                    $componente = $componenteMapper->find($disciplina['ref_cod_disciplina']);

                    $this->cursos_disciplina[$disciplina['ref_cod_curso']][$disciplina['ref_cod_disciplina']] = $this->ref_cod_funcao;
                }
            }
        }

        if ($this->cursos_disciplina) {
            foreach ($this->cursos_disciplina as $curso => $disciplinas) {
                if ($disciplinas) {
                    foreach ($disciplinas as $disciplina => $funcao) {
                        if ($funcao != $this->ref_cod_funcao) {
                            continue;
                        }
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
                    foreach ($disciplinas as $disciplina => $funcao) {
                        if ($funcao != $this->ref_cod_funcao) {
                            continue;
                        }
                        $arr_valores[] = [$curso, $disciplina];
                    }
                }
            }
        }

        if ($this->ref_cod_curso) {
            $cursosDifferente = array_unique($this->ref_cod_curso);
            foreach ($cursosDifferente as $curso) {
                $obj_componentes = new clsModulesComponenteCurricular;
                $componentes = $obj_componentes->listaComponentesPorCurso($this->ref_cod_instituicao, $curso);
                $opcoes_disc = [];
                $opcoes_disc['todas_disciplinas'] = 'Todas as disciplinas';

                $total_componentes = count($componentes);
                for ($i = 0; $i < $total_componentes; $i++) {
                    $opcoes_disc[$componentes[$i]['id']] = $componentes[$i]['nome'];
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
        $curso_servidor = Session::get('cursos_servidor');
        $cursos_disciplina = Session::get('cursos_disciplina');

        if ($this->ref_cod_curso) {
            for ($i = 0, $loop = count($this->ref_cod_curso); $i < $loop; $i++) {
                if ($this->ref_cod_disciplina[$i] == 'todas_disciplinas') {
                    $componenteAnoDataMapper = new ComponenteCurricular_Model_AnoEscolarDataMapper();
                    $componentes = $componenteAnoDataMapper->findComponentePorCurso($this->ref_cod_curso[$i]);

                    foreach ($componentes as $componente) {
                        $curso = $this->ref_cod_curso[$i];
                        $curso_servidor[$curso] = $curso;
                        $disciplina = $componente->id;
                        $cursos_disciplina[$curso][$disciplina] = $this->getQueryString('cod_funcao');
                    }
                } else {
                    $curso = $this->ref_cod_curso[$i];
                    $curso_servidor[$curso] = $curso;
                    $disciplina = $this->ref_cod_disciplina[$i];
                    $cursos_disciplina[$curso][$disciplina] = $this->getQueryString('cod_funcao');
                }
            }
        }
        $funcoes = Session::get('cod_funcao', []);
        $funcoes[] = $this->getQueryString('cod_funcao');
        Session::put([
            'cursos_disciplina' => $cursos_disciplina,
            'cod_servidor' => $this->cod_servidor,
            'cursos_servidor' => $curso_servidor,
            'cod_funcao' => $funcoes
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
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
function trocaCurso(id_campo) {
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
    var campoCurso = document.getElementById(id_campo.id).value;
    var id = /[0-9]+/.exec(id_campo.id);
    var campoDisciplina = document.getElementById('ref_cod_disciplina[' + id + ']');
    campoDisciplina.length = 1;

    if (campoDisciplina) {
        campoDisciplina.disabled = true;
        campoDisciplina.options[0].text = 'Carregando Disciplinas';

        var xml = new ajax(atualizaLstDisciplina, 'ref_cod_disciplina[' + id + ']');
        xml.envia('educar_disciplina_xml.php?cur=' + campoCurso);
    } else {
        campoFuncao.options[0].text = 'Selecione';
    }
}

function atualizaLstDisciplina(xml) {
    var campoDisciplina = document.getElementById(arguments[1]);

    campoDisciplina.length = 1;
    campoDisciplina.options[0].text = 'Selecione uma Disciplina';
    campoDisciplina.disabled = false;

    var disciplinas = xml.getElementsByTagName('disciplina');

    if (disciplinas.length) {
        campoDisciplina.options[campoDisciplina.options.length] =
            new Option('Todas as disciplinas', 'todas_disciplinas', false, false);
        for (var i = 0; i < disciplinas.length; i++) {
            campoDisciplina.options[campoDisciplina.options.length] =
                new Option(disciplinas[i].firstChild.data, disciplinas[i].getAttribute('cod_disciplina'), false, false);
        }
    } else {
        campoDisciplina.options[0].text = 'A instituição não possui nenhuma disciplina';
    }
}

tab_add_1.afterAddRow = function () {
};

window.onload = function () {
};

function trocaTodasfuncoes() {
    for (var ct = 0; ct < tab_add_1.id; ct++) {
        getFuncao('ref_cod_funcao[' + ct + ']');
    }
}

function acao2() {
    var total_horas_alocadas = getArrayHora(document.getElementById('total_horas_alocadas').value);
    var carga_horaria = (document.getElementById('carga_horaria').value).replace(',', '.');

    if (parseFloat(total_horas_alocadas) > parseFloat(carga_horaria)) {
        alert('Atenção, carga horária deve ser maior que horas alocadas!');
        return false;
    } else {
        acao();
    }
}

if (document.getElementById('total_horas_alocadas')) {
    document.getElementById('total_horas_alocadas').style.textAlign = 'right';
}
</script>
