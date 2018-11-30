(function($){
  $(document).ready(function(){
    var $turmaField = $('#ref_cod_turma');
    var $anoField  = $('#ano');
    var $componenteCurricularField = $('#componentecurricular');
    var $instituicaoField = $('#ref_cod_instituicao');

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

    var handleGetComponentesArea = function(response){
      let componentes = Object.keys(response['options']||{});
      $j('#componentecurricular').val(componentes.concat($j('#componentecurricular').val()||[])).trigger('chosen:updated');
      $j("#dialog_area_conhecimento").dialog("close");
    }
 
    var preenchePorAreaConhecimento = function(){
      let areaConhecimento = $j('#area_conhecimento').val();
      if (!areaConhecimento) {
        alert('Área de conhecimento deve ser preenchida');
        return false;
      }
      urlForGetAreaConhecimento = getResourceUrlBuilder.buildUrl('/module/Api/ComponenteCurricular', 'componentes-curriculares-for-multiple-search', {
        turma_id  : $turmaField.val(),
        ano: $anoField.val(),
        instituicao_id: $instituicaoField.val(),
        area_conhecimento_id: areaConhecimento
      });
 
      var options = {
        url : urlForGetAreaConhecimento,
        dataType : 'json',
        success  : handleGetComponentesArea
      };
 
      getResources(options);
    }
 
    $j('body').append(htmlFormModal());

      $j('#area_conhecimento').chosen({
          width: '231px',
          placeholder_text_multiple: "Selecione as opções",
      });
 
    $j("#dialog_area_conhecimento").dialog({
      autoOpen: false,
      height: '500',
      width: 'auto',
      modal: true,
      resizable: false,
      draggable: false,
      title: 'Selecionar por área de conhecimento',
      buttons: {
          "Preencher": preenchePorAreaConhecimento,
          "Cancelar": function(){
              $j(this).dialog("close");
          }
      },
      create: function () {
          $j(this)
              .closest(".ui-dialog")
              .find(".ui-button-text:first")
              .addClass("btn-green");
      },
      close: function () {
          $j('#area_conhecimento').val("");
      }
    });
 
 
    var handleGetAreaConhecimento = function(response) {
      $j('#area_conhecimento').html('').val('');
      var selectOptions = response['options'];
      updateChozen($j('#area_conhecimento'), selectOptions);
      $j("#dialog_area_conhecimento").dialog("open");
    }
 
    function modalOpen(){
      var turma            = $turmaField.val();
 
      if (!turma) {
        alert('Informe uma turma');
        return false;
      }
 
      urlForGetAreaConhecimento = getResourceUrlBuilder.buildUrl('/module/Api/AreaConhecimento', 'areaconhecimento-turma', {
        turma_id  : turma
      });
 
      var options = {
        url : urlForGetAreaConhecimento,
        dataType : 'json',
        success  : handleGetAreaConhecimento
      };
 
      getResources(options);
    }
 
    function htmlFormModal(){
      return `<div id="dialog_area_conhecimento">
                <form>
                  <label for="area_conhecimento">Área de conhecimento</label>
                  <select multiple="multiple" name="area_conhecimento" id="area_conhecimento">
                </form>
              </div>`;
    }
 
    let $linkModalArea = $j('<a/>').attr('href','#').text('Selecionar por área de conhecimento').on('click', modalOpen);
    
    if (searchForArea) {
      $j('#tr_componentecurricular td:last-child').append($linkModalArea);
    }

    // bind onchange event
    $turmaField.change(updateComponenteCurricular);

    // load change event when page loads
    $turmaField.trigger('change');

  }); // ready
})(jQuery);
