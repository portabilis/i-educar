const BuscaAtiva = (function () {

  const data_inicio = $j('#data_inicio');
  const data_fim = $j('#data_fim');
  const resultado_busca_ativa = $j('#resultado_busca_ativa');

  const init = function () {
    maskDates();
    hideTipoRetornoBusca();
  }

  const maskDates = function (){
    data_inicio.mask("99/99/9999", {placeholder: "__/__/____"});
    data_fim.mask("99/99/9999", {placeholder: "__/__/____"});
  }

  const hideTipoRetornoBusca = function () {
    if(!data_fim.val()) {
      resultado_busca_ativa.attr('disabled', true);
      resultado_busca_ativa.closest('tr').hide();
    }
  }

  const showTipoRetornoBusca = function (){
    resultado_busca_ativa.attr('disabled', false);
    resultado_busca_ativa.closest('tr').show();
    resultado_busca_ativa.removeAttr('style');
  }
  return {
    init,
    hideTipoRetornoBusca,
    showTipoRetornoBusca
  }
})();
