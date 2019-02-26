$j('#btn_enviar').removeAttr('onclick');
addOnConfirm();

    function fixUpCheckBoxes() {
        $j('input[name^=check_desenturma]').each(function (index, element) {
            element.id = 'check_desenturma[]';
            element.checked = false;
        });
    }

    fixUpCheckBoxes();

    function marcarCheck(idValue) {
        var contaForm = document.formcadastro.elements.length;
        var campo = document.formcadastro;
        var i;
        for (i = 0; i < contaForm; i++) {
            if (campo.elements[i].id == idValue) {
                campo.elements[i].checked = campo.CheckTodos.checked;
            }
        }
    }

    function fixUpCheckBoxesDois() {
        $j('input[name^=ref_cod_matricula]').each(function (index, element) {
            element.id = 'ref_cod_matricula[]';
            element.checked = true;
        });
    }

    fixUpCheckBoxesDois();

    function marcarCheckDois(idValueDois) {
        var contaFormDois = document.formcadastro.elements.length;
        var campoDois = document.formcadastro;
        var i;
        for (i = 0; i < contaFormDois; i++) {
            if (campoDois.elements[i].id == idValueDois) {
                campoDois.elements[i].checked = campoDois.CheckTodosDois.checked;
            }
        }
    }

    function addOnConfirm() {
        $j('#btn_enviar').click(function() {
            confirm();
        });
    }
    function checkedEnturmados() {
        if ($j( 'input[id^="ref_cod_matricula[]"]:checked' ).length > 0) {
            return true;
        }

        return false;
    }

    function checkedDesenturmados() {
        if ($j( 'input[id^="check_desenturma[]"]:checked' ).length > 0) {
            return true;
        }

        return false;
    }

    function msgDialogModal() {

        if (checkedDesenturmados() || checkedEnturmados()){
            return ['Ao salvar a enturmação/desenturmação do(s) aluno(s) nesta turma, o histórico de enturmação do(s) mesmo(s) também será atualizado. Você gostaria de prosseguir com a ação?'];
        } else {
            return ['Nenhuma ação realizada'];
        }
    }

    function confirm() {
        $j('#campo_modal_confirm').dialog({
            title: 'Atenção!',
            resizable: false,
            height: "auto",
            width: 600,
            modal: true,
            buttons: {
              "Confirmar": function() {
                  $j( this ).dialog( "close" );
                  $j('p#msgDialogModal').remove();
                  acao();
              },
              'Cancelar': function() {
                  $j( this ).dialog( "close" );
                  $j('p#msgDialogModal').remove();
              }
            },
            open: function(event, ui) {
                $j(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
            }
        });

        msg = msgDialogModal();
        this.dialogContainer = $j('#campo_modal_confirm');

        for (var i = 0; msg.length > i; i++) {
            this.dialogContainer.after('<p style="margin:16px" id="msgDialogModal">' + msg[i] + '</p>');
        }

    }
