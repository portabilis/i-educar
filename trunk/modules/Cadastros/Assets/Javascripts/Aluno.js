resourceOptions.name = 'aluno';

//handlePost
//handlePut

// new : true se id na url, senao false
resourceOptions.new = true;

resourceOptions.handlePost = function(dataResponse) {
  console.log('#TODO handle post');
  console.log(dataResponse);
}

var updatePersonDetails = function() {
  console.log('#TODO update person details');
  console.log($j('#pessoa_nome').val());
  console.log($j('#pessoa_id').val());
}

var simpleSearchPessoaOptions = {
  autocompleteOptions : { close : updatePersonDetails }
};