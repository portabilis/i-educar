var campo_tipo_usuario = $j("#ref_cod_tipo_usuario");
var campo_instituicao = $j("#ref_cod_instituicao");
var campo_escola = $j("#escola");

function habilitaCampos()
{
  var nivel = cod_tipo_usuario[campo_tipo_usuario.val()];

  if( nivel == 1 || nivel == 2) {
    campo_instituicao.prop( "disabled", false );
    campo_escola.prop( "disabled", true );
  }
  else {
    campo_instituicao.prop( "disabled", false );
    campo_escola.prop( "disabled", false );
  }
  campo_escola.trigger("chosen:updated");
}

function valida()
{
  if( campo_instituicao.is(':enabled') && campo_instituicao.val() == "" )
  {
    alert("É obrigatório a escolha de uma Instituição!");
    return false;
  }
  else if( campo_escola.is(':enabled') && campo_escola.val() == null )
  {
    alert("É obrigatório a escolha de uma Escola! ");
    return false;
  }
  if(!acao())
    return;
  document.forms[0].submit();
}

campo_tipo_usuario.change(function(){
  habilitaCampos();
});

habilitaCampos();

$j(document).ready(function(){
  $escolas = $j('#escola');
  $escolas.trigger('chosen:updated');

  var handleGetEscolas = function(dataResponse) {
    setTimeout(function() {
      $j.each(dataResponse['escolas'], function(id, value) {
        $escolas.children("[value=" + value + "]").attr('selected', '');
      });
      $escolas.trigger('chosen:updated');
    }, 100);
  }

  var getEscolas = function() {
    var ref_pessoa = $j('#ref_pessoa').val();
    if ($j('#ref_pessoa').val()!='') {
      var additionalVars = {
        id : $j('#ref_pessoa').val(),
      };
      var options = {
        url      : getResourceUrlBuilder.buildUrl('/module/Api/escola', 'escolas-usuario', additionalVars),
        dataType : 'json',
        data     : {},
        success  : handleGetEscolas,
      };
      getResource(options);
    }
  }
  getEscolas();
});
