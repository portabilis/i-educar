<?php

$pessoa_logada = \Illuminate\Support\Facades\Auth::id();

if (!isset($exibe_campo_lista_curso_escola)) {
    $exibe_campo_lista_curso_escola = true;
}

if ($obrigatorio) {
    $instituicao_obrigatorio = $escola_obrigatorio = $curso_obrigatorio = $escola_curso_obrigatorio = $escola_curso_serie_obrigatorio = $serie_obrigatorio = $biblioteca_obrigatorio = $cliente_tipo_obrigatorio = $funcao_obrigatorio = $turma_obrigatorio = true;
} else {
    $instituicao_obrigatorio = isset($instituicao_obrigatorio) ? $instituicao_obrigatorio : false;
    $escola_obrigatorio = isset($escola_obrigatorio) ? $escola_obrigatorio : false;
    $curso_obrigatorio = isset($curso_obrigatorio) ? $curso_obrigatorio : false;
    $escola_curso_obrigatorio = isset($escola_curso_obrigatorio) ? $escola_curso_obrigatorio : false;
    $escola_curso_serie_obrigatorio = isset($escola_curso_serie_obrigatorio) ? $escola_curso_serie_obrigatorio : false;
    $serie_obrigatorio = isset($serie_obrigatorio) ? $serie_obrigatorio : false;
    $biblioteca_obrigatorio = isset($biblioteca_obrigatorio) ? $biblioteca_obrigatorio : false;
    $cliente_tipo_obrigatorio = isset($cliente_tipo_obrigatorio) ? $cliente_tipo_obrigatorio : false;
    $funcao_obrigatorio = isset($funcao_obrigatorio) ? $funcao_obrigatorio : false;
    $turma_obrigatorio = isset($turma_obrigatorio) ? $turma_obrigatorio : false;
}

if ($desabilitado) {
    $instituicao_desabilitado = $escola_desabilitado = $curso_desabilitado = $escola_curso_desabilitado = $escola_curso_serie_desabilitado = $serie_desabilitado = $biblioteca_desabilitado = $cliente_tipo_desabilitado = $turma_desabilitado = true;
} else {
    $instituicao_desabilitado = isset($instituicao_desabilitado) ? $instituicao_desabilitado : false;
    $escola_desabilitado = isset($escola_desabilitado) ? $escola_desabilitado : false;
    $curso_desabilitado = isset($curso_desabilitado) ? $curso_desabilitado : false;
    $escola_curso_desabilitado = isset($escola_curso_desabilitado) ? $escola_curso_desabilitado : false;
    $escola_curso_serie_desabilitado = isset($escola_curso_serie_desabilitado) ? $escola_curso_serie_desabilitado : false;
    $serie_desabilitado = isset($serie_desabilitado) ? $serie_desabilitado : false;
    $biblioteca_desabilitado = isset($biblioteca_desabilitado) ? $biblioteca_desabilitado : false;
    $cliente_tipo_desabilitado = isset($cliente_tipo_desabilitado) ? $cliente_tipo_desabilitado : false;
    $funcao_desabilitado = isset($funcao_desabilitado) ? $funcao_desabilitado : false;
    $turma_desabilitado = isset($turma_desabilitado) ? $turma_desabilitado : false;
}

$obj_permissoes = new clsPermissoes();
$nivel_usuario = $obj_permissoes->nivel_acesso($pessoa_logada);

