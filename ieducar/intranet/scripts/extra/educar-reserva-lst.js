

  function pesquisa_cliente()
  {
    pesquisa_valores_popless('educar_pesquisa_cliente_lst.php?campo1=ref_cod_cliente&campo2=nm_cliente')
  }

  function pesquisa_obra()
  {
    var campoBiblioteca = document.getElementById('cod_biblioteca').value;
    pesquisa_valores_popless('educar_pesquisa_obra_lst.php?campo1=ref_cod_exemplar&campo2=nm_exemplar&campo3='+campoBiblioteca)
  }


