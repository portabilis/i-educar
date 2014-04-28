(function($){
   $(document).ready(function(){
 
     // area reconhecimento necessita do numero da instituicao
     var $instituicaoField = getElementFor('instituicao');
     var $areaConhecimentoField = getElementFor('area_conhecimento')
 
     var handleGetAreasConhecimento = function(response) {
       var selectOptions = jsonResourcesToSelectOptions(response['options']);
       updateSelect($areaConhecimentoField, selectOptions, "Todas");
     }
 
     var updateAreasConhecimento = function(){
       resetSelect($areaConhecimentoField);
 
       if ($instituicaoField.val()) {
         $areaConhecimentoField.children().first().html('Aguarde carregando...');
 
         var urlForGetAreasConhecimento = getResourceUrlBuilder.buildUrl('/module/DynamicInput/AreaConhecimento', 'area_conhecimento', {
           instituicao_id   : $instituicaoField.val(),
         });
 
         var options = {
           url : urlForGetAreasConhecimento,
           dataType : 'json',
           success  : handleGetAreasConhecimento
         };
 
         getResources(options);
       }
 
       $areaConhecimentoField.change();
     };
 
     // bind onchange event
     $instituicaoField.change(updateAreasConhecimento);
 
   }); // ready
 })(jQuery);