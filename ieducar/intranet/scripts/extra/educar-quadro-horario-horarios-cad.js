document.getElementById('ref_cod_servidor_lupa').onclick = function() {
    validaCampoServidor();
}

document.getElementById('ref_cod_escola').onchange = function() {
    getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function() {
    getEscolaCursoSerie();
}

document.getElementById('ref_cod_serie').onchange = function() {
    getTurma();
}

document.getElementById('btn_enviar').onclick = function() {
    verificaHorario();
}

document.getElementById('ref_cod_disciplina').onchange = function() {
    document.getElementById('ref_cod_servidor').length = 1;
}

function validaCampoServidor()
{
    document.getElementById('ref_cod_servidor').length = 1;

    if (document.getElementById('dia_semana').value == '') {
        alert('Você deve escolher o dia da semana!');
        return;
    }
    else if (document.getElementById('hora_inicial').value == '')
    {
        alert('Você deve preencher o campo Hora Inicial!');
        return;
    }
    else if (document.getElementById('hora_final').value == '')
    {
        alert('Você deve preencher o campo Hora Final!');
        return;
    }
    else
    {
        var ref_cod_instituicao;
        var ref_cod_escola;
        var dia_semana;
        var hora_inicial;
        var hora_final;
        var lst_matriculas;
        var min_mat;
        var min_ves;
        var min_not;
        var identificador;
        var ref_cod_disciplina;
        var ref_cod_curso;
        var ano_alocacao;

        if (document.getElementById('ref_cod_instituicao').value) {
            ref_cod_instituicao = document.getElementById('ref_cod_instituicao').value;
        }

        if (document.getElementById('ref_cod_escola').value) {
            ref_cod_escola = document.getElementById('ref_cod_escola').value;
        }

        if (document.getElementById('dia_semana').value) {
            dia_semana = document.getElementById('dia_semana').value;
        }

        if (document.getElementById('hora_inicial').value) {
            hora_inicial = document.getElementById('hora_inicial').value;
        }

        if (document.getElementById('hora_final').value) {
            hora_final = document.getElementById('hora_final').value;
        }

        if (document.getElementById('lst_matriculas').value) {
            lst_matriculas = document.getElementById('lst_matriculas').value;
        }

        if (document.getElementById('min_mat').value) {
            min_mat = parseInt(document.getElementById('min_mat').value, 10);
        }

        if (document.getElementById('min_ves').value) {
            min_ves = parseInt(document.getElementById('min_ves').value, 10);
        }

        if (document.getElementById('min_not').value) {
            min_not = parseInt(document.getElementById('min_not').value, 10);
        }

        if (document.getElementById('identificador').value) {
            identificador = document.getElementById('identificador').value;
        }

        if (document.getElementById('ref_cod_disciplina').value) {
            ref_cod_disciplina = document.getElementById('ref_cod_disciplina').value;
        }

        if (document.getElementById('ref_cod_curso').value) {
            ref_cod_curso = document.getElementById('ref_cod_curso').value;
        }

        if (document.getElementById('ano_alocacao').value) {
            ano_alocacao = document.getElementById('ano_alocacao').value;
        }

        if (document.getElementById('hora_inicial').value && document.getElementById('hora_final').value) {
            var hr_ini;
            var hr_fim;

            hr_ini  = hora_inicial.split(':');
            hr_fim  = hora_final.split(':');

            hr_ini[0] = parseInt(hr_ini[0], 10);
            hr_ini[1] = parseInt(hr_ini[1], 10);
            hr_fim[0] = parseInt(hr_fim[0], 10);
            hr_fim[1] = parseInt(hr_fim[1], 10);

            min_ini = (hr_ini[0] * 60) + hr_ini[1];
            min_fim = (hr_fim[0] * 60) + hr_fim[1];

            if ( min_ini >= 480 && min_ini <= 720) {
                if (min_fim <= 720) {
                    min_mat += min_fim - min_ini;
                }
                else if (min_fim >= 721 && min_fim <= 1080) {
                    min_mat += 720 - min_ini;
                    min_ves += min_fim - 720;
                }
                else if ((min_fim >= 1081 && min_fim <= 1439) || min_fim == 0) {
                    min_mat += 720 - min_ini;
                    min_ves += 360;

                    if (min_fim >= 1081 && min_fim <= 1439) {
                        min_not += min_fim - 1080;
                    }
                    else if (min_fim = 0) {
                        min_not += 360;
                    }
                }
            }
            else if (min_ini >= 721 && min_ini <= 1080) {
                if (min_fim <= 1080) {
                    min_ves += min_fim - min_ini;
                }
                else if ((min_fim >= 1081 && min_fim <= 1439) || min_fim == 0) {
                    min_ves += 1080 - min_ini;

                    if (min_fim >= 1081 && min_fim <= 1439) {
                        min_not += min_fim - 1080;
                    }
                    else if (min_fim = 0) {
                        min_not += 360;
                    }
                }
            }
            else if ((min_ini >= 1081 && min_ini <= 1439) || min_ini == 0) {
                if (min_fim <= 1439) {
                    min_not += min_fim - min_ini;
                }
                else if (min_fim == 0) {
                    min_not += 1440 - min_ini;
                }
            }
        }

        if (verificaQuadroHorario()) {
            if (document.getElementById('lst_matriculas').value) {
                pesquisa_valores_popless('educar_pesquisa_servidor_lst.php?campo1=ref_cod_servidor&matricula=1&ref_cod_servidor=0&ref_cod_instituicao=' + ref_cod_instituicao + '&ref_cod_escola=' + ref_cod_escola + '&dia_semana=' + dia_semana + '&hora_inicial=' + hora_inicial + '&hora_final=' + hora_final + '&horario=S' + '&lst_matriculas=' + lst_matriculas + '&min_mat=' + min_mat + '&min_ves=' + min_ves + '&min_not=' + min_not + '&identificador=' + identificador + '&ref_cod_disciplina=' + ref_cod_disciplina + '&ref_cod_curso=' + ref_cod_curso + '&ano_alocacao=' + ano_alocacao, 'ref_cod_servidor');
            }
            else {
                pesquisa_valores_popless('educar_pesquisa_servidor_lst.php?campo1=ref_cod_servidor&matricula=1&ref_cod_servidor=0&ref_cod_instituicao=' + ref_cod_instituicao + '&ref_cod_escola=' + ref_cod_escola + '&dia_semana=' + dia_semana + '&hora_inicial=' + hora_inicial + '&hora_final=' + hora_final + '&horario=S' + '&min_mat=' + min_mat + '&min_ves=' + min_ves + '&min_not=' + min_not + '&identificador=' + identificador + '&ref_cod_disciplina=' + ref_cod_disciplina + '&ref_cod_curso=' + ref_cod_curso+ '&ano_alocacao=' + ano_alocacao, 'ref_cod_servidor');
            }
        }
    }
}

function verificaQuadroHorario()
{
    var aux      = '';
    var cont     = 1;
    var hora_ini = document.getElementById('hora_inicial').value.substring(0, 2);
    var min_ini  = document.getElementById('hora_inicial').value.substring(3);
    var hora_fim = document.getElementById('hora_final').value.substring(0, 2);
    var min_fim  = document.getElementById('hora_final').value.substring(3);

    hora_ini = parseInt(hora_ini, 10) + (parseFloat(min_ini)  / 60);
    hora_fim = parseInt(hora_fim, 10) + (parseFloat(min_fim) / 60);

    if (hora_ini >= hora_fim) {
        alert('O horário de início deve ser menor que o horário final');
        return false;
    }

    do {
        if (document.getElementById( cont + '_dia_semana')) {
            if (document.getElementById(cont + '_dia_semana').value == document.getElementById('dia_semana').value) {
                if ((document.getElementById('hora_inicial').value < document.getElementById(cont + '_hora_inicial').value
                    && document.getElementById('hora_final').value < document.getElementById(cont + '_hora_inicial').value)
                    || (document.getElementById('hora_inicial').value >= document.getElementById(cont + '_hora_final').value
                        && document.getElementById('hora_final').value > document.getElementById(cont + '_hora_final').value))
                {
                }
                else {
                    alert( 'O horário escolhido coincide com um horário já existente!' );
                    return false;
                }
            }

            cont++;
        }
        else {
            aux = 'sair';
            return true;
        }
    } while (aux == '');
}

function verificaHorario()
{
    if (parseInt(quadro_horario, 10) == 0 && !($j('#ref_cod_disciplina').val() == 'todas_disciplinas')) {
        alert('Você deve incluir pelo menos um horário');
        return false;
    }else if ($j('#ref_cod_disciplina').val() == 'todas_disciplinas'){
        if (document.getElementById('ref_cod_disciplina').value == '') {
            alert('Você deve escolher a disciplina!');
            return;
        }
        else if (document.getElementById('hora_inicial').value == '') {
            alert('Você deve preencher o campo Hora Inicial!');
            return;
        }
        else if (document.getElementById('hora_final').value == '') {
            alert('Você deve preencher o campo Hora Final!');
            return;
        }
        else if (document.getElementById('ref_cod_servidor').value == '') {
            alert('Você deve selecionar um servidor no campo Servidor');
            return;
        }
    }
    acao();
    return true;
}
$j('#ref_cod_disciplina').change(todas_disciplinas);

function todas_disciplinas(){
    if($j('#ref_cod_disciplina').val() == 'todas_disciplinas'){
        $j("#btn_incluir_horario").closest('tr').hide();
    }else{
        $j("#btn_incluir_horario").closest('tr').show();
    }
}

$j('#btn_incluir_horario').click(addHorario);

function addHorario(){
    if (document.getElementById('ref_cod_disciplina').value == '') {
        alert('Você deve escolher a disciplina!');
        return;
    }
    else if (document.getElementById('hora_inicial').value == '') {
        alert('Você deve preencher o campo Hora Inicial!');
        return;
    }
    else if (document.getElementById('hora_final').value == '') {
        alert('Você deve preencher o campo Hora Final!');
        return;
    }
    else if (document.getElementById('ref_cod_servidor').value == '') {
        alert('Você deve selecionar um servidor no campo Servidor');
        return;
    }
    else {
        if (verificaQuadroHorario()) {
            $j('#incluir_horario').val('S');
            $j('#tipoacao').val('');
            formcadastro.submit();
        }
    }
}

