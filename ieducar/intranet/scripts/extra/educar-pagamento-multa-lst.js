
  if ( document.getElementById( 'ref_cod_instituicao' ) ) {
  var ref_cod_instituicao = document.getElementById( 'ref_cod_instituicao' );
  ref_cod_instituicao.onchange = function() { getEscola(); getBiblioteca(1); getClienteTipo(); }
}
  if ( document.getElementById( 'ref_cod_escola' ) ) {
  var ref_cod_escola = document.getElementById( 'ref_cod_escola' );
  ref_cod_escola.onchange = function() { getBiblioteca(2); getClienteTipo(); }
}
  if ( document.getElementById( 'ref_cod_biblioteca' ) ) {
  var ref_cod_biblioteca = document.getElementById( 'ref_cod_biblioteca' );
  ref_cod_biblioteca.onchange = function() { getClienteTipo(); }
}

