$j(document).ready(function(){

});

function acao() {
  if ($j('#ano').val() != '2018') {
    $j('#formcadastro').attr('action', '/educacenso/importacao');
  }

  $j('#formcadastro').removeAttr('onsubmit');
  $j('#formcadastro').submit();
}
