var campo_tipo_usuario = $j("#ref_cod_tipo_usuario");
var campo_instituicao = $j("#ref_cod_instituicao");
var campo_escola = $j("#escola");

function habilitaCampos()
{
  var nivel = cod_tipo_usuario[campo_tipo_usuario.val()];

  if( nivel == 1 )
  {
    campo_instituicao.prop( "disabled", false );
    campo_escola.prop( "disabled", false );
  }
  else if( nivel == 2 )
  {
    campo_instituicao.prop( "disabled", false );
    campo_escola.prop( "disabled", true );
  }
  else if( nivel == 4 )
  {
    campo_instituicao.prop( "disabled", false );
    campo_escola.prop( "disabled", false );
  }
  else if( nivel == 8 )
  {
    campo_instituicao.prop( "disabled", true );
    campo_escola.prop( "disabled", true );
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