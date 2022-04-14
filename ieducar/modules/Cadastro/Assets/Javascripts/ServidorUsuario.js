var servidor_usuario_checkboxs = [];

(function($){
    $(document).ready(function(){
        var servidor_usuario_checkbox = document.getElementById('servidor_usuario_checkbox[]');

        servidor_usuario_checkboxs = document.getElementsByName('servidor_usuario_checkbox[]');

        if (servidor_usuario_checkbox) {
            servidor_usuario_checkbox.onchange = function () {
                servidor_usuario_checkboxs.forEach(_servidor_usuario_checkbox => {
                    _servidor_usuario_checkbox.checked = servidor_usuario_checkbox.checked;
                });
            };
        }

        var botoesSelecaoUsuariosServidores = document.getElementsByClassName('botoes-selecao-usuarios-servidores');
        for (let botaoSelecaoUsuariosServidores of botoesSelecaoUsuariosServidores) {
            if (botaoSelecaoUsuariosServidores.value === " Gerar senha(s)")
                botaoSelecaoUsuariosServidores.setAttribute('onclick', 'iniciaCadastroUsuarioServidorSelecionados()');
            else if (botaoSelecaoUsuariosServidores.value === " Desativar selecionado(s)")
                botaoSelecaoUsuariosServidores.setAttribute('onclick', 'iniciaDesativacaoUsuarioServidorSelecionados()');
            else if (botaoSelecaoUsuariosServidores.value === " Ativar selecionado(s)")
                botaoSelecaoUsuariosServidores.setAttribute('onclick', 'iniciaAtivacaoUsuarioServidorSelecionados()');      
        }
    });
})(jQuery);

const btnDesativar = `
    <button
        id='servidor_usuario_btn[#aaytu#]'
        name='servidor_usuario_btn[]'
        style='width: 80px;'
        class='btn btn-info'
        onclick='(function(e){iniciaAtivacaoUsuarioServidor(e, #aaytu#)})(event)'
    >
        Ativar
    </button>
`;

const btnAtivar = `
    <button
        id='servidor_usuario_btn[#aaytu#]'
        name='servidor_usuario_btn[]'
        style='width: 80px;'
        class='btn btn-danger'
        onclick='(function(e){iniciaDesativacaoUsuarioServidor(e, #aaytu#)})(event)'
    >
        Desativar
    </button>
`;

function iniciaCadastroUsuarioServidorSelecionados () {
    for (let servidor_usuario_checkbox of servidor_usuario_checkboxs) {
        if (servidor_usuario_checkbox.checked) {
            const cod_servidor = pegarCodServidor(servidor_usuario_checkbox);
            iniciaCadastroUsuarioServidor(null, cod_servidor);
        }
    }
}

function iniciaDesativacaoUsuarioServidorSelecionados () {
    for (let servidor_usuario_checkbox of servidor_usuario_checkboxs) {
        if (servidor_usuario_checkbox.checked) {
            const cod_servidor = pegarCodServidor(servidor_usuario_checkbox);
            iniciaDesativacaoUsuarioServidor(null, cod_servidor);
        }
    }
}

function iniciaAtivacaoUsuarioServidorSelecionados () {
    for (let servidor_usuario_checkbox of servidor_usuario_checkboxs) {
        if (servidor_usuario_checkbox.checked) {
            const cod_servidor = pegarCodServidor(servidor_usuario_checkbox);
            iniciaAtivacaoUsuarioServidor(null, cod_servidor);
        }
    }
}

function iniciaCadastroUsuarioServidor (e, cod_servidor) {
    e?.preventDefault();

    pegarDadosServidor(cod_servidor);
}

async function pegarDadosServidor (cod_servidor) {
    var servidor = null;
    var servidor_alocacoes = null;
    var tipo_usuario_professor = null;

    if (cod_servidor) {
        var searchPathDadosServidor = '/module/Api/Servidor?oper=get&resource=dados-servidor',
        paramsDadosServidor = {
            servidor_id: cod_servidor
        };

        await $j.get(searchPathDadosServidor, paramsDadosServidor, function (dataResponse) {
            servidor = dataResponse.result;
        });


        var searchPathServidorAlocacao = '/module/Api/ServidorAlocacao?oper=get&resource=dados-servidor-alocacao',
        paramsServidorAlocacao = {
            cod_servidor: cod_servidor
        };

        await $j.get(searchPathServidorAlocacao, paramsServidorAlocacao, function (dataResponse) {
            servidor_alocacoes = dataResponse.result;
        });


        var searchPathTipoUsuarioProfessor = '/module/Api/TipoUsuario?oper=get&resource=dados-tipo-usuario-professor';

        await $j.get(searchPathTipoUsuarioProfessor, function (dataResponse) {
            tipo_usuario_professor = dataResponse.result;
        });


        if (servidor === false) {
            alert("Informações detalhadas do servidor não foram encontradas.");
            return;
        }

        if (servidor_alocacoes === null) {
            alert("Servidor não está alocado neste ano.");
            return;
        }

        if (tipo_usuario_professor === false) {
            alert("Tipo de usuário \"Professor\" não foi encontrado.");
            return;
        }

        cadastrarUsuarioServidor(servidor, servidor_alocacoes, tipo_usuario_professor);
    }

    return null;
}

