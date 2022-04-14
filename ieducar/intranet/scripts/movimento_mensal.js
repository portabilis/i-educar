(function ($) {
  $(document).ready(function () {
    $("#calendars").chosen();

    showCalendar();

    $j('#modalidade').change(function (){
      showCalendar();
    });

    $j('#ano, #ref_cod_instituicao, #ref_cod_escola, #ref_cod_curso, #ref_cod_serie, #ref_cod_turma').change(function () {
      getCalendars();
    })
  });

  function showCalendar() {
    // Tela de consulta
    if ($j('#modalidade').val() === '5') {
      $('#tr-calendar').show()
      return;
    }

    // Tela de emissão
    if ($j.inArray('3', $j('#modalidade').val()) >= 0) {
      $('#tr-calendar').show()
      return;
    }

    $('#tr-calendar').hide()
  }

  function getCalendars() {
    if (!$('#ano').val()) {
      return;
    }

    let data = {
      ano: $('#ano').val(),
      ref_cod_instituicao: $('#ref_cod_instituicao').val(),
      ref_cod_escola: $('#ref_cod_escola').val(),
      ref_cod_curso: $('#ref_cod_curso').val(),
      ref_cod_serie: $('#ref_cod_serie').val(),
      ref_cod_turma: $('#ref_cod_turma').val(),
      user: $('#user').val(),
    }

    $j.ajax({
      url: '/api/school-class/calendars',
      data: data,
      dataType: 'json',
      success: function (response) {
        let select = $("#calendars");
        select.find('option').remove()

        $.each(response, function(key, value) {
          select.append($('<option>', {
            value: value.start_date + ' ' + value.end_date,
            text: formatDate(value.start_date) + ' - ' + formatDate(value.end_date),
          }));
        });

        select.chosen().trigger('chosen:updated');
      }
    });
  }
})(jQuery);
