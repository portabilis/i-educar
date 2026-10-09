<?php

use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    /**
     * As telas legadas rodam sem avisos do PHP (o LegacyController zera o
     * error_reporting), e vários campos leem posições que o construtor não
     * preenche.
     */
    $this->renderAsLegacyScreen = function (Closure $render): string {
        $level = error_reporting(0);

        try {
            return $render();
        } finally {
            error_reporting($level);
        }
    };
});

test('show file field', function () {
    $clsCampos = new clsCampos;

    $clsCampos->campoArquivo('file', 'Foto', '/caminho/foto', 40, '<b>Foto do aluno</b>');

    $this->assertEquals(
        $this->getHtmlCodeFromFile('form_scripts.html') . trim($this->getHtmlCodeFromFile('file_field.html')),
        $clsCampos->makeCampos()
    );
});

test('campos de documento hora cor e pesquisa viram inputs com mascara', function () {
    $campos = new clsCampos;

    $campos->campoBoolLista('ativo', 'Ativo', 't', 'Sim', 'Não', 'Indefinido');
    $campos->campoCep('cep', 'CEP', '88000-000', true, '-', 'Só números', true);
    $campos->campoCheckMultiplo('dias', 'Dias', [1 => 'Segunda', 2 => 'Terça'], [2]);
    $campos->campoCnpj('cnpj', 'CNPJ', '00.000.000/0001-00', true);
    $campos->campoCnpjPesq('cnpj_pesq', 'CNPJ pesquisado', '11.111.111/0001-11', 'pesquisa_cnpj.php', 'campos_serializados');
    $campos->campoCpf('cpf', 'CPF', '000.000.000-00', true);
    $campos->campoRA('ra', 'RA', '123.456.789-0', true);
    $campos->campoIdFederal('id_federal', 'CPF ou CNPJ', '22.222.222/0001-22', true, true, 'Documento');
    $campos->campoDataDiaMes('aniversario', 'Aniversário', '01/02');
    $campos->campoHora('hora', 'Hora', '10:30', true, '', '', true, true);
    $campos->campoHoraServidor('carga', 'Carga horária', '40:00', true);
    $campos->campoCor('cor', 'Cor', '#ff0000', ['00ff00', '#0000ff']);
    $campos->campoMonetario('valor', 'Valor', '10,00', 10, 10, true, 'R$', '', 'onChange', true, true, 'Reais');
    $campos->campoMonetario('valor_duplo', 'Valor duplo', '20,00', 10, 10, false, '', '', 'onChange', true, true, 'Centavos', true);
    $campos->campoTexto('apos_duplo', 'Depois do duplo', 'fim');
    $campos->campoEmail('email', 'E-mail', 'ana@escola.br', 30, 50);
    $campos->campoTextoPesquisa('pesquisa', 'Pesquisa', 'Ana', 30, 50, true, 'pesquisa.php', false, false, 'Clique na lupa', '', null, true);
    $campos->campoListaPesq('lista_pesq', 'Lista com lupa', [1 => 'Um'], 1, 'pesquisa_lista.php', '', false, '', 'Depois', null, null, '', false, 'serializados', true);
    $campos->campoListaPesq('lista_div', 'Lista em div', [1 => 'Um'], 1, 'pesquisa_div.php', '', false, '', '', null, null, '', true);
    $campos->setOptionsListaPesquisa('lista_popup', ['Lista em pop-up', '', [1 => 'Um'], 1, '', '', 'pesquisa_popup.php', '', 'campo1', 'campo2']);
    $campos->campoCheck('aceite', 'Aceite', true, 'Aceito');
    $campos->campoTexto('busca', 'Busca', '', 10, 20, false, false, false, '', '', 'buscar()', 'onclick');
    $campos->campoTexto('apelido', 'Apelido', 'Bia', 10, 20, false, false, true);
    $campos->campoTextoPesquisa('pesquisa_livre', 'Pesquisa livre', '', 30, 50, false, 'pesquisa_livre.php', false, false, '', '', 'serializados');
    $campos->campoMemo('observacao', 'Observação', 'Texto', 40, 3, false, '', 0, false, 'contar()', 'onkeyup', true);
    $campos->campoLista('turmas', 'Turmas', ['g1' => 'optgroup:Manhã', 1 => 'Turma A', 'g2' => 'optgroup', 'g3' => 'optgroup:Tarde', 2 => 'Turma B'], 1, '', false, '', '', false, true, 3);
    $campos->erros['email'] = 'E-mail inválido';

    $html = ($this->renderAsLegacyScreen)(fn () => $campos->MakeCampos(start_md: false));

    foreach ([
        '<option id="ativo_" value="">Indefinido</option>',
        'onKeyPress="formataCEP(this, event);"',
        'value="88000-000" size="10" maxlength="9" disabled>Só números',
        '<label><input name="dias[]" type="checkbox" value="2" checked="checked"> Terça</label>',
        'onKeyPress="formataCNPJ(this, event);" class=\'obrigatorio\' type=\'text\' name="cnpj"',
        "pesquisa_valores_popless('pesquisa_cnpj.php?campos=campos_serializados', 'cnpj_pesq')",
        'name="cpf" id="cpf" value="000.000.000-00"',
        'name="ra" id="ra" value="123.456.789-0"',
        'onkeyPress="formataIdFederal(this,event);"',
        'name="aniversario" id="aniversario" value="01/02"',
        'name="hora" id="hora" value="10:30"',
        'name="carga" id="carga" value="40:00"',
        '<option value="#00ff00"></option><option value="#0000ff"></option>',
        'type=\'color\' list=\'cores\' name="cor" id="cor" value="#ff0000"',
        'name="valor" id="valor" value="10,00" size="10" maxlength="10" disabled> Reais',
        'name="valor_duplo" id="valor_duplo" value="20,00" size="10" maxlength="10" disabled> Centavos',
        'name="email" id="email" value="ana@escola.br"',
        '<font color=red>E-mail inválido</font>',
        'name="aceite" id="aceite"  checked > Aceito',
        'onclick="buscar()"',
        'name="apelido" id="apelido" value="Bia"',
        "pesquisa_valores_popless('pesquisa_livre.php?campos=serializados', 'pesquisa_livre')",
        'disabled >Texto</textarea>',
        "multiple='multiple' SIZE='3'",
        '<optgroup label="Manhã"><option id="turmas_1" value="1" selected>Turma A</option></optgroup><optgroup label="Tarde"><option id="turmas_2" value="2">Turma B</option></optgroup>',
        "pesquisa_valores_popless('pesquisa.php', 'pesquisa')",
        "pesquisa_valores_popless('pesquisa_lista.php?campos=serializados', 'lista_pesq')",
        "pesquisa_valores_popless('pesquisa_div.php', 'lista_div')",
        "pesquisa_valores_f('pesquisa_popup.php', 'lista_popup', 'campo1', 'campo2')",
    ] as $fragment) {
        $this->assertStringContainsString($fragment, $html);
    }
});

