function fixupTabelaMatriculas() {
  var $parentTd = $j('.botaolistagem[value=" Voltar "]').closest('tr').next().children().first();
      $parentTd.empty().removeAttr('bgcolor').removeAttr('style');

  $j('<p>').html(stringUtils.toUtf8('<strong>Matrículas:</strong>')).appendTo($parentTd);

  var $table = $j('<table>').attr('id', 'matriculas').addClass('styled horizontal-expand').hide();
  var $tr    = $j('<tr>');

  $j('<th>').html('').appendTo($tr);
  $j('<th>').html('Ano').appendTo($tr);
  $j('<th>').html(stringUtils.toUtf8('Situação')).appendTo($tr);
  $j('<th>').html('Turma').appendTo($tr);
  $j('<th>').html('\u00daltima enturma\u00e7\u00e3o').appendTo($tr);
  $j('<th>').html(stringUtils.toUtf8('Série')).appendTo($tr);
  $j('<th>').html('Curso').appendTo($tr);
  $j('<th>').html('Escola').appendTo($tr);
  $j('<th>').html('Entrada').appendTo($tr);
  $j('<th>').html(stringUtils.toUtf8('Saída')).appendTo($tr);

  $tr.appendTo($table);
  $table.appendTo($parentTd);
}

fixupTabelaMatriculas();


// api client

var handleGetMatriculas = function(dataResponse) {
  try{
    handleMessages(dataResponse.msgs);


    var $matriculasTable      = $j('#matriculas');
    var transferenciaEmAberto = false;

    $j.each(dataResponse.matriculas, function(index, matricula) {
      var $tr = $j('<tr>');

      if (matricula.user_can_access) {
        var linkToMatricula = $j('<a>').attr('href', 'educar_matricula_det.php?cod_matricula=' + matricula.id)
                                       .html('Visualizar')
                                       .addClass('decorated');

      }
      else
        var linkToMatricula = '';

      if (!matricula.transferencia_em_aberto && matricula.situacao == 'Em andamento')
        matricula.data_saida = '';      

      $j('<td>').html(linkToMatricula).appendTo($tr).addClass('center');
      $j('<td>').html(matricula.ano).appendTo($tr);
      $j('<td>').html(matricula.situacao).appendTo($tr);
      $j('<td>').html(matricula.turma_nome).appendTo($tr);
      $j('<td>').html(matricula.ultima_enturmacao).appendTo($tr);
      $j('<td>').html(matricula.serie_nome).appendTo($tr);
      $j('<td>').html(matricula.curso_nome).appendTo($tr);
      $j('<td>').html(matricula.escola_nome).appendTo($tr);
      $j('<td>').html(matricula.data_entrada).appendTo($tr);
      $j('<td>').html(matricula.data_saida).appendTo($tr);

      if (matricula.transferencia_em_aberto) {
        transferenciaEmAberto = true;
        $tr.addClass('notice');
      }

      $tr.appendTo($matriculasTable);
    });


    if(dataResponse.matriculas.length < 1) {
      var $p = $j('<p>').html(stringUtils.toUtf8('Aluno sem matrículas, ')).addClass('notice simple-block');

      $j('<a>').attr('href', 'educar_matricula_cad.php?ref_cod_aluno=' + $j('#aluno_id').val())
               .html('matricular aluno.')
               .addClass('decorated')
               .appendTo($p);

      $p.appendTo($matriculasTable.parent());
    }
    else if (transferenciaEmAberto) {
      var $p = $j('<p>').html(stringUtils.toUtf8('* Matrícula com solicitação de transferência interna em aberto, '))
                        .addClass('notice simple-block');

      $j('<a>').attr('href', 'educar_matricula_cad.php?ref_cod_aluno=' + $j('#aluno_id').val())
               .html('matricular aluno.')
               .addClass('decorated')
               .appendTo($p);

      $p.appendTo($matriculasTable.parent());
    }

    $matriculasTable.fadeIn('slow');
    $j('body,html').animate({scrollTop: $j('#matriculas').offset().top }, 900);

    $matriculasTable.find('tr:even').addClass('even');
  }
  catch(error) {
    alert('Erro ao carregar matriculas, detalhes:\n\n' + error);

    safeLog('Error details:');
    safeLog(error);

    safeLog('dataResponse details:');
    safeLog(dataResponse);

    throw error;
  }
}

var getMatriculas = function() {
  var data = {
    aluno_id : $j('#aluno_id').val()
  };

  var options = {
    url      : getResourceUrlBuilder.buildUrl('/module/Api/aluno', 'matriculas'),
    dataType : 'json',
    data     : data,
    success  : handleGetMatriculas
  };

  getResource(options);
}

$j('.tableDetalheLinhaSeparador').closest('tr').attr('id','stop');

// Verifica se possui ficha médica, verificando se existe o primeiro campo
var possui_ficha_medica = $j('#fmedica').length>0;

var possui_uniforme_escolar = $j('#funiforme').length>0;

var possui_moradia = $j('#fmoradia').length>0;