//Se administrador
if ($nivel_usuario == 1 || $cad_usuario) {
    $opcoes = ['' => 'Selecione'];
    $obj_instituicao = new clsPmieducarInstituicao();
    $obj_instituicao->setCamposLista('cod_instituicao, nm_instituicao');
    $obj_instituicao->setOrderby('nm_instituicao ASC');
    $lista = $obj_instituicao->lista(null, null, null, null, null, null, null, null, null, null, null, null, null, 1);
    if (is_array($lista) && count($lista)) {
        foreach ($lista as $registro) {
            $opcoes["{$registro['cod_instituicao']}"] = "{$registro['nm_instituicao']}";
        }
    }

    if ($get_escola && $get_biblioteca) {
        $this->campoLista('ref_cod_instituicao', 'Institui&ccedil;&atilde;o', $opcoes, $this->ref_cod_instituicao, 'getDuploEscolaBiblioteca();', null, null, null, $instituicao_desabilitado, $instituicao_obrigatorio);
    } elseif ($get_escola && $get_curso && $get_matricula) {
        $this->campoLista('ref_cod_instituicao', 'Institui&ccedil;&atilde;o', $opcoes, $this->ref_cod_instituicao, 'getMatricula();', null, null, null, $instituicao_desabilitado, $instituicao_obrigatorio);
    } elseif ($get_escola && $get_curso) {
        $this->campoLista('ref_cod_instituicao', 'Institui&ccedil;&atilde;o', $opcoes, $this->ref_cod_instituicao, 'getDuploEscolaCurso();', null, null, null, $instituicao_desabilitado, $instituicao_obrigatorio);
    } elseif ($get_escola) {
        $this->campoLista('ref_cod_instituicao', 'Institui&ccedil;&atilde;o', $opcoes, $this->ref_cod_instituicao, 'getEscola();', null, null, null, $instituicao_desabilitado, $instituicao_obrigatorio);
    } elseif ($get_curso) {
        $this->campoLista('ref_cod_instituicao', 'Institui&ccedil;&atilde;o', $opcoes, $this->ref_cod_instituicao, 'getCurso();', null, null, null, $instituicao_desabilitado, $instituicao_obrigatorio);
    } elseif ($get_biblioteca) {
        $this->campoLista('ref_cod_instituicao', 'Institui&ccedil;&atilde;o', $opcoes, $this->ref_cod_instituicao, 'getBiblioteca(1);', null, null, null, $instituicao_desabilitado, $instituicao_obrigatorio);
    } elseif ($get_cliente_tipo) {
        $this->campoLista('ref_cod_cliente_tipo', 'Tipo de Cliente', $opcoes, $this->ref_cod_cliente_tipo, 'getCliente();', null, null, null, $cliente_tipo_desabilitado, $cliente_tipo_obrigatorio);
    } else {
        $this->campoLista('ref_cod_instituicao', 'Institui&ccedil;&atilde;o', $opcoes, $this->ref_cod_instituicao, '', null, null, null, $instituicao_desabilitado, $instituicao_obrigatorio);
    }
} //se nao eh administrador
elseif ($nivel_usuario != 1) {
    $obj_usuario = new clsPmieducarUsuario($pessoa_logada);
    $det_usuario = $obj_usuario->detalhe();
    $this->ref_cod_instituicao = $det_usuario['ref_cod_instituicao'];
    $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
    //se eh institucional - admin
    if ($nivel_usuario == 4 || $nivel_usuario == 8) {
        $obj_usuario = new clsPmieducarUsuario($pessoa_logada);
        $det_usuario = $obj_usuario->detalhe();
        $this->ref_cod_escola = $det_usuario['ref_cod_escola'];
        $this->campoOculto('ref_cod_escola', $this->ref_cod_escola);
        if ($exibe_nm_escola == true) {
            $obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
            $det_escola = $obj_escola->detalhe();
            $nm_escola = $det_escola['nome'];
            $this->campoRotulo('nm_escola', 'Escola', $nm_escola);
        }
        if ($get_biblioteca) {
            $obj_per = new clsPermissoes();
            $ref_cod_biblioteca_ = $obj_per->getBiblioteca($pessoa_logada);
        }
    }
}
//                    administrador          institucional - CPD
if ($get_escola && ($nivel_usuario == 1 || $nivel_usuario == 2 || $cad_usuario)) {
    $this->inputsHelper()->dynamic(['escola'], [
        'required' => $escola_obrigatorio,
        'disabled' => $escola_desabilitado,
    ]);
}
if ($get_curso) {
    $opcoes_curso = ['' => 'Selecione'];

    // EDITAR
    if ($this->ref_cod_escola) {
        $obj_escola_curso = new clsPmieducarEscolaCurso();

        $lst_escola_curso = $obj_escola_curso->lista($this->ref_cod_escola, null, null, null, null, null, null, null, 1);

        if (is_array($lst_escola_curso) && count($lst_escola_curso)) {
            foreach ($lst_escola_curso as $escola_curso) {
                $opcoes_curso["{$escola_curso['ref_cod_curso']}"] = $escola_curso['nm_curso'];
            }
        }
    } elseif ($this->ref_cod_instituicao) {
        $opcoes_curso = ['' => 'Selecione'];
        $obj_curso = new clsPmieducarCurso();
        $obj_curso->setOrderby('nm_curso ASC');

        if ($sem_padrao) {
            $lista = $obj_curso->lista(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 1, null, $this->ref_cod_instituicao, 0);
        } else {
            $lista = $obj_curso->lista(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 1, null, $this->ref_cod_instituicao);
        }

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes_curso["{$registro['cod_curso']}"] = "{$registro['nm_curso']}";
            }
        }
    }
    $this->campoLista('ref_cod_curso', 'Curso', $opcoes_curso, $this->ref_cod_curso, null, null, null, null, $curso_desabilitado, $curso_obrigatorio);

    if ($get_semestre) {
        $this->campoRotulo('semestres', 'Semestre', '<div id=\'div_semestre\'>Selecione um Curso</div>');
        $this->campoOculto('is_padrao', 1);
    }
}

