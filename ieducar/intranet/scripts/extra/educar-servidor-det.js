<script type="text/javascript">
  function trocaDisplay(id) {
  var element = document.getElementById(id);
  element.style.display = (element.style.display == 'none') ? 'inline' : 'none';
}

  function popless() {
  var campoServidor = #cod_servidor;
    var campoInstituicao = #ref_cod_instituicao;
    pesquisa_valores_popless('educar_servidor_nivel_cad.php?ref_cod_servidor=' + campoServidor + '&ref_cod_instituicao=' + campoInstituicao, '');
}
</script>
