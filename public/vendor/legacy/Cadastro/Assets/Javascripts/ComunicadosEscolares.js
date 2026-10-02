$j(document).ready(function () {
  initSummernote('descricao');
  function initSummernote(sElement) {

    const element = $j(`#${sElement}`);
    let value = element.val();

    const settings = {
      height: 146,                 // set editor height
      minHeight: null,             // set minimum height of editor
      maxHeight: null,             // set maximum height of editor
      focus: false,                 // set focus to editable area after initializing summernote
      lang: 'pt-BR',
      toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['para', ['ul', 'ol']],
        ['insert', ['link']],
      ],
      fontNames: ['Arial'],
      callbacks: {
        onBlur: function(contents) {

          const elementToFind = contents.relatedTarget;
          const attrClass = $j(elementToFind).attr('class');

          if(!attrClass || !attrClass.include('note-btn')) {

            const currentlyValue = element.val();

            if (value != currentlyValue) {

              if (currentlyValue.replace(/<\/?[^>]+(>|$)/g, "") === '') {

                element.val(currentlyValue.replace(/<\/?[^>]+(>|$)/g, ""));

                value = currentlyValue.replace(/<\/?[^>]+(>|$)/g, "");

              } else {

                value = currentlyValue;
              }

              changeParecer($j(this));

            }
          }
        },
        onPaste : function (event) {
          event.preventDefault();
          let text = null;
          if (window.clipboardData){
            text = window.clipboardData.getData("Text");

          } else if (event.originalEvent && event.originalEvent.clipboardData){
            text = event.originalEvent.clipboardData.getData("Text");
          }

          element.summernote('insertText', text);

          element.val(text);
        }
      }
    };
    element.summernote(settings);
  }
});

$j(document).ready(function () {
  const $instituicao = $j('#ref_cod_instituicao');
  const $escolas = $j('#escolas');

  $escolas.attr('name', 'escolas[]');
  $escolas.chosen({
    no_results_text: 'Sem resultados para ',
    placeholder_text_multiple: 'Selecione as escolas',
    search_contains: true,
  });

  const selecionadas = decodeURIComponent($j('#escolas_selecionadas').val()).split(',');
  $escolas.find('option').each(function () {
    this.selected = selecionadas.indexOf(this.value) !== -1;
  });
  $escolas.trigger('chosen:updated');

  $escolas.change(function () {
    if (($escolas.val() || []).indexOf('all') === -1) {
      return;
    }
    $escolas.find('option').each(function () {
      this.selected = this.value !== 'all';
    });
    $escolas.trigger('chosen:updated');
  });

  $instituicao.change(function () {
    getResource({
      url: getResourceUrlBuilder.buildUrl('/module/Api/escola', 'escolas-para-selecao', {
        instituicao: $instituicao.val(),
      }),
      dataType: 'json',
      data: {},
      success: function (response) {
        let options = '<option value="all">Todas as escolas</option>';
        $j.each(response['options'], function (id, nome) {
          options += '<option value="' + id.replace(/^__/, '') + '">' + nome + '</option>';
        });
        $escolas.empty().append(options).trigger('chosen:updated');
      },
    });
  });
});