if ($get_escola_curso_serie) {
    $opcoes_series_curso_escola = ['' => 'Selecione'];
    // EDITAR
    if ($this->ref_cod_escola && $this->ref_cod_curso) {
        $obj_escola_serie = new clsPmieducarEscolaSerie();
        $obj_escola_serie->setOrderby('nm_serie ASC');
        $lst_escola_serie = $obj_escola_serie->lista($this->ref_cod_escola, null, null, null, null, null, null, null, null, null, null, null, 1, null, null, null, null, null, $this->ref_cod_curso);
        if (is_array($lst_escola_serie) && count($lst_escola_serie)) {
            foreach ($lst_escola_serie as $escola_curso_serie) {
                $opcoes_series_curso_escola["{$escola_curso_serie['ref_cod_serie']}"] = $escola_curso_serie['nm_serie'];
            }
        }
    }
    $this->campoLista('ref_ref_cod_serie', 'S&eacute;rie', $opcoes_series_curso_escola, $this->ref_ref_cod_serie, null, null, null, null, $escola_curso_serie_desabilitado, $escola_curso_serie_obrigatorio);
}

if ($get_serie) {
    $opcoes_serie = ['' => 'Selecione'];
    // EDITAR
    if ($this->ref_cod_curso) {
        $obj_serie = new clsPmieducarSerie();
        $obj_serie->setOrderby('nm_serie ASC');
        $lst_serie = $obj_serie->lista(null, null, null, $this->ref_cod_curso, null, null, null, null, null, null, null, null, 1);
        if (is_array($lst_serie) && count($lst_serie)) {
            foreach ($lst_serie as $serie) {
                $opcoes_serie["{$serie['cod_serie']}"] = $serie['nm_serie'];
            }
        }
    }
    $this->campoLista('ref_cod_serie', 'Série', $opcoes_serie, $this->ref_cod_serie, null, null, null, null, $serie_desabilitado, $serie_obrigatorio);
}

if ($get_biblioteca) {
    if ($ref_cod_biblioteca_ == 0 && $nivel_usuario != 1 && $nivel_usuario != 2) {
        $this->campoOculto('ref_cod_biblioteca', $this->ref_cod_biblioteca);
    } else {
        $qtd_bibliotecas = count($ref_cod_biblioteca_);
        if ($qtd_bibliotecas == 1 && ($nivel_usuario == 4 || $nivel_usuario == 8)) {
            $det_unica_biblioteca = array_shift($ref_cod_biblioteca_);
            $this->ref_cod_biblioteca = $det_unica_biblioteca['ref_cod_biblioteca'];
            $this->campoOculto('ref_cod_biblioteca', $this->ref_cod_biblioteca);
        } elseif ($qtd_bibliotecas > 1) {
            $opcoes_biblioteca = ['' => 'Selecione'];
            if (is_array($ref_cod_biblioteca_) && count($ref_cod_biblioteca_)) {
                foreach ($ref_cod_biblioteca_ as $biblioteca) {
                    $obj_biblioteca = new clsPmieducarBiblioteca($biblioteca['ref_cod_biblioteca']);
                    $det_biblioteca = $obj_biblioteca->detalhe();
                    $opcoes_biblioteca["{$biblioteca['ref_cod_biblioteca']}"] = "{$det_biblioteca['nm_biblioteca']}";
                }
            }
            $getCliente = '';
            if ($get_cliente_tipo) {
                $getCliente = 'getClienteTipo()';
            }
            $this->campoLista('ref_cod_biblioteca', 'Biblioteca', $opcoes_biblioteca, $this->ref_cod_biblioteca, $getCliente, null, null, null, $biblioteca_desabilitado, $biblioteca_obrigatorio);
        } else {
            $opcoes_biblioteca = ['' => 'Selecione'];
            // EDITAR
            if ($this->ref_cod_escola || $this->ref_cod_instituicao) {
                $objTemp = new clsPmieducarBiblioteca();
                $objTemp->setOrderby('nm_biblioteca ASC');
                $lista = $objTemp->lista(null, $this->ref_cod_instituicao, null, null, null, null, null, null, null, null, null, null, 1);

                if (is_array($lista) && count($lista)) {
                    foreach ($lista as $registro) {
                        $opcoes_biblioteca["{$registro['cod_biblioteca']}"] = "{$registro['nm_biblioteca']}";
                    }
                }
            }
            $getCliente = '';
            if ($get_cliente_tipo) {
                $getCliente = 'getClienteTipo()';
            }
            $this->campoLista('ref_cod_biblioteca', 'Biblioteca', $opcoes_biblioteca, $this->ref_cod_biblioteca, $getCliente, null, null, null, $biblioteca_desabilitado, $biblioteca_obrigatorio);
        }
    }
}

