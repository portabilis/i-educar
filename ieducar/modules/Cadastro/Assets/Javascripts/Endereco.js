// --------------------------------- SCRIPTS ENDEREÇAMENTO ---------------------------------------- //

  $j('<a>') .html('N&atilde;o sei meu CEP')
               .attr('target', '_blank')
               .attr('id', 'span-busca-cep')
               .css('color', 'blue')
               .css('margin-left', '10px')
               .attr('href', 'http://www.buscacep.correios.com.br/sistemas/buscacep/')
               .appendTo($j('#cep_').closest('td'));

function hideEnderecoFields(){
  if($j('#cep_').val()){

    if ($j('#bairro_id').val())
      bloqueiaCadastroBairro();
    else
      bloqueiaBuscaBairro();

    if ($j('#logradouro_id').val())
      bloqueiaCadastroLogradouro();
    else
      bloqueiaBuscaLogradouro();      

  }else{

    $j('#bairro').closest('tr').hide(); 
    $j('#logradouro').closest('tr').hide(); 
  }
}

function preenchaCampoCepPrimeiro(){
  messageUtils.error('Digite um CEP primeiro...');
  $j('#cep_').focus();
}


// Campo CEP

var handleGetCep = function(dataResponse) {

  if (dataResponse['cep']){
    $j('#municipio_id').val(dataResponse['idmun']);
    $j('#municipio_municipio').val(dataResponse['idmun'] + ' - ' + dataResponse['nome'] + ' (' + dataResponse['sigla_uf'] + ')');
    $j('#distrito_id').val(dataResponse['iddis']);
    $j('#distrito_distrito').val(dataResponse['iddis'] + ' - ' + dataResponse['nome_distrito']);    
    $j('#bairro_id').val(dataResponse['idbai']);
    $j('#bairro_bairro').val(dataResponse['nome_bairro']+' / Zona '+(dataResponse['zona_localizacao'] == 1 ? 'Urbana' : 'Rural'));
    $j('#logradouro_id').val(dataResponse['idlog']);
    $j('#logradouro_logradouro').val(dataResponse['tipo_logradouro']+' '+dataResponse['nome_logradouro']);   
     
  }else{
    $j('#municipio_id').val('');
    $j('#municipio_municipio').val('');
    $j('#distrito_id').val('');
    $j('#distrito_distrito').val('');    
    $j('#bairro_id').val('');
    $j('#bairro_bairro').val('');
    $j('#logradouro_id').val('');
    $j('#logradouro_logradouro').val('');
  }

  $j('#municipio_municipio').removeAttr('disabled');
  $j('#distrito_distrito').removeAttr('disabled');
  $j('#bairro_bairro').removeAttr('disabled');
  $j('#logradouro_logradouro').removeAttr('disabled');
  $j('#bairro').removeAttr('disabled');
  $j('#zona_localizacao').removeAttr('disabled');
  $j('#idtlog').removeAttr('disabled');
  $j('#logradouro').removeAttr('disabled');
  bloqueiaCadastroBairro();
  bloqueiaCadastroLogradouro();
  fixUpPlaceholderEndereco();
}

// Caso cep seja válido dispara ajax para recuperar dados do primeiro cep encontrado
var searchCep = function() {

  var cep = $j('#cep_').val();
  
  if (checkCepFields(cep)) {    

    var additionalVars = {
      cep : cep,
    };

    var options = {
      url      : getResourceUrlBuilder.buildUrl('/module/Api/endereco', 'primeiro_endereco_cep', additionalVars),
      dataType : 'json',
      data     : {},
      success  : handleGetCep
    };

    getResource(options);
  }else
    clearEnderecoFields();
  
}
// Ao digitar um cep inválido todos os campos de endereçamento são bloqueados e limpados
function clearEnderecoFields(){
  $j('#bairro').attr('disabled','disabled');
  $j('#zona_localizacao').attr('disabled','disabled');
  $j('#bairro_bairro').attr('disabled','disabled');
  $j('#logradouro_logradouro').attr('disabled','disabled');
  $j('#idtlog').attr('disabled','disabled');
  $j('#logradouro').attr('disabled','disabled');
  $j('#municipio_municipio').attr('disabled','disabled');
  $j('#distrito_distrito').attr('disabled','disabled');
  $j('#bairro').val('');
  $j('#zona_localizacao').val('');
  $j('#bairro_bairro').val('');
  $j('#logradouro_logradouro').val('');
  $j('#idtlog').val('');
  $j('#logradouro').val('');  
  $j('#bairro_id').val('');  
  $j('#logradouro_id').val('');  
  $j('#municipio_municipio').val('');  
  $j('#distrito_distrito').val('');  
  $j('#municipio_id').val('');  
  $j('#distrito_id').val('');  
}
// Verifica se o formato do cep é válido
function checkCepFields(cep) {
    var regexp = /[0-9]{5}\-[0-9]{3}/; 
    var valid = regexp.test(cep);
    return valid;
}