function cadastrarUsuarioServidor (servidor, servidor_alocacoes, tipo_usuario_professor) {
    var escolas = [];
    
    servidor_alocacoes.forEach(servidor_alocacao => {
        escolas.push(servidor_alocacao.ref_cod_escola);
    });

    escolas = uniq(escolas);

    if (servidor.cpf === null || typeof servidor.cpf !== 'string') {
        alert("CPF do servidor não cadastrado ou inválido.")
        return;
    }

    var email = null;
    var ref_cod_instituicao = 1;
    var ref_pessoa = servidor.cod_servidor;
    var matricula = servidor.cpf.padStart(11, "0").toString();
    var _senha = "Mudar2022!";
    var ativo = 1;
    var ref_cod_funcionario_vinculo = 3;
    var tempo_expira_senha = 0;
    var data_expiracao = null;
    var pessoa_logada = $j("#pessoaLogada").val();
    var matricula_interna = null;
    var force_reset_password = true;
    var ref_cod_tipo_usuario = tipo_usuario_professor.cod_tipo_usuario;
    var escolas = escolas;

    var urlForCadastrarUsuarioServidor = postResourceUrlBuilder.buildUrl('/module/Api/UsuarioServidor', 'cadastrar-usuario-servidor', {});

    var options = {
        type     : 'POST',
        url      : urlForCadastrarUsuarioServidor,
        dataType : 'json',
        data     : {
            email                          :       email,
            ref_cod_instituicao            :       ref_cod_instituicao,
            ref_pessoa                     :       ref_pessoa,
            matricula                      :       matricula,
            _senha                         :       _senha,
            ativo                          :       ativo,
            ref_cod_funcionario_vinculo    :       ref_cod_funcionario_vinculo,
            tempo_expira_senha             :       tempo_expira_senha,
            data_expiracao                 :       data_expiracao,
            pessoa_logada                  :       pessoa_logada,
            matricula_interna              :       matricula_interna,
            force_reset_password           :       force_reset_password,
            ref_cod_tipo_usuario           :       ref_cod_tipo_usuario,
            escolas                        :       escolas,
        },
        success  : handleCadastrarUsuarioServidor
    };

    postResource(options);
}

function handleCadastrarUsuarioServidor (response) {
    if (!isNaN(response.result.cod_servidor) && !isNaN(response.result.matricula)) {
        span_btn_parent = document.getElementById(`servidor_usuario_btn[${response.result.cod_servidor}]`).parentElement;
        span_btn_parent.innerHTML = '';
        span_btn_parent.innerHTML = btnAtivar.replace(/#aaytu#/g, response.result.cod_servidor);

        span_matricula = document.getElementById(`servidor_usuario_matricula[${response.result.cod_servidor}]`);
        span_matricula.innerHTML = response.result.matricula;
    }
}

function iniciaDesativacaoUsuarioServidor (e, cod_servidor) {
    e?.preventDefault();

    var urlForDesativarUsuarioServidor = postResourceUrlBuilder.buildUrl('/module/Api/UsuarioServidor', 'desativar-usuario-servidor', {});

    var options = {
        type     : 'POST',
        url      : urlForDesativarUsuarioServidor,
        dataType : 'json',
        data     : {
            cod_servidor    :       cod_servidor,
        },
        success  : handleDesativarUsuarioServidor
    };

    postResource(options);
}

function handleDesativarUsuarioServidor (response) {
    if (!isNaN(response.result)) {
        span_btn_parent = document.getElementById(`servidor_usuario_btn[${response.result}]`).parentElement;
        span_btn_parent.innerHTML = '';
        span_btn_parent.innerHTML = btnDesativar.replace(/#aaytu#/g, response.result);
    }
}

function iniciaAtivacaoUsuarioServidor (e, cod_servidor) {
    e?.preventDefault();
 
    var urlForAtivarUsuarioServidor = postResourceUrlBuilder.buildUrl('/module/Api/UsuarioServidor', 'ativar-usuario-servidor', {});

    var options = {
        type     : 'POST',
        url      : urlForAtivarUsuarioServidor,
        dataType : 'json',
        data     : {
            cod_servidor    :       cod_servidor,
        },
        success  : handleAtivarUsuarioServidor
    };

    postResource(options);
}

function handleAtivarUsuarioServidor (response) {
    if (!isNaN(response.result)) {
        span_btn_parent = document.getElementById(`servidor_usuario_btn[${response.result}]`).parentElement;
        span_btn_parent.innerHTML = '';
        span_btn_parent.innerHTML = btnAtivar.replace(/#aaytu#/g, response.result);
    }
}

function uniq(array) {
    return array.sort().filter(function(item, pos, ary) {
        return !pos || item != ary[pos - 1];
    });
}

function pegarCodServidor (checkbox) {
    let id = checkbox.id;
    id = id.substring(id.indexOf('[') + 1, id.indexOf(']'));
  
    return id;
}