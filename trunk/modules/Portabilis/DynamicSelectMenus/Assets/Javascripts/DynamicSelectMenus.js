function buildId(id) {
  if (id.length > 0)
    return id[0] == '#' ? id : '#' + id;
  return id;
}


function updateSelect($targetElement, options) {
  $targetElement.children('[value^=""]').remove();

  $j(options).each(function(index, value){
    $j('<option />').attr('id', value.id).attr('checked', value.checked).html(value.value).appendTo($targetElement);
  });

  if (options.length > 0)
    enableElement($targetElement);
  else
    $targetElement.children(':first').html('Sem op&ccedil;&otilde;es');

}


function disableElement($targetElement) {
  $($targetElement).attr('disabled', 'disabled');
}


function enableElement($targetElement) {
  $($targetElement).removeAttr('disabled');
}