// Eventos que escondem//apagam campos não usados na alternância entre cadastro/busca
function bloqueiaCadastroBairro(){
  if (checkCepFields($j('#cep_').val())){ 
    $j('#bairro').closest('tr').hide();
    $j('#bairro_bairro').closest('tr').show();
    $j('#zona_localizacao').val('');
    $j('#bairro').val('');
  }
  else
    preenchaCampoCepPrimeiro();
}

function bloqueiaBuscaBairro(){
  if (checkCepFields($j('#cep_').val())){ 
    $j('#bairro_bairro').closest('tr').hide(); 
    $j('#bairro').closest('tr').show(); 
    $j('#bairro').val($j('#bairro').val() ? $j('#bairro').val() :$j('#bairro_bairro').val());
    clearBairroFields();
  }
  else
    preenchaCampoCepPrimeiro();
}

function permiteEditarEndereco(){

  var options = {
    url      : getResourceUrlBuilder.buildUrl('/module/Api/endereco', 'permissao_editar'),
    dataType : 'json',
    data     : {},
    success  : handleGetPermissaoEditar
  };
  getResource(options);
}

var handleGetPermissaoEditar = function(dataResponse) {
  if (dataResponse.permite_editar == 0) {
    $j('#span-busca-logradouro').hide();
    $j('#span-busca-bairro').hide();
  }
}

function bloqueiaCadastroLogradouro(){
  if (checkCepFields($j('#cep_').val())){ 
    $j('#idtlog').closest('tr').hide();   
    $j('#logradouro_logradouro').closest('tr').show();
    $j('#idtlog').val('');
    $j('#logradouro').val('');  
  }else
    preenchaCampoCepPrimeiro();
}

function bloqueiaBuscaLogradouro(){
  if (checkCepFields($j('#cep_').val())){ 
    $j('#logradouro_logradouro').closest('tr').hide();   
    $j('#idtlog').closest('tr').show();  
    $j('#logradouro').val($j('#logradouro').val() ? $j('#logradouro').val() :$j('#logradouro_logradouro').val());
    $j('#logradouro_logradouro').val('');
    $j('#logradouro_id').val('');   
  }else{
    preenchaCampoCepPrimeiro();
  }
}

// Dispara evento para buscar CEP quando o mesmo for preenchido sem utilizar a lupa
$j('#cep_').keyup(searchCep);
$j('#cep_').change(searchCep);

// Limpa campos logradouro e bairro simpleSearch
function clearLogradouroAndBairroAndDistritoFields(){
  $j('#logradouro_logradouro').val('');
  $j('#logradouro_id').val('');
  $j('#distrito_id').val('');  
  $j('#distrito_distrito').val('');
  clearBairroFields();
}

// Lmpa campos bairro simpleSearch
function clearBairroFields(){
  $j('#bairro_bairro').val('');
  $j('#bairro_id').val('');  
}

// Adiciona links para Informar/Atualizar troca entre cadastro ou busca 
function addLinksEnderecamento(){
  $j('<span>') .html('ou cadastre um novo bairro')
               .attr('id', 'span-busca-bairro')
               .css('color','blue')
               .css('margin-left','5px')
               .css('cursor','pointer')
               .addClass('decorated')
               .appendTo($j('#bairro_bairro').closest('td'));

  $j('<span>').html('ou busque um bairro existente')
              .attr('id', 'span-cad-bairro')
              .css('color','blue')
              .css('margin-left','5px')
              .css('cursor','pointer')
              .addClass('decorated')
              .appendTo($j('#zona_localizacao').closest('td')); 

  $j('<span>').html('ou cadastre um novo logradouro')
              .attr('id', 'span-busca-logradouro')
              .css('color','blue')
              .css('margin-left','5px')
              .css('cursor','pointer')
              .addClass('decorated')
              .appendTo($j('#logradouro_logradouro').closest('td'));

  $j('<span>').html('ou busque logradouro existente')
              .attr('id', 'span-cad-logradouro')
              .css('color','blue')
              .css('margin-left','5px')
              .css('cursor','pointer')
              .addClass('decorated')
              .appendTo($j('#idtlog').closest('td'));      
}

addLinksEnderecamento();

function desativaAutoComplete(){

  $j('#logradouro').attr('autocomplete', 'off');
  $j('#bairro').attr('autocomplete', 'off');
  $j('#cep_').attr('autocomplete', 'off');

}

desativaAutoComplete();

// Dispara evento para alterar entre Cadastro/Busca
$j('#span-busca-bairro').click(bloqueiaBuscaBairro);
$j('#span-cad-bairro').click(bloqueiaCadastroBairro);
$j('#span-busca-logradouro').click(bloqueiaBuscaLogradouro);
$j('#span-cad-logradouro').click(bloqueiaCadastroLogradouro);

