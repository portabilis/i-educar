function exibeMotivo() {
  if (jQuery('#ativo').val() == 1) {
    jQuery('#tr_motivo').hide();
  } else {
    jQuery('#tr_motivo').show();
  }
}

jQuery(document).ready(function(){
  jQuery('#ativo').change(exibeMotivo).trigger('change');
});
