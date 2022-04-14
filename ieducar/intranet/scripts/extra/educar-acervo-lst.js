


function getExemplarTipo(xml_exemplar_tipo)
{
    var campoTipo = document.getElementById('ref_cod_exemplar_tipo');
    var DOM_array = xml_exemplar_tipo.getElementsByTagName( "exemplar_tipo" );

    if(DOM_array.length)
    {
        campoTipo.length = 1;
        campoTipo.options[0].text = 'Selecione um tipo de exemplar';
        campoTipo.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoTipo.options[campoTipo.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_exemplar_tipo"),false,false);
        }
    }
    else
        campoTipo.options[0].text = 'A biblioteca não possui nenhum tipo de exemplar';
}

function getAcervoColecao(xml_acervo_colecao)
{
    var campoColecao = document.getElementById('ref_cod_acervo_colecao');
    var DOM_array = xml_acervo_colecao.getElementsByTagName( "acervo_colecao" );
    if(DOM_array.length)
    {
        campoColecao.length = 1;
        campoColecao.options[0].text = 'Selecione uma coleção';
        campoColecao.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoColecao.options[campoColecao.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_acervo_colecao"),false,false);
        }
    }
    else
        campoColecao.options[0].text = 'A biblioteca não possui nenhuma coleção';
}

function getAcervoEditora(xml_acervo_editora)
{
    var campoEditora = document.getElementById('ref_cod_acervo_editora');
    var DOM_array = xml_acervo_editora.getElementsByTagName( "acervo_editora" );
    if(DOM_array.length)
    {
        campoEditora.length = 1;
        campoEditora.options[0].text = 'Selecione uma editora';
        campoEditora.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoEditora.options[campoEditora.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_acervo_editora"),false,false);
        }
    }
    else
        campoEditora.options[0].text = 'A biblioteca não possui nenhuma editora';
}

document.getElementById('ref_cod_biblioteca').onchange = function()
{
    var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;

    var campoTipo = document.getElementById('ref_cod_exemplar_tipo');
    campoTipo.length = 1;
    campoTipo.disabled = true;
    campoTipo.options[0].text = 'Carregando tipo de exemplar';
    var xml_exemplar_tipo = new ajax( getExemplarTipo );
    xml_exemplar_tipo.envia( "educar_exemplar_tipo_xml.php?bib="+campoBiblioteca );

    var campoColecao = document.getElementById('ref_cod_acervo_colecao');
    campoColecao.length = 1;
    campoColecao.disabled = true;
    campoColecao.options[0].text = 'Carregando coleção';
    var xml_acervo_colecao = new ajax(getAcervoColecao);
    xml_acervo_colecao.envia("educar_acervo_colecao_xml.php?bib="+campoBiblioteca);

    var campoEditora = document.getElementById('ref_cod_acervo_editora');
    campoEditora.length = 1;
    campoEditora.disabled = true;
    campoEditora.options[0].text = 'Carregando editora';
    var xml_acervo_editora = new ajax(getAcervoEditora);
    xml_acervo_editora.envia("educar_acervo_editora_xml.php?bib="+campoBiblioteca);

};


