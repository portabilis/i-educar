// Abas
$j('td .formdktd').append(`
<div id="tabControl">
  <ul>
    <li>
      <div id="tab1" class="instituicaoTab">
        <span class="tabText">Dados gerais</span>
      </div>
    </li>
    <li>
      <div id="tab2" class="instituicaoTab">
        <span class="tabText">Parâmetros</span>
      </div>
    </li>
  </ul>
</div>`);
$j('td .formdktd b').remove();
$j('.tablecadastro td .formdktd div').remove();
$j('#tab1').addClass('instituicaoTab-active').removeClass('instituicaoTab');

// Atribui um id a linha, para identificar até onde/a partir de onde esconder os campos
$j('#orgao_regional').closest('tr').attr('id','tr_orgao_regional');

// Adiciona um ID à linha que termina o formulário para parar de esconder os campos
$j('.tableDetalheLinhaSeparador').closest('tr').attr('id','stop');

// Pega o número dessa linha
linha_inicial_tipo = $j('#tr_orgao_regional').index();

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

// Aba dados gerais
$j('#tab1').click(
function(){
  $j('.instituicaoTab-active').toggleClass('instituicaoTab-active instituicaoTab');
  $j('#tab1').toggleClass('instituicaoTab instituicaoTab-active')
  $j('.tablecadastro >tbody  > tr').each(function(index, row) {
    if (index>=linha_inicial_tipo){
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

// Aba parametros
$j('#tab2').click(
function(){
  $j('.instituicaoTab-active').toggleClass('instituicaoTab-active instituicaoTab');
  $j('#tab2').toggleClass('instituicaoTab instituicaoTab-active')
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
  $j('#controlar_espaco_utilizacao_aluno').click(onControlarEspacoUtilizadoClick);

  if (!$j('#controlar_espaco_utilizacao_aluno').prop('checked')) {
    $j('#percentagem_maxima_ocupacao_salas').closest('tr').hide();
    $j('#quantidade_alunos_metro_quadrado').closest('tr').hide();
  }
});

// fix checkboxs
$j('.tablecadastro >tbody  > tr').each(function(index, row) {
  if (index>=linha_inicial_tipo){
    $j('#'+row.id).find('input:checked').val('on');
  }
});

function onControlarEspacoUtilizadoClick() {
  if (!$j('#controlar_espaco_utilizacao_aluno').prop('checked')) {
    $j('#percentagem_maxima_ocupacao_salas').val('');
    $j('#quantidade_alunos_metro_quadrado').val('');
    $j('#percentagem_maxima_ocupacao_salas').closest('tr').hide();
    $j('#quantidade_alunos_metro_quadrado').closest('tr').hide();
  } else {
    $j('#percentagem_maxima_ocupacao_salas').closest('tr').show();
    $j('#quantidade_alunos_metro_quadrado').closest('tr').show();
  }
}