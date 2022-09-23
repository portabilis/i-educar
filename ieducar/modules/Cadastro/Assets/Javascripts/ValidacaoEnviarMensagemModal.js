$j('body').append(htmlFormModal());
$j('#modal_enviar_mensagem').find(':input').css('display', 'block');

var idLastLineUsed = null;
var typeValidation = null;
var receptor_user_id = null;
var urlExistNotification = null;
var idUserLogado = null;
var isProfessor = false;

$j("#modal_enviar_mensagem").dialog({
  autoOpen: false,
  height: 'auto',
  width: '500px',
  minWidth: '500px',
  maxWidth: '500px',
  modal: true,
  resizable: false,
  draggable: false,
  title: 'Enviar Mensagem',
  buttons: {
    "Gravar": function () {
      if (validateMensagem()) {
        addMensagem();
        $j(this).dialog("close");
      }
    },
    "Cancelar": function () {
      $j(this).dialog("close");
    }
  },
  create: function () {
    $j(this)
      .closest(".ui-dialog")
      .find(".ui-button-text:first")
      .addClass("btn-green");
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

function modalOpen(thisElement, registroId, type, user_id, url = null, auth_id, professor = false) {
  idLastLineUsed = registroId;
  typeValidation = type;
  receptor_user_id = user_id;
  urlExistNotification = url;
  idUserLogado = auth_id;
  isProfessor = professor;

  fillMensagens();
  fillInputs();
  $j("#modal_enviar_mensagem").dialog("open");
}

function fillMensagens() {
  var data = {
    registro_id: idLastLineUsed
  };

  var options = {
    url: getResourceUrlBuilder.buildUrl('/module/Api/ValidacaoEnviarMensagem', 'get-mensagens', {}),
    dataType: 'json',
    data: data,
    success: function (dataResponse) {
      let html = '';

      if (dataResponse) {
        for (let i = 0; i < dataResponse.result.length; i++) {
          let classePosicionamento = '';
          let tituloMensagem = '';
          let data = dataResponse.result[i].created_at;

          if (dataResponse.result[i].emissor_user_id == idUserLogado) {
            classePosicionamento = 'right';
            tituloMensagem = 'Você - '+ data;
          } else {
            classePosicionamento = 'left';
            tituloMensagem = (isProfessor ? 'Coordenador - ' + data : 'Professor - ' + data);
          }

          html += `<div class="${classePosicionamento}" style="margin-top: 2rem;">
                        <p><b>${tituloMensagem}</b></p>
                        <div>
                            <p>${dataResponse.result[i].texto}</p>
                        </div>
                    </div>`;
        }

        $j('#box-mensagens').html(html);

      }
    }
  };

  getResources(options);
}

function fillInputs() {
  let novaMensagem = $j('textarea[id^="novaMensagem[' + idLastLineUsed + ']').val();

  $j("#novaMensagem").val(novaMensagem);
}

function htmlFormModal() {
  return `<div id="modal_enviar_mensagem">
                <div class="row">
                    <div class="col-12">
                        <p><b>Mensagens enviadas</b></p>
                    </div>
                </div>
                <div class="row" id="box-mensagens">
                    <div class="text-center">
                        <div>
                            <p>Carregando...</p>
                        </div>
                    </div>
                </div>

               <div class="row">
                <form>
                  <label for="mensagem"><b>Mensagem:</b></label>
                  <textarea name="novaMensagem" id="novaMensagem" rows="3" cols="50" style="resize: none;" maxlength="250"></textarea>
                </form>
               </div>
            </div>`;
}

function validateMensagem() {
  if ($j('#novaMensagem').val() == '') {
    messageUtils.error("A Mensagem é obrigatória");
    return false;
  }

  return true;
}

function addMensagem() {
  let mensagem = $j('#novaMensagem').val();
  let complementoUrlNotification = '/intranet/educar_professores_planejamento_de_aula_cad.php?id=' + idLastLineUsed;
  let complementoUrlRedirect = '/intranet/educar_professores_validacao_planejamento_de_aula_lst.php';

  if (typeValidation == 2) {
    complementoUrlNotification = '/intranet/educar_professores_frequencia_cad.php?id=' + idLastLineUsed;
    complementoUrlRedirect = '/intranet/educar_professores_validacao_registro_de_frequencia_lst.php';
  }

  if (typeValidation == 3) {
    complementoUrlNotification = urlExistNotification;
    complementoUrlRedirect = '/notificacoes';
  }


  var data = {
    mensagem: mensagem,
    registro_id: idLastLineUsed,
    typeValidation: typeValidation,
    receptor_user_id: receptor_user_id,
    url: complementoUrlNotification
  };

  var options = {
    url: postResourceUrlBuilder.buildUrl('/module/Api/ValidacaoEnviarMensagem', 'enviar-mensagem', {}),
    dataType: 'json',
    data: data,
    success: function (dataResponse) {
      if (dataResponse) {
        // $j("#dialog-form-pessoa-parent").dialog('close');
        messageUtils.success('Mensagem enviada com sucesso!');

       // delay(1000).then(() => urlHelper("http://" + window.location.host + complementoUrlRedirect, '_self'));

      } else {
        messageUtils.error("Houve algum erro ao enviar a mensagem");
      }
    }
  };

  postResource(options);
}

function delay(time) {
  return new Promise(resolve => setTimeout(resolve, time));
}

function urlHelper(href, mode) {
  Object.assign(document.createElement('a'), {
    target: mode,
    href: href,
  }).click();
}

