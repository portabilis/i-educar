

  let populaOrgaoRegional = data => {
  $j('#orgao_regional').append(
    $j('<option/>').text('Selecione').val('')
  );
  if (data.orgaos) {
  $j.each(data.orgaos, function(){
  $j('#orgao_regional').append(
  $j('<option/>').text(this.codigo).val(this.codigo)
  );
});
}
}

  $j('#ref_sigla_uf').on('change', function(){
  let sigla_uf = this.value;
  $j('#orgao_regional').html('');
  if (sigla_uf) {
  let parametros = {
  oper: 'get',
  resource: 'orgaos_regionais',
  sigla_uf: sigla_uf
};
  let link = '../module/Api/EducacensoOrgaoRegional';
  $j.getJSON(link, parametros)
  .done(populaOrgaoRegional);
} else {
  $j('#orgao_regional').html('<option value="" selected>Selecione uma UF</option>');
}
});

  $j('#data_base').mask("99/99");
  $j('#data_fechamento').mask("99/99");