// Adiciona abas na página
$j('td .formdktd').append('<div id="tabControl"><ul><li><div id="tab1" class="alunoTab2"> <span class="tabText">Dados pessoais</span></div></li><li><div id="tab2" class="alunoTab2"> <span class="tabText">Ficha m\u00e9dica</span></div></li><li><div id="tab3" class="alunoTab2"> <span class="tabText">Uniforme escolar</span></div></li><li><div id="tab4" class="alunoTab2"> <span class="tabText">Moradia</span></div></li></ul></div>');
$j('td .formdktd b').remove();
$j('#tab1').addClass('alunoTab-active2').removeClass('alunoTab2');
var linha_inicial_fmedica = 0;

if(possui_ficha_medica){
  // Atribui um id a linha, para identificar até onde/a partir de onde esconder os campos
  $j('#fmedica').closest('tr').attr('id','tfmedica');

  // Pega o número dessa linha
  linha_inicial_fmedica = $j('#tfmedica').index();

  // hide nos campos das outras abas (deixando só os campos da primeira aba)
  $j('.tableDetalhe >tbody  > tr').each(function(index, row) {
    if (index>=linha_inicial_fmedica){
      if (row.id!='stop')
        row.hide();    
      else
        return false;
    }
  });
}

if(possui_uniforme_escolar){
  // Atribui um id a linha, para identificar até onde/a partir de onde esconder os campos
  $j('#funiforme').closest('tr').attr('id','tfuniforme');

  // Pega o número dessa linha
  linha_inicial_funiforme = $j('#tfuniforme').index();

  // hide nos campos das outras abas (deixando só os campos da primeira aba)
  $j('.tableDetalhe >tbody  > tr').each(function(index, row) {
    if (index>=linha_inicial_funiforme){
      if (row.id!='stop')
        row.hide();    
      else
        return false;
    }
  });
}

if(possui_moradia){
  // Atribui um id a linha, para identificar até onde/a partir de onde esconder os campos
  $j('#fmoradia').closest('tr').attr('id','tfmoradia');

  // Pega o número dessa linha
  linha_inicial_fmoradia = $j('#tfmoradia').index();

  // hide nos campos das outras abas (deixando só os campos da primeira aba)
  $j('.tableDetalhe >tbody  > tr').each(function(index, row) {
    if (index>=linha_inicial_fmoradia){
      if (row.id!='stop')
        row.hide();    
      else
        return false;
    }
  });
}


// when page is ready
$j(document).ready(function() {

  // on click das abas

  // DADOS PESSOAIS
    $j('#tab1').click( 
      function(){

        $j('.alunoTab-active2').toggleClass('alunoTab-active2 alunoTab2');
        $j('#tab1').toggleClass('alunoTab2 alunoTab-active2')
        $j('.tableDetalhe >tbody  > tr').each(function(index, row) {
          if (index>=linha_inicial_fmedica){
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

    // FICHA MÉDICA
    $j('#tab2').click( 
      function(){
        if (possui_ficha_medica){
          $j('.alunoTab-active2').toggleClass('alunoTab-active2 alunoTab2');
          $j('#tab2').toggleClass('alunoTab2 alunoTab-active2')
          $j('.tableDetalhe >tbody  > tr').each(function(index, row) {
            if (row.id!='stop'){
              if (index>=linha_inicial_fmedica && index<linha_inicial_funiforme){
                row.show();
              }else if (index>0){
                row.hide();
              }
            }else
              return false;
          });
        }else
          alert('Dados da ficha m\u00e9dica n\u00e3o foram adicionados ainda. \nVoc\u00ea pode adicion\u00e1-los clicando em editar.');
      
      });

      // FICHA MÉDICA
      $j('#tab3').click( 
        function(){
          if (possui_uniforme_escolar){
            $j('.alunoTab-active2').toggleClass('alunoTab-active2 alunoTab2');
            $j('#tab3').toggleClass('alunoTab2 alunoTab-active2')
            $j('.tableDetalhe >tbody  > tr').each(function(index, row) {
              if (row.id!='stop'){
                if (index>=linha_inicial_funiforme && index<linha_inicial_fmoradia){
                  row.show();
                }else if (index>0){
                  row.hide();
                }
              }else
                return false;
            });
          }else
            alert('Dados do uniforme escolar n\u00e3o foram adicionados ainda. \nVoc\u00ea pode adicion\u00e1-los clicando em editar.');
        
        });          
      // FICHA MÉDICA
      $j('#tab4').click( 
        function(){
          if (possui_moradia){
            $j('.alunoTab-active2').toggleClass('alunoTab-active2 alunoTab2');
            $j('#tab4').toggleClass('alunoTab2 alunoTab-active2')
            $j('.tableDetalhe >tbody  > tr').each(function(index, row) {
              if (row.id!='stop'){
                if (index>=linha_inicial_fmoradia){
                  row.show();
                }else if (index>0){
                  row.hide();
                }
              }else
                return false;
            });
          }else
            alert('Dados da moradia n\u00e3o foram adicionados ainda. \nVoc\u00ea pode adicion\u00e1-los clicando em editar.');
        
        });    

  getMatriculas();

}); // ready