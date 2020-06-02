$j(document).ready(function(){

});

function acao() {
  $j('#formcadastro').attr('action', '/educacenso/importacao');

  $j('#formcadastro').removeAttr('onsubmit');
  $j('#formcadastro').submit();
}
