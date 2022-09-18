$j('body').append(htmlFormModal());
$j('#modal_enviar_mensagem').find(':input').css('display', 'block');

var idLastLineUsed = null;
var typeValidation = null;
var receptor_user_id = null;
var urlExistNotification = null;

$j("#modal_enviar_mensagem").dialog({
  autoOpen: false,
  height: 'auto',
  width: 'auto',
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

function modalOpen(thisElement, registroId, type, user_id, url =null) {
  idLastLineUsed = registroId;
  typeValidation = type;
  receptor_user_id = user_id;
  urlExistNotification = url;
  fillInputs();
  $j("#modal_enviar_mensagem").dialog("open");
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
                <div class="row">
                    <div class="right">
                        <p><b>Você:</b></p>
                        <div>
                            <p>Testee</p>
                        </div>
                    </div>
                    <div class="right">
                        <p><b>Você:</b></p>
                        <div>
                            <p>Testee</p>
                        </div>
                    </div>
                    <div class="left">
                        <p><b>Professor</b></p>
                        <div>
                            <p>msg prof</p>
                        </div>
                    </div>
                </div>

               <div class="row">
                <form>
                  <label for="mensagem">Mensagem</label>
                  <textarea name="novaMensagem" id="novaMensagem" rows="3" cols="50" style="resize: none;"></textarea>
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
    url: "http://" + window.location.host + complementoUrlNotification
  };

  var options = {
    url: postResourceUrlBuilder.buildUrl('/module/Api/ValidacaoEnviarMensagem', 'enviar-mensagem', {}),
    dataType: 'json',
    data: data,
    success: function (dataResponse) {
      console.log(dataResponse)
      console.log(typeof dataResponse)

      if (dataResponse) {
        $j("#dialog-form-pessoa-parent").dialog('close');
        messageUtils.success('Mensagem enviada com sucesso!');

       delay(1000).then(() => urlHelper("http://" + window.location.host + complementoUrlRedirect, '_self'));

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
