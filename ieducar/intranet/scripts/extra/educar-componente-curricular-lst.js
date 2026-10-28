$j(function () {
  $j('[data-toggle="tooltip"]').tooltip();

  $j(document).on('click', '.js-excluir-componente', function (event) {
    event.preventDefault();

    let id = $j(this).data('id');
    let nome = $j(this).data('nome');

    $j('<div></div>')
      .text('Deseja realmente excluir o componente ' + nome + '?')
      .dialog({
        title: 'Atenção!',
        modal: true,
        close: function () {
          $j(this).dialog('destroy').remove();
        },
        buttons: [{
          text: 'Sim',
          click: function () {
            $j(this).dialog('destroy').remove();
            excluirComponente(id);
          }
        }, {
          text: 'Não',
          click: function () {
            $j(this).dialog('destroy').remove();
          }
        }]
      });
  });
});

function excluirComponente(id) {
  $j.ajax({
    url: '/api/discipline/' + id,
    type: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': $j('meta[name="csrf-token"]').attr('content')
    },
    success: function () {
      messageUtils.success('Componente curricular excluído com sucesso.');
      location.reload();
    },
    error: function (xhr) {
      messageUtils.error(mensagemErroExclusao(xhr));
    }
  });
}

function mensagemErroExclusao(xhr) {
  if (xhr.status === 403) {
    return 'Você não tem permissão para excluir componentes curriculares.';
  }

  if (xhr.status === 422) {
    try {
      return $j.parseJSON(xhr.responseText).message;
    } catch (e) {
      return 'Não foi possível excluir o componente curricular.';
    }
  }

  return 'Não foi possível excluir o componente curricular.';
}
