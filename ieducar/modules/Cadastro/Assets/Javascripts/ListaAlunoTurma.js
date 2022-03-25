var registrosLista = [];
var lista;
function listar(){

    var cod_turma = location.href.split('cod_turma=').pop();
    console.log(cod_turma);

    var urlForListar = postResourceUrlBuilder.buildUrl('/module/Api/ListaAlunoTurma', 'lista', {});

    var options = {
        type     : 'POST',
        url      : urlForListar,
        dataType : 'json',
        data     : {
            cod_turma    : cod_turma,
           
        },
        success  : handleListar
    };

    postResource(options);
}


function handleListar (response) {
    registrosLista = response.lista;
    console.log(response)
    opennig(registrosLista);
}


function opennig(registrosLista){
   lista = registrosLista.length;
    $j("#lista-aluno").find('#msg').html(mensagem(registrosLista));
    console.log('oi casada, sou mendigo')
    $j("#lista-aluno").dialog("open")

}

function close() {
    registrosLista = [];
    
    $j("#lista-aluno").dialog('close');
}

$j('body').append(
    '<div id="lista-aluno' + '" style="max-height: 80vh; width: 820px; overflow: auto;">' +
    '<div id="msg" class="msg"></div>' +
    '</div>'
);
$j('#dlista-aluno').find(':input').css('display', 'block');

$j("#lista-aluno").dialog({
    autoOpen: false,
    closeOnEscape: false,
    draggable: false,
    width: 820,
    modal: true,
    resizable: false,
    title: 'Listar alunos da turma',
    open: function(event, ui) {
        $j(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
    },
    buttons: {
        "Cancelar": function () {
            close();
        },
      
    }

});

function mensagem(registrosLista){
    if(!registrosLista){
       var reg = 'Não há alunos enturmados';
        return reg;
    }
    var lista = [];
    var registro = '';
    var tabela = '';
    tabela += `<div style="margin-bottom: 10px;">`;
    tabela += `<span style="display: block; float: left; width: 390px; font-weight: bold">Nome</span>`;

    registrosLista.forEach(function(lista)
    {
    tabela += `<div style = "margin-bottom: 10px; float: left;" class = "linha-disciplina"> `;
    tabela += `<span style = 'display: block; float: left; width: 390px'>${lista['nome']}</span>`;
});
    

    tabela += `</div>`;  
    tabela += `<br style="clear: left"/>`;

    registro += `<table cellspacing = "0" cellpading="0" border="0">`;
    registro += `<tr align = "left"><td>%s</td></tr>, tabela`;
    registro += `</table>`;
    console.log(tabela)

    return tabela;
  
}
