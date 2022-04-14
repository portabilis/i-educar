

  function setOrdem(id)
  {
    document.getElementById('nr_nivel['+(id)+']').value = (id+1);
  }

  tab_add_1.afterAddRow = function() {
  setOrdem(this.id-1);
}

  tab_add_1.afterRemoveRow = function() {
  reordena();
}

  function reordena()
  {
    for(var ct=0;ct < tab_add_1.getId();ct++)
  {
    setOrdem(ct);
  }
  }

