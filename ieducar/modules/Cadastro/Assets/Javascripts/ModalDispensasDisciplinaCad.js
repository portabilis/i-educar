var submitButton = $j('#btn_enviar');
submitButton.removeAttr('onclick');

submitButton.click(function(){
  getDispensaDisciplinaComNotas();
});

$j( 'body' ).append(modalHtml());
$j('#notas_vinculadas').find(':input').css('display', 'block');

$j("#notas_vinculadas").dialog({
  autoOpen: false,
  closeOnEscape: false,
  draggable: false,
  width: 560,
  modal: true,
  resizable: false,
  title: 'Aviso!',
  buttons: {
    "Salvar" : function () {
      acao();
    },
    "Cancelar": function () {
      $j(this).dialog("close");
    }
  },
  close: function () {

  },
  hide: {
    effect: "clip",
    duration: 500
  },
  show: {
    effect: "clip",
    duration: 500
  }
});

function modalOpen() {
  $j("#notas_vinculadas").dialog("open");
}

function modalHtml() {
  return `<div id="notas_vinculadas">
                <p>Todas as notas desse aluno para essa(s) disciplina(s) e etapa(s) serão removidas. Deseja continuar?</p>
            </div>`;
}

function getDispensaDisciplinaComNotas() {
  let etapas  = [];
  $j('input[name^="etapa["]').each(function(id, val) {
    let ischecked = $j('#'+val.id).is(':checked');
    if (ischecked) {
      etapas.push(val.value)
    }
  });

  let matricula_id = $j('#ref_cod_matricula').val();
  let disciplina_id = $j('#componentecurricular').val();
  if ($j('#modo_edicao').val() == 1 ) {
    disciplina_id = $j('#ref_cod_disciplina').val();
  }

  var url = getResourceUrlBuilder.buildUrl('/module/Api/DispensaDisciplinaPorEtapa',
    'existe-nota',
    {
      ref_cod_matricula : matricula_id,
      componentecurricular : disciplina_id,
      etapas : etapas
    }
  );

  var options = {
    url      : url,
    dataType : 'json',
    success  : function (response) {
      if (response.existe_nota) {
        modalOpen();
      } else {
        acao();
      }
    }
  };
  getResources(options);
}
