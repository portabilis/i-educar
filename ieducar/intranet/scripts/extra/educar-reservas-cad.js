

  function pesquisa_obra()
  {
    var campoBiblioteca = document.getElementById('cod_biblioteca').value;
    pesquisa_valores_popless('educar_pesquisa_obra_lst.php?campo1=ref_cod_acervo&campo2=titulo_obra&campo3='+campoBiblioteca)
  }