if ($get_cliente_tipo) {
    $opcoes_cli_tpo = ['' => 'Selecione'];
    if ($this->ref_cod_biblioteca) {
        $obj_cli_tpo = new clsPmieducarClienteTipo();
        $obj_cli_tpo->setOrderby('nm_tipo ASC');
        $lst_cli_tpo = $obj_cli_tpo->lista(null, $this->ref_cod_biblioteca, null, null, null, null, null, null, null, null, 1);
        if (is_array($lst_cli_tpo) && count($lst_cli_tpo)) {
            foreach ($lst_cli_tpo as $cli_tpo) {
                $opcoes_cli_tpo["{$cli_tpo['cod_cliente_tipo']}"] = "{$cli_tpo['nm_tipo']}";
            }
        }
    }
    $this->campoLista('ref_cod_cliente_tipo', 'Tipo do Cliente', $opcoes_cli_tpo, $this->ref_cod_cliente_tipo, null, null, null, null, $cliente_tipo_desabilitado, $cliente_tipo_obrigatorio);
}
if ($get_funcao) {
    $opcoes_funcao = ['' => 'Selecione'];
    if ($this->ref_cod_instituicao) {
        $obj_funcao = new clsPmieducarFuncao();
        $obj_funcao->setOrderby('nm_funcao ASC');
        $lst_funcao = $obj_funcao->lista(null, null, null, null, null, null, null, null, null, null, 1, $this->ref_cod_instituicao);
        if (is_array($lst_funcao) && count($lst_funcao)) {
            foreach ($lst_funcao as $funcao) {
                $opcoes_funcao["{$funcao['cod_funcao']}"] = "{$funcao['nm_funcao']}";
            }
        }
    }
    $this->campoLista('ref_cod_funcao', 'Função', $opcoes_funcao, $this->ref_cod_funcao, null, null, null, null, $funcao_desabilitado, $funcao_obrigatorio);
}
if ($get_turma) {
    $opcoes_turma = ['' => 'Selecione'];
    // EDITAR
    if (($this->ref_ref_cod_serie && $this->ref_cod_escola) || $this->ref_cod_curso) {
        $obj_turma = new clsPmieducarTurma();
        $obj_turma->setOrderby('nm_turma ASC');
        $lst_turma = $obj_turma->lista(null, null, null, $this->ref_ref_cod_serie, $this->ref_cod_escola, null, null, null, null, null, null, null, null, null, 1, null, null, null, null, null, null, null, null, null, $this->ref_cod_curso);
        if (is_array($lst_turma) && count($lst_turma)) {
            foreach ($lst_turma as $turma) {
                $opcoes_turma["{$turma['cod_turma']}"] = "{$turma['nm_turma']}";
            }
        }
    }
    $this->campoLista('ref_cod_turma', 'Turma', $opcoes_turma, $this->ref_cod_turma, null, null, null, null, $turma_desabilitado, $turma_obrigatorio);
}
if ($get_ano) {
    $lst_anos = Portabilis_Utils_Database::fetchPreparedQuery('SELECT distinct ano from pmieducar.turma where ativo = 1 order by ano desc');
    if (is_array($lst_anos) && count($lst_anos)) {
        foreach ($lst_anos as $ano) {
            if ($ano['ano']) {
                $opcoes_ano["{$ano['ano']}"] = "{$ano['ano']}";
            }
        }
    } else {
        $opcoes_ano = ['' => 'Indisponível'];
    }
    $this->campoLista('ano', 'Ano', $opcoes_ano, $this->ano, null, null, null, null, false, false);
}
if (isset($get_cabecalho)) {
    if ($qtd_bibliotecas > 1 && ($nivel_usuario == 4 || $nivel_usuario == 8)) {
        ${$get_cabecalho}[] = 'Biblioteca';
    } elseif ($nivel_usuario == 1 || $nivel_usuario == 2 || $nivel_usuario == 4) {
        ${$get_cabecalho}[] = 'Biblioteca';
    }
    if ($nivel_usuario == 1 || $nivel_usuario == 2) {
        ${$get_cabecalho}[] = 'Escola';
    }
    if ($nivel_usuario == 1) {
        ${$get_cabecalho}[] = 'Institui&ccedil;&atilde;o';
    }
}
?>
<script type='text/javascript'>
<?php
if ($nivel_usuario == 1 || $nivel_usuario == 2 || $cad_usuario) {
    ?>
var before_getEscola;
var after_getEscola;

function getEscola() {
    if (typeof before_getEscola == 'function') {
        before_getEscola();
    }

    limpaCampos(2);
}
<?php
if ($get_escola && $get_biblioteca) {
        ?>
function getDuploEscolaBiblioteca() {
    getEscola();
    getBiblioteca(1);
}
<?php
    }
}
if ($get_curso && $sem_padrao && !$get_matricula) {
    ?>
function getCurso() {
    var campoCurso = document.getElementById('ref_cod_curso');
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
    campoCurso.length = 1;

    limpaCampos(3);
    if (campoInstituicao) {
        campoCurso.disabled = true;
        campoCurso.options[0].text = 'Carregando cursos';

        var xml = new ajax(atualizaLstCurso);
        xml.envia('educar_curso_xml.php?ins=' + campoInstituicao + '&sem=true');
    } else {
        campoCurso.options[0].text = 'Selecione';
    }
}

function atualizaLstCurso(xml) {
    var campoCurso = document.getElementById('ref_cod_curso');
    campoCurso.length = 1;
    campoCurso.options[0].text = 'Selecione um curso';
    campoCurso.disabled = false;

    var cursos = xml.getElementsByTagName('curso');
    if (cursos.length) {
        for (var i = 0; i < cursos.length; i++) {
            campoCurso.options[campoCurso.options.length] = new Option(cursos[i].firstChild.data, cursos[i].getAttribute('cod_curso'), false, false);
        }
    } else {
        campoCurso.options[0].text = 'A instituição não possui nenhum curso';
    }
}
<?php
} elseif ($get_curso && !$get_matricula) {
        ?>
function getCurso() {
    var campoCurso = document.getElementById('ref_cod_curso');
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
    campoCurso.length = 1;

    limpaCampos(3);
    if (campoInstituicao) {
        campoCurso.disabled = true;
        campoCurso.options[0].text = 'Carregando cursos';

        var xml = new ajax(atualizaLstCurso);
        xml.envia('educar_curso_xml.php?ins=' + campoInstituicao);
    } else {
        campoCurso.options[0].text = 'Selecione';
    }
}

function atualizaLstCurso(xml) {
    var campoCurso = document.getElementById('ref_cod_curso');
    campoCurso.length = 1;
    campoCurso.options[0].text = 'Selecione um curso';
    campoCurso.disabled = false;

    var cursos = xml.getElementsByTagName('curso');
    if (cursos.length) {
        for (var i = 0; i < cursos.length; i++) {
            campoCurso.options[campoCurso.options.length] = new Option(cursos[i].firstChild.data, cursos[i].getAttribute('cod_curso'), false, false);
        }
    } else {
        campoCurso.options[0].text = 'A instituição não possui nenhum curso';
    }
}
<?php
    }