test('tabela de adicao renderiza cada tipo de campo por linha', function () {
    $campos = new clsCampos;

    $campos->campoTabelaInicio('itens', 'Itens', ['Texto', 'Valor', 'Hora', 'Lista', 'Rótulo', 'Marcado', 'CNPJ', 'CPF', 'RA', 'Documento', 'Data', 'Dia e mês', 'Pesquisa', 'CEP', 'Servidor', 'Cor'], [
        ['primeiro', '1,00', '08:00:00', 2, 'rótulo', true, '00.000.000/0001-00', '000.000.000-00', '123', '1', '01/02/2030', '01/02', 'Ana', '88000-000', '40:00', '#fff', 7],
    ], '80%', [[[1 => 'Um da linha', 2 => 'Dois da linha']]]);
    $campos->campoTexto('texto', 'Texto', '', 10, 20, true, false, false, '', '', 'alert(1)', 'onclick', true);
    $campos->campoMonetario('valor', 'Valor', '', 10, 10, false, '', '', 'onChange', true);
    $campos->campoHora('hora', 'Hora', '');
    $campos->campoLista('lista', 'Lista', [1 => 'Um'], 2, '', false, '', '', false, true, 2);
    $campos->campoRotulo('rotulo', 'Rótulo');
    $campos->campoCheck('marcado', 'Marcado', '', 'Sim', false, 'marcar()', true, 'dica');
    $campos->campoCnpj('cnpj', 'CNPJ', '');
    $campos->campoCpf('cpf', 'CPF', '', false, false, true, 'validar()');
    $campos->campoRA('ra', 'RA', '');
    $campos->campoIdFederal('documento', 'Documento', '', false, true, 'Documento');
    $campos->campoData('data', 'Data', '', false, '', false, '', true);
    $campos->campoDataDiaMes('dia_mes', 'Dia e mês', '');
    $campos->campoTextoPesquisa('pesquisa', 'Pesquisa', '', 10, 20, false, 'pesquisa.php', false, false, 'Lupa', '', 'serializados', true);
    $campos->campoCep('cep', 'CEP', '', false, '-', 'Só números', true);
    $campos->campoHoraServidor('servidor', 'Servidor', '');
    $campos->campoCor('cor', 'Cor', '');
    $campos->campoOculto('id', '');
    $campos->campoTabelaFim();

    $campos->campoTabelaInicio('pesquisas', '', ['Pesquisa', 'CEP'], [['Bia', '89000-000']]);
    $campos->campoTextoPesquisa('pesquisa_simples', 'Pesquisa', '', 10, 20, false, 'pesquisa_simples.php');
    $campos->campoCep('cep_simples', 'CEP', '');
    $campos->campoOculto('id_pesquisa', '');
    $campos->campoTabelaFim();

    $html = ($this->renderAsLegacyScreen)(fn () => $campos->MakeCampos());

    foreach ([
        '<td colspan=\'17\'>Itens</td>',
        'name="texto[0]" id="texto[0]" value="primeiro" size="10" maxlength="20"  onclick="alert(1)" disabled=\'disabled\'>',
        'name="valor[0]" id="valor[0]" value="1,00" size="10" maxlength="10" disabled=\'disabled\'>',
        "name='lista[0]' id='lista[0]'   multiple='multiple' SIZE='2' >",
        'name="data[0]" id="data[0]" value="01/02/2030" size="9" maxlength="10" disabled=\'disabled\'>',
        'name="hora[0]" id="hora[0]" value="08:00"',
        '<option id="lista[0]_2" value="2" selected>Dois da linha</option>',
        '<span class="form"> rótulo</span>',
        'name="marcado[0]" id="marcado[0]" onclick="marcar()" checked disabled=\'disabled\'> Sim',
        'onKeyPress="formataCNPJ(this, event);" class=\'geral\' type=\'text\' name="cnpj[0]"',
        'onChange="validar()"onKeyPress="formataCPF(this, event);"',
        'name="ra[0]" id="ra[0]" value="123"',
        'name="documento[0]" id="documento[0]" value="1" size="20" maxlength="18" disabled=\'disabled\'>',
        'name="data[0]" id="data[0]" value="01/02/2030"',
        'name="dia_mes[0]" id="dia_mes[0]" value="01/02"',
        "pesquisa_valores_popless('pesquisa.php?campos=serializados', 'pesquisa[0]')",
        'name="cep[0]" id="cep[0]" value="88000-000" size="10" maxlength="9" disabled=\'disabled\'>Só números',
        "pesquisa_valores_f('pesquisa_simples.php', 'pesquisa_simples[0]')",
        'name="cep_simples[0]" id="cep_simples[0]" value="89000-000" size="10" maxlength="9" >',
        "<input name='id[0]' id='id[0]' type='hidden' value='7'>",
    ] as $fragment) {
        $this->assertStringContainsString($fragment, $html);
    }
});

test('campos inline ficam na mesma linha', function () {
    $campos = new clsCampos;

    $campos->campoEmail('email', 'E-mail', 'ana@escola.br', 30, 50, false, false, true, '', '', 'validar()');
    $campos->campoTexto('nome', 'Nome', 'Ana');
    $campos->campoCheck('ativo', 'Ativo', true, 'Sim', true);
    $campos->campoMonetario('salario', 'Salário', '1,00', 10, 10, false, 'R$', '', 'onChange', false, true, '', true);

    $html = ($this->renderAsLegacyScreen)(fn () => $campos->MakeCampos(todos_inline: true));

    $this->assertStringContainsString('<input type=\'checkbox\' name="ativo" checked> Sim', $html);
    $this->assertStringContainsString('name="salario" id="salario" value="1,00" size="10" maxlength="10" > R$', $html);

    $this->assertStringContainsString('name="email" id="email" value="ana@escola.br" size="30" maxlength="50" onKeyUp="validar()">', $html);
    $this->assertStringNotContainsString("<tr id='tr_nome'", $html);
});
