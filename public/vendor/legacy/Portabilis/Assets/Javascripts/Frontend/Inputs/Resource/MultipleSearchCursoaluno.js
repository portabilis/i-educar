(function($){
  $(document).ready(function(){

    var $alunoField = getElementFor('aluno');
    var $cursoAlunoField = getElementFor('cursoaluno');

    var handleGetCursoAluno = function(response) {
      var selectOptions = response['options'];
      console.log(selectOptions);
      updateChozen($cursoAlunoField, selectOptions);
    }

    var updateCursoAluno = function(){
      clearValues($cursoAlunoField);
      if ($alunoField.val()) {
        var urlForCursoAluno = getResourceUrlBuilder.buildUrl('/module/Api/CursoAluno', 'curso-aluno', {
          aluno_id : $alunoField.val()
        });

        var options = {
          url : urlForCursoAluno,
          dataType : 'json',
          success  : handleGetCursoAluno
        };

        getResources(options);
      }
    };

    // bind onchange event
    $alunoField.change(updateCursoAluno);

  }); // ready
})(jQuery);