//abas

$j('td .formdktd').append('<div id="tabControl"><ul><li><div id="tab1" class="turmaTab"> <span class="tabText">Dados gerais</span></div></li><li><div id="tab2" class="turmaTab"> <span class="tabText">Dados adicionais</span></div></li></ul></div>');
$j('td .formdktd b').remove();
$j('#tab1').addClass('turmaTab-active').removeClass('turmaTab');

// Atribui um id a linha, para identificar até onde/a partir de onde esconder os campos
$j('#tipo_atendimento').closest('tr').attr('id','ttipo_atendimento');

// Adiciona um ID à linha que termina o formulário para parar de esconder os campos
$j('.tableDetalheLinhaSeparador').closest('tr').attr('id','stop');

// Pega o número dessa linha
linha_inicial_tipo = $j('#ttipo_atendimento').index()-1;

// hide nos campos das outras abas (deixando só os campos da primeira aba)
$j('.tablecadastro >tbody  > tr').each(function(index, row) {
  if (index>=linha_inicial_tipo){
    if (row.id!='stop')
      row.hide();    
    else{
      return false;
    }
  }
});

$j(document).ready(function() {

  // on click das abas

  // DADOS GERAIS
  $j('#tab1').click( 
    function(){

      $j('.turmaTab-active').toggleClass('turmaTab-active turmaTab');
      $j('#tab1').toggleClass('turmaTab turmaTab-active')
      $j('.tablecadastro >tbody  > tr').each(function(index, row) {
        if (index>=linha_inicial_tipo-1){
          if (row.id!='stop')
            row.hide();    
          else
            return false;
        }else{
          row.show();
        }
      });        
    }
  );  

  // Adicionais
  $j('#tab2').click( 
    function(){
      $j('.turmaTab-active').toggleClass('turmaTab-active turmaTab');
      $j('#tab2').toggleClass('turmaTab turmaTab-active')
      $j('.tablecadastro >tbody  > tr').each(function(index, row) {
        if (row.id!='stop'){
          if (index>=linha_inicial_tipo){
            if ((index - linha_inicial_tipo) % 2 == 0){
              $j('#'+row.id).find('td').removeClass('formlttd');
              $j('#'+row.id).find('td').addClass('formmdtd');
            }else{
              $j('#'+row.id).find('td').removeClass('formmdtd');
              $j('#'+row.id).find('td').addClass('formlttd');
              
            }

            row.show();
          }else if (index>0){
            row.hide();
          }
        }else
          return false;
      });
    });

  // fix checkboxs
  $j('input:checked').val('on');
});