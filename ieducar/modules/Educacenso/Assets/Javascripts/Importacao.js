$j(document).ready(function(){
  $j('#ano').val('2019')
});

function acao() {
  $j('#formcadastro').attr('action', '/educacenso/importacao');

  $j('#formcadastro').removeAttr('onsubmit');
  $j('#formcadastro').submit();
}