if ($get_escola && $get_curso && $get_matricula) {
    ?>
function getMatricula() {
    getEscola();
    getCursoMatricula();
}
<?php
}
if ($get_escola && $get_curso && !$get_matricula) {
    ?>
function getDuploEscolaCurso() {
    getEscola();
    getCurso();
}
<?php
}
//if ( $get_escola_curso )
if ($get_curso) {
    ?>
function getEscolaCurso() {
    var campoCurso = document.getElementById('ref_cod_curso');
    if (document.getElementById('ref_cod_escola')) {
        var campoEscola = document.getElementById('ref_cod_escola').value;
    } else if (document.getElementById('ref_ref_cod_escola')) {
        var campoEscola = document.getElementById('ref_ref_cod_escola').value;
    }
    campoCurso.length = 1;

    limpaCampos(3);
    if (campoEscola) {
        campoCurso.disabled = true;
        campoCurso.options[0].text = 'Carregando cursos';

        var xml = new ajax(atualizaLstEscolaCurso);
        <?php if ($get_cursos_nao_padrao) {?>
        xml.envia('educar_curso_xml.php?esc=' + campoEscola + '&padrao_ano_escolar=nao');
        <?php } else {?>
        xml.envia('educar_curso_xml.php?esc=' + campoEscola);
        <?php } ?>
    } else {
        campoCurso.options[0].text = 'Selecione';
    }
}

function atualizaLstEscolaCurso(xml) {
    var campoCurso = document.getElementById('ref_cod_curso');
    campoCurso.length = 1;
    campoCurso.options[0].text = 'Selecione um curso';
    campoCurso.disabled = false;

    var cursos = xml.getElementsByTagName('curso');
    if (cursos.length) {
        for (var i = 0; i < cursos.length; i++) {
            campoCurso.options[campoCurso.options.length] = new Option(cursos[i].firstChild.data, cursos[i].getAttribute('cod_curso'), false, false);
        }
    } else {
        campoCurso.options[0].text = 'A escola não possui nenhum curso';
    }
}
<?php
}
if ($get_escola_curso_serie && $get_matricula && $_GET['ref_cod_aluno']) {
    // tah matriculando o aluno, seleciona as series que ele pode se matricular?
?>
function getEscolaCursoSerie() {
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
    var campoEscola = document.getElementById('ref_cod_escola').value;
    var campoCursoValue = document.getElementById('ref_cod_curso').value;
    var campoCurso = document.getElementById('ref_cod_curso');
    var campoSerie = document.getElementById('ref_ref_cod_serie');
    var cod_aluno = <?=$_GET['ref_cod_aluno']?>;

    campoSerie.length = 1;

    limpaCampos(4);
    if (campoInstituicao && campoCursoValue && campoEscola && cod_aluno) {
        campoSerie.disabled = true;
        campoSerie.options[0].text = 'Carregando séries';

        var xml = new ajax(atualizaLstSerieMatricula);
        xml.envia('educar_serie_matricula_xml.php?ins=' + campoInstituicao + '&cur=' + campoCursoValue + '&esc=' + campoEscola + '&alu=' + cod_aluno);
    } else {
        campoSerie.options[0].text = 'Selecione';
    }
}

function atualizaLstSerieMatricula(xml) {
    var campoSerie = document.getElementById('ref_ref_cod_serie');
    campoSerie.length = 1;
    campoSerie.options[0].text = 'Selecione uma série';
    campoSerie.disabled = false;

    series = xml.getElementsByTagName('serie');
    if (series.length) {
        for (var i = 0; i < xml.length; i++) {
            campoSerie.options[campoSerie.options.length] = new Option(series[i].firstChild.data, series[i].getAttribute('cod_serie'), false, false);
        }
    } else {
        campoSerie.options[0].text = 'A escola/curso não possui nenhuma série';
    }
}
<?php
}
if ($get_escola_curso_serie && !$get_matricula) {
    ?>
function getEscolaCursoSerie() {
    var campoCurso = document.getElementById('ref_cod_curso').value;
    if (document.getElementById('ref_cod_escola')) {
        var campoEscola = document.getElementById('ref_cod_escola').value;
    } else if (document.getElementById('ref_ref_cod_escola')) {
        var campoEscola = document.getElementById('ref_ref_cod_escola').value;
    }
    var campoSerie = document.getElementById('ref_ref_cod_serie');
    campoSerie.length = 1;

    limpaCampos(4);
    if (campoEscola && campoCurso) {
        campoSerie.disabled = true;
        campoSerie.options[0].text = 'Carregando séries';
        var xml = new ajax(atualizaLstEscolaCursoSerie);
        xml.envia('educar_escola_curso_serie_xml.php?esc=' + campoEscola + '&cur=' + campoCurso);
    } else {
        campoSerie.options[0].text = 'Selecione';
    }
}

function atualizaLstEscolaCursoSerie(xml) {
    var campoSerie = document.getElementById('ref_ref_cod_serie');
    campoSerie.length = 1;
    campoSerie.options[0].text = 'Selecione uma série';
    campoSerie.disabled = false;

    series = xml.getElementsByTagName('serie');
    if (series.length) {
        for (var i = 0; i < series.length; i++) {
            campoSerie.options[campoSerie.options.length] = new Option(series[i].firstChild.data, series[i].getAttribute('cod_serie'), false, false);
        }
    } else {
        campoSerie.options[0].text = 'A escola/curso não possui nenhuma série';
    }
}
<?php
}
if ($get_serie && $get_escola_serie) {
    // lista todas as series que nao estao associadas a essa escola
?>
function getSerie() {
    var campoCurso = document.getElementById('ref_cod_curso').value;
    if (document.getElementById('ref_cod_escola')) {
        var campoEscola = document.getElementById('ref_cod_escola').value;
    } else if (document.getElementById('ref_ref_cod_escola')) {
        var campoEscola = document.getElementById('ref_ref_cod_escola').value;
    }
    var campoSerie = document.getElementById('ref_cod_serie');

    campoSerie.length = 1;

    limpaCampos(4);
    if (campoEscola && campoCurso) {
        campoSerie.disabled = true;
        campoSerie.options[0].text = 'Carregando séries';

        var xml = new ajax(atualizaLstSerie);
        xml.envia('educar_serie_not_escola_xml.php?esc=' + campoEscola + '&cur=' + campoCurso);
    } else {
        campoSerie.options[0].text = 'Selecione';
    }
}

function atualizaLstSerie(xml) {

    var campoSerie = document.getElementById('ref_cod_serie');
    campoSerie.length = 1;
    campoSerie.options[0].text = 'Selecione uma série';
    campoSerie.disabled = false;

    series = xml.getElementsByTagName('serie');
    if (series.length) {
        for (var i = 0; i < series.length; i++) {
            campoSerie.options[campoSerie.options.length] = new Option(series[i].firstChild.data, series[i].getAttribute('cod_serie'), false, false);
        }
    } else {
        campoSerie.options[0].text = 'O curso não possui nenhuma série ou todas as séries já estã associadas a essa escola';
    }
}
<?php
}
if ($get_serie && !$get_escola_serie || $exibe_get_serie) {
    ?>
function getSerie() {
    var campoCurso = document.getElementById('ref_cod_curso').value;
    var campoSerie = document.getElementById('ref_cod_serie');
    if (!campoSerie)
        campoSerie = document.getElementById('ref_ref_cod_serie');
    campoSerie.length = 1;

    limpaCampos(4);
    if (campoCurso) {
        campoSerie.disabled = true;
        campoSerie.options[0].text = 'Carregando séries';

        var xml = new ajax(atualizaLstSerie);
        xml.envia('educar_serie_xml.php?cur=' + campoCurso);
    } else {
        campoSerie.options[0].text = 'Selecione';
    }
}

function atualizaLstSerie(xml) {
    var campoSerie = document.getElementById('ref_cod_serie');
    if (!campoSerie)
        campoSerie = document.getElementById('ref_ref_cod_serie');
    campoSerie.length = 1;
    campoSerie.options[0].text = 'Selecione uma série';
    campoSerie.disabled = false;

    series = xml.getElementsByTagName('serie');
    if (series.length) {
        for (var i = 0; i < series.length; i++) {
            campoSerie.options[campoSerie.options.length] = new Option(series[i].firstChild.data, series[i].getAttribute('cod_serie'), false, false);
        }
    } else {
        campoSerie.options[0].text = 'O curso não possui nenhuma série';
    }
}
<?php
}
if ($get_biblioteca) {
    ?>
function getBiblioteca(flag) {
    var campoBiblioteca = document.getElementById('ref_cod_biblioteca');
    campoBiblioteca.length = 1;

    campoBiblioteca.disabled = true;
    campoBiblioteca.options[0].text = 'Carregando bibliotecas';

    var xml = new ajax(atualizaLstBiblioteca);
    if (flag == 1) {
        xml.envia('educar_biblioteca_xml.php?ins=' + document.getElementById('ref_cod_instituicao').value);
    } else if (flag == 2) {
        xml.envia('educar_biblioteca_xml.php?esc=' + document.getElementById('ref_cod_escola').value);
    }
}

function atualizaLstBiblioteca(xml) {
    var campoBiblioteca = document.getElementById('ref_cod_biblioteca');
    campoBiblioteca.length = 1;
    campoBiblioteca.options[0].text = 'Selecione uma biblioteca';
    campoBiblioteca.disabled = false;

    bibliotecas = xml.getElementsByTagName('biblioteca');
    if (bibliotecas.length) {
        for (var i = 0; i < bibliotecas.length; i++) {
            campoBiblioteca.options[campoBiblioteca.options.length] = new Option(bibliotecas[i].firstChild.data, bibliotecas[i].getAttribute('cod_biblioteca'), false, false);
        }
    } else {
        campoBiblioteca.options[0].text = 'Nenhuma biblioteca';
    }
}
<?php
}
if ($get_cliente_tipo) {
    ?>
function getClienteTipo() {
    var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
    var campoClienteTipo = document.getElementById('ref_cod_cliente_tipo');
    campoClienteTipo.length = 1;

    if (campoClienteTipo) {
        campoClienteTipo.disabled = true;
        campoClienteTipo.options[0].text = 'Carregando tipos de cliente';

        var xml = new ajax(atualizaLstClienteTipo);
        xml.envia('educar_cliente_tipo_xml.php?bib=' + campoBiblioteca);
//          educar_cliente_tipo_xml = function() { atualizaLstClienteTipo(); };
//          strURL = "educar_cliente_tipo_xml.php?bib="+campoBiblioteca;
//          DOM_loadXMLDoc( strURL );
    } else {
        campoClienteTipo.options[0].text = 'Selecione';
    }
}

function atualizaLstClienteTipo(xml) {
    var campoClienteTipo = document.getElementById('ref_cod_cliente_tipo');
    campoClienteTipo.length = 1;
    campoClienteTipo.options[0].text = 'Selecione um tipo de cliente';
    campoClienteTipo.disabled = false;

    var tipos = xml.getElementsByTagName('cliente_tipo');
    if (tipos.length) {
        for (var i = 0; i < tipos.length; i++) {
            campoClienteTipo.options[campoClienteTipo.options.length] = new Option(tipos[i].firstChild.data, tipos[i].getAttribute('cod_cliente_tipo'), false, false);
        }
    } else {
        campoClienteTipo.options[0].text = 'A biblioteca não possui nenhum tipo de cliente';
    }
}
<?php
}
if ($get_funcao) {
    ?>
function getFuncao() {
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
    var campoFuncao = document.getElementById('ref_cod_funcao');
    campoFuncao.length = 1;

    if (campoFuncao) {
        campoFuncao.disabled = true;
        campoFuncao.options[0].text = 'Carregando funções';

        var xml = new ajax(atualizaLstFuncao);
        xml.envia('educar_funcao_xml.php?ins=' + campoInstituicao);
    } else {
        campoFuncao.options[0].text = 'Selecione';
    }
}

function atualizaLstFuncao(xml) {
    var campoFuncao = document.getElementById('ref_cod_funcao');
    campoFuncao.length = 1;
    campoFuncao.options[0].text = 'Selecione uma função';
    campoFuncao.disabled = false;

    var funcoes = xml.getElementsByTagName('funcao');
    if (funcoes.length) {
        for (var i = 0; i < funcoes.length; i++) {
            campoFuncao.options[campoFuncao.options.length] = new Option(funcoes[i].firstChild.data, funcoes[i].getAttribute('cod_funcao'), false, false);
        }
    } else {
        campoFuncao.options[0].text = 'A instituição não possui nenhuma função';
    }
}
<?php
}
if ($get_turma) {
    ?>
function getTurma() {
    var campoEscola = document.getElementById('ref_cod_escola').value;
    var campoSerie = document.getElementById('ref_ref_cod_serie').value;
    var campoTurma = document.getElementById('ref_cod_turma');
    campoTurma.length = 1;

    limpaCampos(5);
    if (campoTurma) {
        campoTurma.disabled = true;
        campoTurma.options[0].text = 'Carregando turmas';

        var xml = new ajax(atualizaLstTurma);
        xml.envia('educar_turma_xml.php?esc=' + campoEscola + '&ser=' + campoSerie);
    } else {
        campoTurma.options[0].text = 'Selecione';
    }
}

var after_getTurma = function () {
};

function atualizaLstTurma(xml) {
    var campoTurma = document.getElementById('ref_cod_turma');
    campoTurma.length = 1;
    campoTurma.options[0].text = 'Selecione uma turma';
    campoTurma.disabled = false;

    var turmas = xml.getElementsByTagName('turma');
    if (turmas.length) {
        for (var i = 0; i < turmas.length; i++) {
            campoTurma.options[campoTurma.options.length] = new Option(turmas[i].firstChild.data, turmas[i].getAttribute('cod_turma'), false, false);
        }
    } else {
        campoTurma.options[0].text = 'A série não possui nenhuma turma';
    }

    after_getTurma();
}
<?php
}
?>
function limpaCampos(nivel) {
    switch (nivel) {
        case 1: {
            if (document.getElementById('cod_instituicao'))
                document.getElementById('cod_instituicao').length = 1;
            if (document.getElementById('ref_cod_instituicao'))
                document.getElementById('ref_cod_instituicao').length = 1;
            if (document.getElementById('ref_ref_cod_instituicao'))
                document.getElementById('ref_ref_cod_instituicao').length = 1;
        }
        case 2: {
            if (document.getElementById('cod_escola'))
                document.getElementById('cod_escola').length = 1;
            if (document.getElementById('ref_cod_escola'))
                document.getElementById('ref_cod_escola').length = 1;
            if (document.getElementById('ref_ref_cod_escola'))
                document.getElementById('ref_ref_cod_escola').length = 1;
        }
        case 3: {
            if (document.getElementById('cod_curso'))
                document.getElementById('cod_curso').length = 1;
            if (document.getElementById('ref_cod_curso'))
                document.getElementById('ref_cod_curso').length = 1;
            if (document.getElementById('ref_ref_cod_curso'))
                document.getElementById('ref_ref_cod_curso').length = 1;
        }
        case 4: {
            if (document.getElementById('cod_serie'))
                document.getElementById('cod_serie').length = 1;
            if (document.getElementById('ref_cod_serie'))
                document.getElementById('ref_cod_serie').length = 1;
            if (document.getElementById('ref_ref_cod_serie'))
                document.getElementById('ref_ref_cod_serie').length = 1;
        }
        case 5: {
            if (document.getElementById('cod_turma'))
                document.getElementById('cod_turma').length = 1;
            if (document.getElementById('ref_cod_turma'))
                document.getElementById('ref_cod_turma').length = 1;
            if (document.getElementById('ref_ref_cod_turma'))
                document.getElementById('ref_ref_cod_turma').length = 1;
        }
    }
}

<?php
if ($get_semestre) {
    ?>

function verifica_curso() {
    var ref_cod_curso = document.getElementById('ref_cod_curso').value;
    if (ref_cod_curso != '') {
        var pars = 'ref_cod_curso=' + ref_cod_curso;
        new Ajax.Request('educar_matricula_cad_curso_segue_padrao.php', {
                method: 'post',
                parameters: pars,
                onComplete: function (resp) {
                    if (resp.responseText == 0) {
                        var radios = '<input type=\'radio\' id=\'sem1\' name=\'semestre\' value=\'1\'>1º Semestre<br>' +
                            '<input type=\'radio\' id=\'sem2\' name=\'semestre\' value=\'2\'>2º Semestre<br>';
                        $('div_semestre').innerHTML = radios;
                        $('is_padrao').value = 0;
                    } else {
                        $('div_semestre').innerHTML = 'Selecione um Curso';
                        $('is_padrao').value = 1;
                    }
                },
            },
        );
    } else {
        $('div_semestre').innerHTML = 'Selecione um Curso';
        $('is_padrao').value = 1;
    }
}

<?php
} ?>

</script>
