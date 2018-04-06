$j('#btn_enviar').removeAttr('onclick');
$j('#btn_enviar').on('click', validaServidor);

function validaServidor() {
  var inepServidor = $j('#cod_docente_inep').val();

  if (inepServidor.length > 0 && inepServidor.length != 12) {
    messageUtils.error('O código INEP deve conter 12 dígitos');
    return false;
  }
  
  acao();
}

habilitaComplementacaoPedagogica(1);
habilitaComplementacaoPedagogica(2);
habilitaComplementacaoPedagogica(3);

$j('#situacao_curso_superior_1').on('change', () => habilitaComplementacaoPedagogica(1));
$j('#codigo_curso_superior_1').on('change', () => habilitaComplementacaoPedagogica(1));

$j('#situacao_curso_superior_2').on('change', () => habilitaComplementacaoPedagogica(2));
$j('#codigo_curso_superior_2').on('change', () => habilitaComplementacaoPedagogica(2));

$j('#situacao_curso_superior_3').on('change', () => habilitaComplementacaoPedagogica(3));
$j('#codigo_curso_superior_3').on('change', () => habilitaComplementacaoPedagogica(3));

function habilitaComplementacaoPedagogica(seq) {
  var cursoSuperiorConcluido = $j('#situacao_curso_superior_'+seq).val() == 1;
  var tecnologo = $j('#codigo_curso_superior_'+seq).val().search('Tecnologo') != -1;
  var bacharelado  = $j('#codigo_curso_superior_'+seq).val().search('Bacharelado') != -1;
  var habilitaCampo = cursoSuperiorConcluido && (tecnologo || bacharelado);

  $j('#formacao_complementacao_pedagogica_'+seq).attr('disabled', !habilitaCampo);
}

//abas

$j('.tablecadastro').children().children('tr:first').children('td:first').append('<div id="tabControl"><ul><li><div id="tab1" class="servidorTab"> <span class="tabText">Dados gerais</span></div></li><li><div id="tab2" class="servidorTab"> <span class="tabText">Dados adicionais</span></div></li></ul></div>');
$j('.tablecadastro').children().children('tr:first').children('td:first').find('b').remove();
$j('#tab1').addClass('servidorTab-active').removeClass('servidorTab');

// Adiciona um ID à linha que termina o formulário para parar de esconder os campos
$j('.tableDetalheLinhaSeparador').closest('tr').attr('id','stop');

// Pega o número dessa linha
linha_inicial_escolaridade = $j('#tr_ref_idesco').index()-1;

// hide nos campos das outras abas (deixando só os campos da primeira aba)
$j('.tablecadastro >tbody  > tr').each(function(index, row) {
  if (index>=linha_inicial_escolaridade - 1){
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

      $j('.servidorTab-active').toggleClass('servidorTab-active servidorTab');
      $j('#tab1').toggleClass('servidorTab servidorTab-active')
      $j('.tablecadastro >tbody  > tr').each(function(index, row) {
        if (index>=linha_inicial_escolaridade -1){
          if (row.id!='stop')
            row.hide();    
          else
            return false;
        }else{
          if ($j('#cod_servidor').val() != '' || $j.inArray(row.id, ['tr_deficiencias', 'tr_cod_docente_inep']) == -1)
            row.show();
        }
      });        
    }
  );  

  // Adicionais
  $j('#tab2').click( 
    function(){
      $j('.servidorTab-active').toggleClass('servidorTab-active servidorTab');
      $j('#tab2').toggleClass('servidorTab servidorTab-active')
      $j('.tablecadastro >tbody  > tr').each(function(index, row) {
        if (row.id!='stop'){
          if (index>=linha_inicial_escolaridade -1){
            if ((index - linha_inicial_escolaridade + 1) % 2 == 0){
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
  $j('.tablecadastro >tbody  > tr').each(function(index, row) {
    if (index>=linha_inicial_escolaridade){
      $j('#'+row.id).find('input:checked').val('on');
    }
  });
});