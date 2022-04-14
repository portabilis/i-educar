
function getUsuario(xml_usuario)
{
    var campoUsuario = document.getElementById('ref_cod_usuario');
    var DOM_array = xml_usuario.getElementsByTagName( "usuario" );

    if(DOM_array.length)
    {
        campoUsuario.length = 1;
        campoUsuario.options[0].text = 'Selecione um usuário';
        campoUsuario.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoUsuario.options[campoUsuario.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_usuario"),false,false);
        }
    }
    else
        campoUsuario.options[0].text = 'A instituição não possui nenhum usuário';
}

before_getEscola = function()
{
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

    var campoUsuario = document.getElementById('ref_cod_usuario');
    campoUsuario.length = 1;
    campoUsuario.disabled = true;
    campoUsuario.options[0].text = 'Carregando usuário';

    var xml_usuario = new ajax( getUsuario );
    xml_usuario.envia( "educar_usuario_xml.php?ins="+campoInstituicao );
}

document.getElementById('ref_cod_escola').onchange = function()
{
    var campoEscola = document.getElementById('ref_cod_escola').value;

    var campoUsuario = document.getElementById('ref_cod_usuario');
    campoUsuario.length = 1;
    campoUsuario.disabled = true;
    campoUsuario.options[0].text = 'Carregando usuário';

    var xml_usuario = new ajax( getUsuario );
    xml_usuario.envia( "educar_usuario_xml.php?esc="+campoEscola );
}

