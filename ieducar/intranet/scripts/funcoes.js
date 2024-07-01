function janela(pagina,TW,TH) {
        window.open(pagina, 'cliente', "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=yes"+",width="+TW+",height="+TH);
}

function FiltraCampo(codigo) {
    var s = "";

	tam = codigo.length;
	for (i = 0; i < tam ; i++) {
		if (codigo.substring(i,i + 1) == "0" ||
           	codigo.substring(i,i + 1) == "1" ||
            codigo.substring(i,i + 1) == "2" ||
            codigo.substring(i,i + 1) == "3" ||
            codigo.substring(i,i + 1) == "4" ||
            codigo.substring(i,i + 1) == "5" ||
            codigo.substring(i,i + 1) == "6" ||
            codigo.substring(i,i + 1) == "7" ||
            codigo.substring(i,i + 1) == "8" ||
            codigo.substring(i,i + 1) == "9"  )
		 		s = s + codigo.substring(i,i + 1);
	}
	return s;
}

function DvCnpjOk(e) {
    var dv = false;

    controle = "";
    s = FiltraCampo(e.value);
    tam = s.length
    if ( tam  == 14 ) {
        dv_cnpj = s.substring(tam-2,tam);
        for ( i = 0; i < 2; i++ ) {
            soma = 0;
            for ( j = 0; j < 12; j++ )
                soma += s.substring(j,j+1)*((11+i-j)%8+2);
            if ( i == 1 ) soma += digito * 2;
            digito = 11 - soma  % 11;
            if ( digito > 9 ) digito = 0;
            controle += digito;
        }
        if ( controle == dv_cnpj )
            dv = true;
     }
     if ( ! dv && tam > 0) {
         mensagem = "           Erro de digitação:\n";
         mensagem+= "          ===============\n\n";
         mensagem+= " O CNPJ: " + e.value + " não existe!!\n";
         mensagem+= " CONTROLE " +controle +"\n";
         alert(mensagem);
     }
     return dv;
}

function DvCpfOk(e) {
    var dv = false;

    controle = "";
    s = FiltraCampo(e.value);
    tam = s.length;
    if ( tam == 11 ) {
        dv_cpf = s.substring(tam-2,tam);
        for ( i = 0; i < 2; i++ ) {
            soma = 0;
            for ( j = 0; j < 9; j++ )
                soma += s.substring(j,j+1)*(10+i-j);
            if ( i == 1 ) soma += digito * 2;
            digito = (soma * 10) % 11;
            if ( digito == 10 ) digito = 0;
            controle += digito;
        }
        if ( controle == dv_cpf )
            dv = true;
    }
     if ( ! dv && tam > 0) {
         mensagem = "           Erro de digitação:\n";
         mensagem+= "          ===============\n\n";
         mensagem+= " O CPF: " + e.value + " não existe!!\n";
         alert(mensagem);
     }
    return dv;
}
