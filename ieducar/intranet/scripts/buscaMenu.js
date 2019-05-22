(function(){

  //inicia função jQuery para impedir conflitos de versão
  $j = jQuery.noConflict();

  doc = document;
  loc = doc.location;
  host = loc.hostname;
  $j("#busca-menu-input").ready(function(){

    var $element_nome_menu = $j("#busca-menu-input");

    var handleSelect = function(event, ui){
      $j(event.target).val(ui.item.label);
      return false;
    };

    var buscaMenu = function(request, response){
      var searchPath = '/module/Api/menu/';
      var params     = { query : request.term, oper : 'get', resource : 'menu-search' };

      $j.get(searchPath, params, function(dataresponse){
        simpleSearch.handleSearch(dataresponse, response)
      });

    };

    function setAutoComplete(){

      $element_nome_menu.autocomplete({
        source    : buscaMenu,
        select    : handleSelect,
        minLength : 1,
        autoFocus : false,

        messages : {
          noResults: '',
          results : function(resultsCount) {
            return resultsCount;
          }
        },

        focus: function(event, ui) {
          event.preventDefault();
          $element_nome_menu.val(ui.item.label);
        }

      });
    }
    $element_nome_menu.keyup(function(key){
      if(key.keyCode == '13'){

        caminho = $j("#busca-menu span").text();

        if(caminho){

          if(caminho.search(".php") > -1){
            var caminhoCompleto = caminho;
            loc.href = caminhoCompleto;
          }else{
            loc.pathname = caminho;
          }
        }
      }
      if($element_nome_menu.val() != ''){
        setAutoComplete();
      }
    });
  });
}());
