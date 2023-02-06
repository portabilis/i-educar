$j('#show-detail').click(function () {
  if ($j('#det_pree').css('display') === 'none') {
    $j('#det_pree').css('display', 'inline');
    $j(this).html('Ocultar detalhe');
  } else {
    $j('#det_pree').css('display', 'none');
    $j(this).html('Mostrar detalhe');
  }
});
