
  function validaValor()
  {
    var valor_pago_bib;
    var valor_pagamento;
    var total_divida;
    var valor_pendente;

    if ( document.getElementById('total_divida') )
    total_divida = document.getElementById('total_divida').value;
    if ( document.getElementById('valor_pago_bib') )
    valor_pago_bib   = document.getElementById('valor_pago_bib').value;
    if ( document.getElementById('valor_pagamento') )
    valor_pagamento  = document.getElementById('valor_pagamento').value;
    if ( document.getElementById('valor_pendente') )
    valor_pendente  = document.getElementById('valor_pendente').value;

    if ( ( total_divida.replace(",", ".") - valor_pago_bib.replace(",", ".") ) < valor_pagamento.replace(",", ".") ) {
    alert( "O valor de pagamento deve ser inferior ou igual ao valor devido na respectiva biblioteca." );
    valor_pagamento  = document.getElementById('valor_pagamento').value = "";
    return;
  }
    else
  {
    document.formcadastro.submit();
  }
  }

