(function($){
  $(document).ready(function(){

    var $turmaField = getElementFor('ref_cod_turma');
    var $anoField  = getElementFor('ano');
    var $componenteCurricularField = getElementFor('componentecurricular');
    var $instituicaoField = getElementFor('instituicao');

    var handleGetComponentesCurricular = function(response) {
      var selectOptions = response['options'];
      updateChozen($componenteCurricularField, selectOptions);
    }

    var updateComponenteCurricular = function(){
      clearValues($componenteCurricularField);
      if ($turmaField.val()) {

        var urlForGetComponenteCurricular = getResourceUrlBuilder.buildUrl('/module/Api/ComponenteCurricular', 'componentes-curriculares-for-multiple-search', {
          turma_id : $turmaField.val(),
          ano : $anoField.val(),
          instituicao_id: $instituicaoField.val()
        });

        var options = {
          url : urlForGetComponenteCurricular,
          dataType : 'json',
          success  : handleGetComponentesCurricular
        };

        getResources(options);
      }
    };

    // bind onchange event
    $turmaField.change(updateComponenteCurricular);

    // load change event when page loads
    $turmaField.trigger('change');

  }); // ready
})(jQuery);
