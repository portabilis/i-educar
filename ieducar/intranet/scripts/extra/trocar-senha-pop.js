<script type="text/javascript">
  function acao2()
  {
    if ($F('f_senha').length > 7) {
    if ($F('f_senha') == $F('f_senha2')) {
    acao();
  }
    else {
    alert('As senhas devem ser iguais');
  }
  }
    else {
    alert('A sua nova senha deverá conter pelo menos oito caracteres');
  }
  }
</script>
