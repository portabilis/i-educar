(function($){
   $(document).ready(function(){

     var $escolaDestinoTransporteEscolar = getElementFor('escola_destino_transporte_escolar_id');

     var handleGetEscolaDestinoTransporteEscolar = function(response) {
       var selectOptions = jsonResourcesToSelectOptions(response['options']);
       updateSelect($escolaDestinoTransporteEscolar, selectOptions, "Todos");
     };

     var updateEscolaDestinoTransporteEscolar = function(){
       resetSelect($escolaDestinoTransporteEscolar);

       $escolaDestinoTransporteEscolar.children().first().html('Aguarde carregando...');

       var urlForGetEscolaDestinoTransporteEscolar = getResourceUrlBuilder.buildUrl('/module/DynamicInput/EscolaDestinoTransporteEscolar', 'escola_destino_transporte_escolar');

         var options = {
           url : urlForGetEscolaDestinoTransporteEscolar,
           success  : handleGetEscolaDestinoTransporteEscolar
         };

         getResources(options);
       };

       updateEscolaDestinoTransporteEscolar();

   });
 })(jQuery);