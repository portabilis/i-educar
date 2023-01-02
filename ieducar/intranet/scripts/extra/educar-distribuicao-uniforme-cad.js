function bloqueiaCamposQuantidade() {
  $j('#coat_pants_qty').val('').attr('disabled', 'disabled');
  $j('#shirt_short_qty').val('').attr('disabled', 'disabled');
  $j('#shirt_long_qty').val('').attr('disabled', 'disabled');
  $j('#socks_qty').val('').attr('disabled', 'disabled');
  $j('#shorts_tactel_qty').val('').attr('disabled', 'disabled');
  $j('#shorts_coton_qty').val('').attr('disabled', 'disabled');
  $j('#sneakers_qty').val('').attr('disabled', 'disabled');
  $j('#kids_shirt_qty').val('').attr('disabled', 'disabled');
  $j('#pants_jeans_qty').val('').attr('disabled', 'disabled');
  $j('#skirt_qty').val('').attr('disabled', 'disabled');
  $j('#coat_jacket_qty').val('').attr('disabled', 'disabled');
  return true;
}

function liberaCamposQuantidade() {
  $j('#coat_pants_qty').val('').removeAttr('disabled');
  $j('#shirt_short_qty').val('').removeAttr('disabled');
  $j('#shirt_long_qty').val('').removeAttr('disabled');
  $j('#socks_qty').val('').removeAttr('disabled');
  $j('#shorts_tactel_qty').val('').removeAttr('disabled');
  $j('#shorts_coton_qty').val('').removeAttr('disabled');
  $j('#sneakers_qty').val('').removeAttr('disabled');
  $j('#kids_shirt_qty').val('').removeAttr('disabled');
  $j('#pants_jeans_qty').val('').removeAttr('disabled');
  $j('#skirt_qty').val('').removeAttr('disabled');
  $j('#coat_jacket_qty').val('').removeAttr('disabled');
}

$j(document).ready(function () {
  if ($j('#complete_kit').is(':checked'))
    bloqueiaCamposQuantidade();

  $j('#complete_kit').on('change', function () {
    if ($j('#complete_kit').is(':checked'))
      bloqueiaCamposQuantidade();
    else
      liberaCamposQuantidade();
  });

  if ($j('#type').val() != 'Entregue')
    $j("#distribution_date").closest('tr').hide();

  $j('#type').on('change', function () {
    if ($j('#type').val() == 'Entregue') {
      $j("#distribution_date").closest('tr').show();
      $j('#distribution_date').attr('required', 'required');
    } else {
      $j("#distribution_date").closest('tr').hide();
      $j('#distribution_date').removeAttr('required');
    }
  });
  $j("#ref_cod_instituicao").trigger("chosen:updated");
  $j("#ref_cod_escola").trigger("chosen:updated");
})

