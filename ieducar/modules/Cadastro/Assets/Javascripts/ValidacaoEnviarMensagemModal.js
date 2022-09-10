$j('body').append(htmlFormModal());
$j('#modal_enviar_mensagem').find(':input').css('display', 'block');

var idLastLineUsed = null;

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
      if (validateAccessCriteriaId() && validateLinkType()) {
        fillHiddenInputs();
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

function modalOpen(thisElement, registroId) {
  idLastLineUsed = registroId;
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
                  <textarea id="resizable" name="novaMensagem" id="novaMensagem" rows="3" cols="50" style="resize: none;"></textarea>
                </form>
               </div>
            </div>`;
}

