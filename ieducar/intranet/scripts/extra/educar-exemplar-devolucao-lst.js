

  function pesquisa_cliente()
  {
    var campoBiblioteca = document.getElementById('cod_biblioteca').value;
    pesquisa_valores_popless('educar_pesquisa_cliente_lst.php?campo1=ref_cod_cliente&campo2=nm_cliente&ref_cod_biblioteca='+campoBiblioteca)
  }

  function pesquisa_obra()
  {
    var campoBiblioteca = document.getElementById('cod_biblioteca').value;
    pesquisa_valores_popless('educar_pesquisa_obra_lst.php?campo1=ref_cod_acervo&campo2=nm_obra&campo3='+campoBiblioteca)
  }


