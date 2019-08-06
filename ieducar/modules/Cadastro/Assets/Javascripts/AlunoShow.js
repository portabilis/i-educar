

function fixupTabelaMatriculas() {
  var $parentTd = $j('.botaolistagem[value=" Voltar "]').closest('tr').next().children().first();
      $parentTd.empty().removeAttr('bgcolor').removeAttr('style');

  $j('<p>').addClass('title-table-matricula').html(stringUtils.toUtf8('<strong>Matrículas:</strong>')).appendTo($parentTd);

  var $table = $j('<table>').attr('id', 'matriculas').addClass('styled horizontal-expand').hide();
  var $tr    = $j('<tr>');

  $j('<th>').html('').appendTo($tr);
  $j('<th>').html('Ano').appendTo($tr);
  $j('<th>').html(stringUtils.toUtf8('Situação')).appendTo($tr);
  $j('<th>').html('Turma').appendTo($tr);
  $j('<th>').html('Enturma\u00e7\u00e3o anterior').appendTo($tr);
  $j('<th>').html(stringUtils.toUtf8('Série')).appendTo($tr);
  $j('<th>').html('Curso').appendTo($tr);
  $j('<th>').html('Escola').appendTo($tr);
  $j('<th>').html('Entrada').appendTo($tr);
  $j('<th>').html(stringUtils.toUtf8('Saída')).appendTo($tr);

  if($j('#can_show_dependencia').val() == 1)
    $j('<th>').html(stringUtils.toUtf8('Dependência')).appendTo($tr);

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

      if (!matricula.transferencia_em_aberto && matricula.situacao=='Cursando')
        matricula.data_saida = '';

      $j('<td>').html(linkToMatricula).appendTo($tr).addClass('center');
      $j('<td>').html(matricula.ano).appendTo($tr);

      if(matricula.user_can_change_situacao){
        var situacoes = [
              {val: 1, text: 'Aprovado'},
              {val: 2, text: 'Retido'},
              {val: 3, text: 'Cursando'},
              {val: 4, text: 'Transferido'},
              {val: 5, text: 'Reclassificado'},
              {val: 6, text: 'Abandono'},
              {val: 12, text: 'Aprovado com dependência'},
              {val: 13, text: 'Aprovado pelo conselho'},
              {val: 14, text: 'Reprovado por faltas'},
              {val: 15, text: 'Falecido'}
        ];

        var sel = $j('<select>')
        $j(situacoes).each(function() {
          sel.append($j("<option>").attr('value',this.val).text(this.text));
        });

        sel.val(matricula.codigo_situacao);
        sel.bind('change', function(){
          onSituacaoChange(matricula.id, $j(this).val());
        });
        sel.appendTo($tr);

      }else{
        $j('<td>').html(matricula.situacao).appendTo($tr);
      }


      $j('<td>').html(matricula.turma_nome).appendTo($tr);
      $j('<td>').html(matricula.ultima_enturmacao).appendTo($tr);
      $j('<td>').html(matricula.serie_nome).appendTo($tr);
      $j('<td>').html(matricula.curso_nome).appendTo($tr);
      $j('<td>').html(matricula.escola_nome).appendTo($tr);

      if(matricula.data_entrada != ""){
        if(matricula.user_can_access && matricula.user_can_change_date){
          $inputDataEntrada = $j('<input>').val(matricula.data_entrada).css('width', '58px').mask("99/99/9999", {placeholder: "__/__/____"});
          $inputDataEntrada.bind('change', function(key){
            onDataEntradaChange(matricula.id, key, $j(this));
          });
          $inputDataEntrada.appendTo($j('<td>').appendTo($tr));
        }else{
          $j('<td>').html(matricula.data_entrada).appendTo($tr);
        }
      }else{
        $j('<td>').html('').appendTo($tr);
      }

      if(matricula.data_saida != ""){
        if(matricula.user_can_access && matricula.user_can_change_date){
          $inputDataSaida = $j('<input>').val(matricula.data_saida).css('width', '58px').mask("99/99/9999", {placeholder: "__/__/____"});
          $inputDataSaida.bind('change', function(key){
            onDataSaidaChange(matricula.id, key, $j(this));
          });
          $inputDataSaida.appendTo($j('<td>').appendTo($tr));
          }else{
            $j('<td>').html(matricula.data_saida).appendTo($tr);
          }
      }else{
        $j('<td>').html('').appendTo($tr);
      }

      if($j('#can_show_dependencia').val() == 1){
        var dependencia = matricula.dependencia ? 'Sim' : stringUtils.toUtf8('Não');
        $j('<td>').html(dependencia).appendTo($tr);
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

function onSituacaoChange(matricula_id, novaSituacao){
  var data = {
    matricula_id  : matricula_id,
    nova_situacao : novaSituacao
  };

  var options = {
      url      : postResourceUrlBuilder.buildUrl('/module/Api/matricula', 'situacao'),
      dataType : 'json',
      data     : data,
      success  : handlePostSituacao
  };
  postResource(options);
}

var handlePostSituacao = function(dataresponse){
  handleMessages(dataresponse.msgs);
}

function onDataEntradaChange(matricula_id, key, campo){
  if(key.keyCode == 13 || key.keyCode == 9 || (typeof key.keyCode == "undefined")){
    var data = {
      matricula_id : matricula_id,
      data_entrada : campo.val()
    };

    var options = {
      url      : postResourceUrlBuilder.buildUrl('/module/Api/matricula', 'data-entrada'),
      dataType : 'json',
      data     : data,
      success  : handlePostDataEntrada
    };
    postResource(options);
  }

}

var handlePostDataEntrada = function(dataresponse){
  handleMessages(dataresponse.msgs);
}

function onDataSaidaChange(matricula_id, key, campo){

  if(key.keyCode == 13 || key.keyCode == 9 || (typeof key.keyCode == "undefined")){
    var data = {
      matricula_id : matricula_id,
      data_saida : campo.val()
    };

    var options = {
      url      : postResourceUrlBuilder.buildUrl('/module/Api/matricula', 'data-saida'),
      dataType : 'json',
      data     : data,
      success  : handlePostDataSaida
    };
    postResource(options);
  }

}

var handlePostDataSaida = function(dataresponse){
  handleMessages(dataresponse.msgs);
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

var participa_projetos = $j('#fprojeto').length>0;

// Adiciona abas na página
$j('td .formdktd').append('<div id="tabControl"><ul><li><div id="tab1" class="alunoTab2"> <span class="tabText">Dados pessoais</span></div></li><li><div id="tab2" class="alunoTab2"> <span class="tabText">Ficha m\u00e9dica</span></div></li><li><div id="tab3" class="alunoTab2"> <span class="tabText">Uniforme escolar</span></div></li><li><div id="tab4" class="alunoTab2"> <span class="tabText">Moradia</span></div></li><li><div id="tab5" class="alunoTab2"> <span class="tabText">Projetos</span></div></li></ul></div>');
$j('td .formdktd b').remove();
$j('#tab1').addClass('alunoTab-active2').removeClass('alunoTab2');
var linha_inicial_fmedica = 0;

if(possui_ficha_medica){
  // Atribui um id a linha, para identificar até onde/a partir de onde esconder os campos
  $j('#fmedica').closest('tr').attr('id','tfmedica');

  // Pega o número dessa linha
  linha_inicial_fmedica = $j('#tfmedica').index() + 1;

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
  linha_inicial_funiforme = $j('#tfuniforme').index() + 1;
  linha_final_funiforme = $j('#ffuniforme').closest('tr').index() + 1;

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
  linha_inicial_fmoradia = $j('#tfmoradia').index() + 1;

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

if(participa_projetos){
  // Atribui um id a linha, para identificar até onde/a partir de onde esconder os campos
  $j('#fprojeto').closest('tr').attr('id','tfprojeto');

  // Pega o número dessa linha
  linha_inicial_fprojeto = $j('#tfprojeto').index() + 1;

  // hide nos campos das outras abas (deixando só os campos da primeira aba)
  $j('.tableDetalhe >tbody  > tr').each(function(index, row) {
    if (index>=linha_inicial_fprojeto){
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
          if (index>= (linha_inicial_fmedica || linha_inicial_funiforme)){
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
              }else if (index>1){
                row.hide();
              }
            }else
              return false;
          });
        }else
          alert('Dados da ficha m\u00e9dica n\u00e3o foram adicionados ainda. \nVoc\u00ea pode adicion\u00e1-los clicando em editar.');

      });

      // Uniforme escolar
      $j('#tab3').click(
        function(){
          if (possui_uniforme_escolar){
            $j('.alunoTab-active2').toggleClass('alunoTab-active2 alunoTab2');
            $j('#tab3').toggleClass('alunoTab2 alunoTab-active2')
            $j('.tableDetalhe >tbody  > tr').each(function(index, row) {
              if (row.id!='stop'){
                if (index>=linha_inicial_funiforme && index<linha_final_funiforme + 1){
                  row.show();
                }else if (index>1){
                  row.hide();
                }
              }else
                return false;
            });
          }else
            alert('Dados do uniforme escolar n\u00e3o foram adicionados ainda. \nVoc\u00ea pode adicion\u00e1-los clicando em distribui\u00e7\u00e3o de uniforme.');

        });
      // Moradia
      $j('#tab4').click(
        function(){
          if (possui_moradia){
            $j('.alunoTab-active2').toggleClass('alunoTab-active2 alunoTab2');
            $j('#tab4').toggleClass('alunoTab2 alunoTab-active2')
            $j('.tableDetalhe >tbody  > tr').each(function(index, row) {
              if (row.id!='stop'){
                if (index>=linha_inicial_fmoradia){
                  row.show();
                }else if (index>1){
                  row.hide();
                }
              }else
                return false;
            });
          }else
            alert('Dados da moradia n\u00e3o foram adicionados ainda. \nVoc\u00ea pode adicion\u00e1-los clicando em editar.');

        });
      // Projetos
      $j('#tab5').click(
        function(){
          if (participa_projetos){
            $j('.alunoTab-active2').toggleClass('alunoTab-active2 alunoTab2');
            $j('#tab5').toggleClass('alunoTab2 alunoTab-active2')
            $j('.tableDetalhe >tbody  > tr').each(function(index, row) {
              if (row.id!='stop'){
                if (index>=linha_inicial_fprojeto){
                  row.show();
                }else if (index>1){
                  row.hide();
                }
              }else
                return false;
            });
          }else
            alert('Aluno n\u00e3o participa de projetos.');

        });

  getMatriculas();

}); // ready