// Altera zebrado para não interferir quando for trocado entre cadastro/busca de bairro/logradouro
function alteraZebradoEnderacamento(){
  if ($j('#bairro').closest('td').hasClass('formmdtd'))
    $j('#bairro').closest('tr').find('td').toggleClass('formmdtd formlttd');
  else
    $j('#bairro').closest('tr').find('td').toggleClass('formlttd formmdtd');

  if ($j('#logradouro_logradouro').closest('td').hasClass('formmdtd'))
    $j('#logradouro_logradouro').closest('tr').find('td').toggleClass('formmdtd formlttd');
  else
    $j('#logradouro_logradouro').closest('tr').find('td').toggleClass('formlttd formmdtd');
}

alteraZebradoEnderacamento();

// Correções para apagarem o valor do campo ID quando for deletado o valor do simpleSearch
$j('#municipio_municipio').keyup( function(){
  if ($j('#municipio_municipio').val() == '')
    $j('#municipio_id').val('').trigger('change');
});

$j('#distrito_distrito').keyup( function(){
  if ($j('#distrito_distrito').val() == '')
    $j('#distrito_id').val('').trigger('change');
});

$j('#bairro_bairro').focusout( function(){
  if ($j('#bairro_bairro').val() == '')
    $j('#bairro_id').val('');
});

$j('#logradouro_logradouro').focusout( function(){
  if ($j('#logradouro_logradouro').val() == '')
    $j('#logradouro_id').val('');
});

/* Como os campos SimpleSearchBairro, SimpleSearchLogradouro, SimpleSearchDistrito dependem do valor do municipio_id,
   quando o mesmo for alterado dispara um evento para apagar esses campos dependentes 
   O campo SimpleSearchBairro também depende do distrito_id */ 
$j('#municipio_id').change(clearLogradouroAndBairroAndDistritoFields);
$j('#distrito_id').change(clearBairroFields);

function fixUpPlaceholderEndereco(){
  $j('#municipio_municipio').attr('placeholder' , 'Digite o nome de um munic\u00edpio para buscar');
  $j('#distrito_distrito').attr('placeholder' , 'Digite o nome de um distrito para buscar');
  $j('#bairro_bairro').attr('placeholder' , 'Digite o nome de um bairro para buscar');
  $j('#logradouro_logradouro').attr('placeholder' , 'Digite o nome de um logradouro para buscar');
  $j('#bairro').attr('placeholder' , 'Digite o nome do novo bairro');
  $j('#logradouro').attr('placeholder' , 'Digite o nome do novo logradouro');
}

function validateEndereco(){

  var err = false;

  if (!checkCepFields($j('#cep_').val())){

    $j('#municipio_municipio').addClass('error');
    $j('#municipio_id').addClass('error');
    messageUtils.error('Informe um CEP no formato NNNNN-NNN.'); 
    err = true;   

  }


  if (!$j('#municipio_id').val()){
    $j('#municipio_municipio').addClass('error');
    $j('#municipio_id').addClass('error');
    messageUtils.error('Selecione um município corretamente.');
    err = true;    
  }

  if (!$j('#distrito_id').val()){
    $j('#distrito_distrito').addClass('error');
    $j('#distrito_id').addClass('error');
    messageUtils.error('Selecione um distrito corretamente.');
    err = true;    
  }    

  if ($j('#logradouro_logradouro').closest('tr').is(':visible')){

    if (!$j('#logradouro_id').val()){
      $j('#logradouro_logradouro').addClass('error');
      $j('#logradouro_id').addClass('error');
      messageUtils.error('Selecione um logradouro ou utilize a opção ao lado para cadastrar um novo.');
      err = true;
    }
  }else{
    if (!$j('#logradouro').val()){
      $j('#logradouro').addClass('error');
      messageUtils.error('Digite o nome do logradouro.');
      err = true;
    }
    if (!$j('#idtlog').val()){
      $j('#idtlog').addClass('error');
      messageUtils.error('Selecione o tipo do logradouro.');
      err = true;
    }
  }
  if ($j('#bairro_bairro').closest('tr').is(':visible')){
    if (!$j('#bairro_id').val()){
      $j('#bairro_bairro').addClass('error');
      $j('#bairro_id').addClass('error');
      messageUtils.error('Selecione um bairro ou utilize a opção ao lado para cadastrar um novo.');
      err = true;
    }

  }else{

    if (!$j('#bairro').val()){
      $j('#bairro').addClass('error');
      messageUtils.error('Digite o nome do bairro.');
      err = true;
    }

    if (!$j('#zona_localizacao').val()){
      $j('#zona_localizacao').addClass('error'); 
      messageUtils.error('Selecione a zona de localização.');     
      err = true;
    }
  }  

  return !err;

}

// --------------------------------- FIM SCRIPTS ENDEREÇAMENTO ---------------------------------------- //