

  $j(function() {

  let checkIfSchoolIsActive = () => {
  let schoolId = $j("#ref_cod_escola").val();
  if (!schoolId) {
  return false;
}

  let urlForGetSchoolActive = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoAnalise', 'school-is-active', {
  school_id: schoolId
});

  let options = {
  url: urlForGetSchoolActive,
  dataType: 'json',
  success: (data) => {
  $j('#escola_em_andamento').val(data['active'] ? '1' : '0');
  if (!data['active']) {
  showNotActiveModal();
}
}
};

  getResources(options);
}

  $j('#ref_cod_escola').on('change', checkIfSchoolIsActive);

  let createNotActiveModal = () => {
  $j("body").append(`
<div id="not_active_modal" class="modal" style="display:none;">
   <p>Essa escola encontra-se paralisada ou extinta, portanto somente os dados dos registros 00, 30 e 40 serão analisados e exportados.</p>
</div>
        `);
}
  createNotActiveModal();

  let showNotActiveModal = () => {
  $j("#not_active_modal").modal();
}
});

  function acaoExportar() {
  document.formcadastro.target='_blank';
  acao();
  document.getElementById( 'btn_enviar' ).disabled = false;
  document.getElementById( 'btn_enviar' ).value = 'Analisar';
}

  function marcarCheck(idValue) {
  // testar com formcadastro
  var contaForm = document.formcadastro.elements.length;
  var campo = document.formcadastro;
  var i;

  for (i = 0; i < contaForm; i++) {
  if (campo.elements[i].id == idValue) {

  campo.elements[i].checked = campo.CheckTodos.checked;
}

}
}

