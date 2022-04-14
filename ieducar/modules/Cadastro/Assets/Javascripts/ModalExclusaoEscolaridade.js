$j( 'body' ).append(modalHtml());
$j('#servidores_vinculados').find(':input').css('display', 'block');

$j("#servidores_vinculados").dialog({
  autoOpen: false,
  closeOnEscape: false,
  draggable: false,
  width: 560,
  modal: true,
  resizable: false,
  title: 'Aviso!',
  buttons: {
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

function modalOpen(){
  $j("#servidores_vinculados").dialog("open");
}

function modalHtml() {
  let idesco =  $j('#idesco').val();
  link =  "/intranet/educar_servidor_lst.php?idesco=" + idesco;
  return `<div id="servidores_vinculados">
                <p>Esta escolaridade está vinculada à servidores</p>
                <a href=`+ link +` > Clique aqui para ver mais </a>
            </div>`;
}
